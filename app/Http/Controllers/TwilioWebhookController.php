<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\VoiceResponse;

class TwilioWebhookController extends Controller
{
    protected TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    /**
     * Handle incoming call webhook
     */
    public function handleIncomingCall(Request $request)
    {
        try {
            Log::info('Incoming call webhook', $request->all());

            $communication = $this->twilioService->handleIncomingCall($request->all());

            // You could add logic here to route the call based on business rules
            // For now, we'll just log it and let it ring

            return $this->generateCallTwiml($request);

        } catch (\Exception $e) {
            Log::error('Failed to handle incoming call', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Generate TwiML for call handling
     */
    public function generateCallTwiml(Request $request)
    {
        $response = new VoiceResponse();

        // Simple TwiML - could be made more sophisticated
        $response->say('Thank you for calling. Please hold while we connect you to an available representative.');
        
        // Add a pause
        $response->pause(['length' => 2]);
        
        // Dial could be configured to route to specific agents or departments
        $dial = $response->dial();
        $dial->number(config('services.twilio.forward_number', '+1234567890'));

        // If no answer, leave a voicemail prompt
        $response->say('We are currently unavailable. Please leave a message after the tone.');
        $response->record([
            'timeout' => 10,
            'recordingStatusCallback' => route('twilio.recording.status'),
        ]);

        return response($response, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Handle call status updates
     */
    public function handleCallStatus(Request $request)
    {
        try {
            Log::info('Call status webhook', $request->all());

            $this->twilioService->updateCallStatus($request->all());

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle call status', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Handle incoming SMS webhook
     */
    public function handleIncomingSms(Request $request)
    {
        try {
            Log::info('Incoming SMS webhook', $request->all());

            $communication = $this->twilioService->handleIncomingSms($request->all());

            // Optional: Auto-respond to SMS messages
            // This could be made configurable
            if ($this->shouldAutoRespond($request)) {
                $autoResponse = $this->generateAutoResponse($communication);
                if ($autoResponse) {
                    // Send auto-response (would need to be implemented)
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle incoming SMS', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Handle SMS status updates
     */
    public function handleSmsStatus(Request $request)
    {
        try {
            Log::info('SMS status webhook', $request->all());

            // Update SMS status in communication record
            $messageSid = $request->input('MessageSid');
            $messageStatus = $request->input('MessageStatus');

            // Find and update the communication record
            // Implementation would be similar to call status updates

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle SMS status', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Handle incoming WhatsApp webhook
     */
    public function handleIncomingWhatsapp(Request $request)
    {
        try {
            Log::info('Incoming WhatsApp webhook', $request->all());

            // Similar to SMS but for WhatsApp
            $webhookData = $request->all();
            $webhookData['type'] = 'whatsapp'; // Override type

            $communication = $this->twilioService->handleIncomingSms($webhookData);

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle incoming WhatsApp', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Handle WhatsApp status updates
     */
    public function handleWhatsappStatus(Request $request)
    {
        try {
            Log::info('WhatsApp status webhook', $request->all());

            // Similar to SMS status handling
            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle WhatsApp status', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Handle recording completion webhook
     */
    public function handleRecordingComplete(Request $request)
    {
        try {
            Log::info('Recording complete webhook', $request->all());

            $this->twilioService->handleRecordingComplete($request->all());

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Failed to handle recording completion', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Check if we should auto-respond to SMS
     */
    private function shouldAutoRespond(Request $request): bool
    {
        // Business logic to determine if we should auto-respond
        // Could be based on time of day, message content, etc.
        
        $message = $request->input('Body', '');
        
        // Don't auto-respond to certain keywords
        $noAutoResponse = ['stop', 'unsubscribe', 'opt-out'];
        
        foreach ($noAutoResponse as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return false;
            }
        }

        // For now, let's auto-respond during business hours
        $hour = now()->hour;
        return $hour >= 9 && $hour <= 17; // 9 AM to 5 PM
    }

    /**
     * Generate auto-response message
     */
    private function generateAutoResponse($communication): ?string
    {
        // Could use AI service to generate contextual responses
        return "Thank you for your message. We've received it and will respond shortly during business hours (9 AM - 5 PM). For urgent matters, please call us directly.";
    }

    /**
     * Validate Twilio webhook (optional security measure)
     */
    private function validateTwilioWebhook(Request $request): bool
    {
        $twilioSignature = $request->header('X-Twilio-Signature');
        $url = $request->url();
        $postVars = $request->all();

        $validator = new \Twilio\Security\RequestValidator(config('services.twilio.auth_token'));
        
        return $validator->validate($twilioSignature, $url, $postVars);
    }
}