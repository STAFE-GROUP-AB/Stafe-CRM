<?php

namespace App\Services;

use App\Models\Communication;
use App\Models\UserAiConfiguration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TranscriptionService
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Transcribe a call recording
     */
    public function transcribeCall(Communication $communication): void
    {
        if (!$communication->hasRecording()) {
            Log::warning('No recording URL for communication', ['id' => $communication->id]);
            return;
        }

        try {
            // Download the recording file
            $audioContent = $this->downloadRecording($communication->recording_url);
            
            if (!$audioContent) {
                Log::error('Failed to download recording', ['id' => $communication->id]);
                return;
            }

            // Get user's AI configuration for transcription
            $aiConfig = $this->getUserAiConfig($communication->user_id);
            
            // Transcribe using the appropriate service
            $transcription = $this->transcribeAudio($audioContent, $aiConfig);
            
            if ($transcription) {
                $this->processTranscription($communication, $transcription);
            }

        } catch (\Exception $e) {
            Log::error('Transcription failed', [
                'communication_id' => $communication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Download recording from URL
     */
    private function downloadRecording(string $url): ?string
    {
        try {
            // Add Twilio authentication if needed
            $response = Http::withBasicAuth(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            )->get($url);

            if ($response->successful()) {
                return $response->body();
            }

            Log::error('Failed to download recording', [
                'url' => $url,
                'status' => $response->status(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Recording download exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }

    /**
     * Get user's AI configuration for transcription
     */
    private function getUserAiConfig(?int $userId): ?UserAiConfiguration
    {
        if (!$userId) {
            return null;
        }

        return UserAiConfiguration::forUser($userId)
            ->active()
            ->default()
            ->first();
    }

    /**
     * Transcribe audio using AI service
     */
    private function transcribeAudio(string $audioContent, ?UserAiConfiguration $config): ?array
    {
        if (!$config) {
            // Fallback to OpenAI Whisper if no user config
            return $this->transcribeWithWhisper($audioContent);
        }

        $provider = $config->aiProvider->slug;

        return match ($provider) {
            'openai' => $this->transcribeWithOpenAI($audioContent, $config),
            'google' => $this->transcribeWithGoogle($audioContent, $config),
            'azure' => $this->transcribeWithAzure($audioContent, $config),
            default => $this->transcribeWithWhisper($audioContent)
        };
    }

    /**
     * Transcribe with OpenAI Whisper
     */
    private function transcribeWithOpenAI(string $audioContent, UserAiConfiguration $config): ?array
    {
        try {
            // Save audio to temporary file
            $tempFile = $this->saveTemporaryAudio($audioContent);
            
            if (!$tempFile) {
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $config->getCredential('api_key'),
            ])->attach(
                'file', $audioContent, 'recording.wav'
            )->attach(
                'model', 'whisper-1'
            )->attach(
                'response_format', 'verbose_json'
            )->attach(
                'timestamp_granularities[]', 'word'
            )->post('https://api.openai.com/v1/audio/transcriptions');

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'text' => $data['text'] ?? '',
                    'segments' => $data['words'] ?? [],
                    'language' => $data['language'] ?? 'en',
                    'provider' => 'openai_whisper',
                    'confidence' => $this->calculateAverageConfidence($data['words'] ?? []),
                ];
            }

            Log::error('OpenAI transcription failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('OpenAI transcription exception', ['error' => $e->getMessage()]);
            return null;
        } finally {
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Fallback Whisper transcription (using local or external Whisper)
     */
    private function transcribeWithWhisper(string $audioContent): ?array
    {
        // This could be implemented to use a local Whisper installation
        // or an external Whisper API service
        
        try {
            // For now, we'll use a basic text extraction approach
            // In a real implementation, you would integrate with Whisper directly
            
            return [
                'text' => '[Transcription not available - Whisper integration pending]',
                'segments' => [],
                'language' => 'en',
                'provider' => 'whisper_local',
                'confidence' => 0.0,
            ];

        } catch (\Exception $e) {
            Log::error('Whisper transcription exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Transcribe with Google Speech-to-Text
     */
    private function transcribeWithGoogle(string $audioContent, UserAiConfiguration $config): ?array
    {
        try {
            $apiKey = $config->getCredential('api_key');
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://speech.googleapis.com/v1/speech:recognize?key={$apiKey}", [
                'config' => [
                    'encoding' => 'WEBM_OPUS', // Adjust based on Twilio format
                    'sampleRateHertz' => 8000,
                    'languageCode' => 'en-US',
                    'enableWordTimeOffsets' => true,
                    'enableSpeakerDiarization' => true,
                    'diarizationSpeakerCount' => 2,
                ],
                'audio' => [
                    'content' => base64_encode($audioContent),
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['results'] ?? [];
                
                if (empty($results)) {
                    return null;
                }

                $transcript = '';
                $words = [];
                
                foreach ($results as $result) {
                    $alternatives = $result['alternatives'] ?? [];
                    if (!empty($alternatives)) {
                        $alternative = $alternatives[0];
                        $transcript .= $alternative['transcript'] ?? '';
                        
                        if (isset($alternative['words'])) {
                            $words = array_merge($words, $alternative['words']);
                        }
                    }
                }

                return [
                    'text' => trim($transcript),
                    'segments' => $words,
                    'language' => 'en',
                    'provider' => 'google_speech',
                    'confidence' => $this->calculateAverageConfidence($words),
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Google transcription exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Transcribe with Azure Speech Services
     */
    private function transcribeWithAzure(string $audioContent, UserAiConfiguration $config): ?array
    {
        // Azure Speech Services implementation
        // This would be similar to the other providers but using Azure's API
        
        try {
            // Placeholder for Azure implementation
            return [
                'text' => '[Azure transcription not implemented]',
                'segments' => [],
                'language' => 'en',
                'provider' => 'azure_speech',
                'confidence' => 0.0,
            ];

        } catch (\Exception $e) {
            Log::error('Azure transcription exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Process transcription results
     */
    private function processTranscription(Communication $communication, array $transcription): void
    {
        // Update communication with transcription
        $communication->update([
            'transcript' => $transcription['text'],
            'ai_analysis' => array_merge($communication->ai_analysis ?? [], [
                'transcription' => $transcription,
                'transcription_confidence' => $transcription['confidence'],
                'transcription_provider' => $transcription['provider'],
            ]),
        ]);

        // Analyze transcript with AI for insights
        $this->analyzeTranscript($communication, $transcription['text']);

        // Generate follow-up suggestions
        $this->generateFollowUpSuggestions($communication, $transcription['text']);

        Log::info('Transcription completed', [
            'communication_id' => $communication->id,
            'provider' => $transcription['provider'],
            'confidence' => $transcription['confidence'],
            'text_length' => strlen($transcription['text']),
        ]);
    }

    /**
     * Analyze transcript for insights
     */
    private function analyzeTranscript(Communication $communication, string $transcript): void
    {
        try {
            // Extract keywords, sentiment, and topics
            $analysis = $this->aiService->analyzeConversation($transcript);
            
            if ($analysis) {
                $communication->addAiAnalysis([
                    'conversation_analysis' => $analysis,
                    'analyzed_at' => now()->toISOString(),
                ]);

                // Update sentiment score if available
                if (isset($analysis['sentiment_score'])) {
                    $communication->update(['sentiment_score' => $analysis['sentiment_score']]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Transcript analysis failed', [
                'communication_id' => $communication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate AI-powered follow-up suggestions
     */
    private function generateFollowUpSuggestions(Communication $communication, string $transcript): void
    {
        try {
            $suggestions = $this->aiService->generateFollowUpSuggestions($transcript, [
                'communication_type' => $communication->type,
                'contact_info' => $communication->communicable,
                'call_duration' => $communication->duration_seconds,
            ]);

            if ($suggestions) {
                $communication->addFollowUpSuggestions($suggestions);
            }

        } catch (\Exception $e) {
            Log::error('Follow-up suggestions failed', [
                'communication_id' => $communication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Save audio content to temporary file
     */
    private function saveTemporaryAudio(string $audioContent): ?string
    {
        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'recording_');
            file_put_contents($tempFile, $audioContent);
            return $tempFile;
        } catch (\Exception $e) {
            Log::error('Failed to save temporary audio file', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Calculate average confidence from word segments
     */
    private function calculateAverageConfidence(array $words): float
    {
        if (empty($words)) {
            return 0.0;
        }

        $totalConfidence = 0.0;
        $count = 0;

        foreach ($words as $word) {
            if (isset($word['confidence'])) {
                $totalConfidence += $word['confidence'];
                $count++;
            }
        }

        return $count > 0 ? round($totalConfidence / $count, 4) : 0.0;
    }

    /**
     * Check if transcription is available for a communication
     */
    public function canTranscribe(Communication $communication): bool
    {
        return $communication->hasRecording() && 
               in_array($communication->type, ['call', 'video']) && 
               empty($communication->transcript);
    }
}