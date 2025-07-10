<?php

namespace App\Services;

use App\Models\Communication;
use App\Models\Contact;
use App\Models\Company;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TwilioService
{
    protected TwilioClient $client;
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
        
        // Initialize Twilio client if credentials are configured
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        
        if ($accountSid && $authToken) {
            $this->client = new TwilioClient($accountSid, $authToken);
        }
    }

    /**
     * Make an outbound call
     */
    public function makeCall(string $fromNumber, string $toNumber, $communicable = null, ?int $userId = null): ?Communication
    {
        if (!$this->client) {
            throw new \Exception('Twilio not configured');
        }

        try {
            $call = $this->client->calls->create(
                $toNumber,
                $fromNumber,
                [
                    'url' => route('twilio.call.twiml'), // TwiML endpoint
                    'statusCallback' => route('twilio.call.status'),
                    'statusCallbackEvent' => ['initiated', 'ringing', 'answered', 'completed'],
                    'record' => true,
                    'recordingStatusCallback' => route('twilio.recording.status'),
                ]
            );

            // Create communication record
            $communication = Communication::create([
                'type' => 'call',
                'direction' => 'outbound',
                'status' => 'initiated',
                'communicable_type' => $communicable ? get_class($communicable) : null,
                'communicable_id' => $communicable?->id,
                'user_id' => $userId,
                'from_number' => $fromNumber,
                'to_number' => $toNumber,
                'external_id' => $call->sid,
                'provider' => 'twilio',
                'provider_data' => [
                    'call_sid' => $call->sid,
                    'account_sid' => $call->accountSid,
                    'status' => $call->status,
                ],
            ]);

            Log::info('Outbound call initiated', [
                'communication_id' => $communication->id,
                'call_sid' => $call->sid,
                'to' => $toNumber,
            ]);

            return $communication;

        } catch (TwilioException $e) {
            Log::error('Failed to make call', [
                'error' => $e->getMessage(),
                'to' => $toNumber,
                'from' => $fromNumber,
            ]);
            
            throw new \Exception('Failed to make call: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS message
     */
    public function sendSms(string $fromNumber, string $toNumber, string $message, $communicable = null, ?int $userId = null): ?Communication
    {
        if (!$this->client) {
            throw new \Exception('Twilio not configured');
        }

        try {
            $sms = $this->client->messages->create(
                $toNumber,
                [
                    'from' => $fromNumber,
                    'body' => $message,
                    'statusCallback' => route('twilio.sms.status'),
                ]
            );

            // Create communication record
            $communication = Communication::create([
                'type' => 'sms',
                'direction' => 'outbound',
                'status' => 'initiated',
                'communicable_type' => $communicable ? get_class($communicable) : null,
                'communicable_id' => $communicable?->id,
                'user_id' => $userId,
                'content' => $message,
                'from_number' => $fromNumber,
                'to_number' => $toNumber,
                'external_id' => $sms->sid,
                'provider' => 'twilio',
                'provider_data' => [
                    'message_sid' => $sms->sid,
                    'account_sid' => $sms->accountSid,
                    'status' => $sms->status,
                ],
            ]);

            Log::info('SMS sent', [
                'communication_id' => $communication->id,
                'message_sid' => $sms->sid,
                'to' => $toNumber,
            ]);

            return $communication;

        } catch (TwilioException $e) {
            Log::error('Failed to send SMS', [
                'error' => $e->getMessage(),
                'to' => $toNumber,
                'message' => $message,
            ]);
            
            throw new \Exception('Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp message
     */
    public function sendWhatsApp(string $fromNumber, string $toNumber, string $message, $communicable = null, ?int $userId = null): ?Communication
    {
        if (!$this->client) {
            throw new \Exception('Twilio not configured');
        }

        try {
            $whatsapp = $this->client->messages->create(
                'whatsapp:' . $toNumber,
                [
                    'from' => 'whatsapp:' . $fromNumber,
                    'body' => $message,
                    'statusCallback' => route('twilio.whatsapp.status'),
                ]
            );

            // Create communication record
            $communication = Communication::create([
                'type' => 'whatsapp',
                'direction' => 'outbound',
                'status' => 'initiated',
                'communicable_type' => $communicable ? get_class($communicable) : null,
                'communicable_id' => $communicable?->id,
                'user_id' => $userId,
                'content' => $message,
                'from_number' => $fromNumber,
                'to_number' => $toNumber,
                'external_id' => $whatsapp->sid,
                'provider' => 'twilio',
                'provider_data' => [
                    'message_sid' => $whatsapp->sid,
                    'account_sid' => $whatsapp->accountSid,
                    'status' => $whatsapp->status,
                ],
            ]);

            return $communication;

        } catch (TwilioException $e) {
            Log::error('Failed to send WhatsApp message', [
                'error' => $e->getMessage(),
                'to' => $toNumber,
                'message' => $message,
            ]);
            
            throw new \Exception('Failed to send WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Handle incoming call webhook
     */
    public function handleIncomingCall(array $webhookData): Communication
    {
        $fromNumber = $webhookData['From'] ?? '';
        $toNumber = $webhookData['To'] ?? '';
        $callSid = $webhookData['CallSid'] ?? '';

        // Try to find associated contact
        $contact = Contact::where('phone', $fromNumber)
            ->orWhere('mobile', $fromNumber)
            ->first();

        $communication = Communication::create([
            'type' => 'call',
            'direction' => 'inbound',
            'status' => 'ringing',
            'communicable_type' => $contact ? Contact::class : null,
            'communicable_id' => $contact?->id,
            'from_number' => $fromNumber,
            'to_number' => $toNumber,
            'external_id' => $callSid,
            'provider' => 'twilio',
            'provider_data' => $webhookData,
        ]);

        Log::info('Incoming call received', [
            'communication_id' => $communication->id,
            'call_sid' => $callSid,
            'from' => $fromNumber,
            'contact_id' => $contact?->id,
        ]);

        return $communication;
    }

    /**
     * Handle incoming SMS webhook
     */
    public function handleIncomingSms(array $webhookData): Communication
    {
        $fromNumber = $webhookData['From'] ?? '';
        $toNumber = $webhookData['To'] ?? '';
        $message = $webhookData['Body'] ?? '';
        $messageSid = $webhookData['MessageSid'] ?? '';

        // Try to find associated contact
        $contact = Contact::where('phone', $fromNumber)
            ->orWhere('mobile', $fromNumber)
            ->first();

        $communication = Communication::create([
            'type' => 'sms',
            'direction' => 'inbound',
            'status' => 'completed',
            'communicable_type' => $contact ? Contact::class : null,
            'communicable_id' => $contact?->id,
            'content' => $message,
            'from_number' => $fromNumber,
            'to_number' => $toNumber,
            'external_id' => $messageSid,
            'provider' => 'twilio',
            'provider_data' => $webhookData,
        ]);

        // Analyze message with AI
        if ($message) {
            $this->analyzeMessageWithAi($communication);
        }

        return $communication;
    }

    /**
     * Update call status from webhook
     */
    public function updateCallStatus(array $webhookData): ?Communication
    {
        $callSid = $webhookData['CallSid'] ?? '';
        $callStatus = $webhookData['CallStatus'] ?? '';
        $duration = $webhookData['CallDuration'] ?? 0;

        $communication = Communication::where('external_id', $callSid)->first();
        
        if (!$communication) {
            Log::warning('Call status update for unknown call', ['call_sid' => $callSid]);
            return null;
        }

        $communication->update([
            'status' => $this->mapTwilioStatusToInternal($callStatus),
            'duration_seconds' => (int) $duration,
            'provider_data' => array_merge($communication->provider_data ?? [], $webhookData),
        ]);

        return $communication;
    }

    /**
     * Handle recording completion
     */
    public function handleRecordingComplete(array $webhookData): void
    {
        $callSid = $webhookData['CallSid'] ?? '';
        $recordingUrl = $webhookData['RecordingUrl'] ?? '';
        $duration = $webhookData['RecordingDuration'] ?? 0;

        $communication = Communication::where('external_id', $callSid)->first();
        
        if (!$communication) {
            Log::warning('Recording for unknown call', ['call_sid' => $callSid]);
            return;
        }

        // Store recording URL and initiate transcription
        $communication->update([
            'recording_url' => $recordingUrl,
            'duration_seconds' => max($communication->duration_seconds ?? 0, (int) $duration),
        ]);

        // Queue transcription job
        if ($recordingUrl) {
            dispatch(function () use ($communication) {
                app(TranscriptionService::class)->transcribeCall($communication);
            })->delay(now()->addSeconds(30)); // Give time for recording to be available
        }
    }

    /**
     * Map Twilio status to internal status
     */
    private function mapTwilioStatusToInternal(string $twilioStatus): string
    {
        return match (strtolower($twilioStatus)) {
            'queued', 'ringing' => 'ringing',
            'in-progress', 'answered' => 'answered',
            'completed' => 'completed',
            'busy' => 'busy',
            'failed', 'canceled' => 'failed',
            'no-answer' => 'no-answer',
            default => 'initiated'
        };
    }

    /**
     * Analyze message content with AI
     */
    private function analyzeMessageWithAi(Communication $communication): void
    {
        try {
            // Use existing AI service for sentiment analysis
            $analysis = $this->aiService->analyzeText($communication->content);
            
            if ($analysis) {
                $communication->addAiAnalysis($analysis);
                
                if (isset($analysis['sentiment_score'])) {
                    $communication->update(['sentiment_score' => $analysis['sentiment_score']]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to analyze message with AI', [
                'communication_id' => $communication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if Twilio is configured
     */
    public function isConfigured(): bool
    {
        return !is_null($this->client);
    }

    /**
     * Get account information
     */
    public function getAccountInfo(): ?array
    {
        if (!$this->client) {
            return null;
        }

        try {
            $account = $this->client->api->accounts(config('services.twilio.account_sid'))->fetch();
            
            return [
                'account_sid' => $account->sid,
                'friendly_name' => $account->friendlyName,
                'status' => $account->status,
                'type' => $account->type,
            ];
        } catch (TwilioException $e) {
            Log::error('Failed to fetch Twilio account info', ['error' => $e->getMessage()]);
            return null;
        }
    }
}