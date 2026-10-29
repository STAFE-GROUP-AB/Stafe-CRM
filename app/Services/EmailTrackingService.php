<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmailTrackingService
{
    /**
     * Generate tracking pixel URL for email open tracking
     */
    public function generateTrackingPixel(Email $email): string
    {
        $trackingToken = $this->generateTrackingToken($email);
        
        return route('email.track.open', ['token' => $trackingToken]);
    }

    /**
     * Generate trackable link for click tracking
     */
    public function generateTrackableLink(Email $email, string $originalUrl, int $linkIndex = 0): string
    {
        $trackingToken = $this->generateTrackingToken($email);
        
        return route('email.track.click', [
            'token' => $trackingToken,
            'link' => $linkIndex,
            'url' => base64_encode($originalUrl),
        ]);
    }

    /**
     * Generate unique tracking token for email
     */
    protected function generateTrackingToken(Email $email): string
    {
        if ($email->tracking_token) {
            return $email->tracking_token;
        }

        $token = Str::random(64);
        
        $email->update(['tracking_token' => $token]);
        
        return $token;
    }

    /**
     * Record email open event
     */
    public function recordOpen(string $token, array $metadata = []): bool
    {
        $email = Email::where('tracking_token', $token)->first();
        
        if (!$email) {
            Log::warning('Email tracking token not found', ['token' => $token]);
            return false;
        }

        // Only record first open
        if (!$email->opened_at) {
            $email->update([
                'opened_at' => now(),
                'open_count' => 1,
                'tracking_metadata' => array_merge($email->tracking_metadata ?? [], [
                    'first_open' => [
                        'timestamp' => now()->toISOString(),
                        'ip' => $metadata['ip'] ?? null,
                        'user_agent' => $metadata['user_agent'] ?? null,
                        'location' => $metadata['location'] ?? null,
                    ],
                ]),
            ]);
        } else {
            // Increment open count for subsequent opens
            $email->increment('open_count');
            
            $trackingMetadata = $email->tracking_metadata ?? [];
            $trackingMetadata['opens'][] = [
                'timestamp' => now()->toISOString(),
                'ip' => $metadata['ip'] ?? null,
                'user_agent' => $metadata['user_agent'] ?? null,
            ];
            
            $email->update(['tracking_metadata' => $trackingMetadata]);
        }

        Log::info('Email open tracked', [
            'email_id' => $email->id,
            'open_count' => $email->open_count,
        ]);

        return true;
    }

    /**
     * Record email click event
     */
    public function recordClick(string $token, int $linkIndex, array $metadata = []): ?string
    {
        $email = Email::where('tracking_token', $token)->first();
        
        if (!$email) {
            Log::warning('Email tracking token not found for click', ['token' => $token]);
            return null;
        }

        // Record first click
        if (!$email->clicked_at) {
            $email->update([
                'clicked_at' => now(),
                'click_count' => 1,
            ]);
        } else {
            $email->increment('click_count');
        }

        // Track individual link clicks
        $trackingMetadata = $email->tracking_metadata ?? [];
        $trackingMetadata['clicks'][] = [
            'timestamp' => now()->toISOString(),
            'link_index' => $linkIndex,
            'ip' => $metadata['ip'] ?? null,
            'user_agent' => $metadata['user_agent'] ?? null,
        ];
        
        $email->update(['tracking_metadata' => $trackingMetadata]);

        Log::info('Email click tracked', [
            'email_id' => $email->id,
            'link_index' => $linkIndex,
            'click_count' => $email->click_count,
        ]);

        // Return the original URL from metadata
        $links = $email->tracking_metadata['tracked_links'] ?? [];
        return $links[$linkIndex] ?? null;
    }

    /**
     * Inject tracking pixel into email HTML
     */
    public function injectTrackingPixel(Email $email, string $htmlContent): string
    {
        $pixelUrl = $this->generateTrackingPixel($email);
        
        $trackingPixel = sprintf(
            '<img src="%s" width="1" height="1" alt="" style="display:none;" />',
            $pixelUrl
        );

        // Insert before closing body tag
        if (stripos($htmlContent, '</body>') !== false) {
            return str_ireplace('</body>', $trackingPixel . '</body>', $htmlContent);
        }

        // Append to end if no body tag
        return $htmlContent . $trackingPixel;
    }

    /**
     * Replace links in email HTML with trackable links
     */
    public function injectTrackableLinks(Email $email, string $htmlContent): string
    {
        $linkIndex = 0;
        $trackedLinks = [];

        // Find all links in HTML
        $pattern = '/<a\s+(?:[^>]*?\s+)?href=(["\'])(.*?)\1/i';
        
        $htmlContent = preg_replace_callback($pattern, function ($matches) use ($email, &$linkIndex, &$trackedLinks) {
            $originalUrl = $matches[2];
            
            // Skip tracking pixel URLs and anchor links
            if (str_starts_with($originalUrl, '#') || 
                str_contains($originalUrl, 'email.track.open') ||
                str_contains($originalUrl, 'email.track.click')) {
                return $matches[0];
            }

            $trackedLinks[$linkIndex] = $originalUrl;
            $trackableUrl = $this->generateTrackableLink($email, $originalUrl, $linkIndex);
            $linkIndex++;

            return str_replace($matches[2], $trackableUrl, $matches[0]);
        }, $htmlContent);

        // Store tracked links in email metadata
        $metadata = $email->tracking_metadata ?? [];
        $metadata['tracked_links'] = $trackedLinks;
        $email->update(['tracking_metadata' => $metadata]);

        return $htmlContent;
    }

    /**
     * Get email tracking statistics
     */
    public function getTrackingStats(Email $email): array
    {
        return [
            'sent_at' => $email->sent_at,
            'opened_at' => $email->opened_at,
            'clicked_at' => $email->clicked_at,
            'open_count' => $email->open_count ?? 0,
            'click_count' => $email->click_count ?? 0,
            'is_opened' => !is_null($email->opened_at),
            'is_clicked' => !is_null($email->clicked_at),
            'time_to_open' => $email->opened_at && $email->sent_at 
                ? $email->sent_at->diffInSeconds($email->opened_at) 
                : null,
            'time_to_click' => $email->clicked_at && $email->sent_at 
                ? $email->sent_at->diffInSeconds($email->clicked_at) 
                : null,
            'tracked_links' => $email->tracking_metadata['tracked_links'] ?? [],
            'click_details' => $email->tracking_metadata['clicks'] ?? [],
        ];
    }

    /**
     * Get aggregate tracking statistics for multiple emails
     */
    public function getAggregateStats(array $emailIds): array
    {
        $emails = Email::whereIn('id', $emailIds)->get();
        
        $totalSent = $emails->count();
        $totalOpened = $emails->whereNotNull('opened_at')->count();
        $totalClicked = $emails->whereNotNull('clicked_at')->count();
        
        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;
        $clickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0;
        $clickToOpenRate = $totalOpened > 0 ? round(($totalClicked / $totalOpened) * 100, 2) : 0;

        return [
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'open_rate' => $openRate,
            'click_rate' => $clickRate,
            'click_to_open_rate' => $clickToOpenRate,
            'avg_time_to_open' => $this->calculateAverageTimeToOpen($emails),
            'avg_time_to_click' => $this->calculateAverageTimeToClick($emails),
        ];
    }

    /**
     * Calculate average time to open
     */
    protected function calculateAverageTimeToOpen($emails): ?int
    {
        $openedEmails = $emails->whereNotNull('opened_at')->whereNotNull('sent_at');
        
        if ($openedEmails->isEmpty()) {
            return null;
        }

        $totalSeconds = $openedEmails->sum(function ($email) {
            return $email->sent_at->diffInSeconds($email->opened_at);
        });

        return round($totalSeconds / $openedEmails->count());
    }

    /**
     * Calculate average time to click
     */
    protected function calculateAverageTimeToClick($emails): ?int
    {
        $clickedEmails = $emails->whereNotNull('clicked_at')->whereNotNull('sent_at');
        
        if ($clickedEmails->isEmpty()) {
            return null;
        }

        $totalSeconds = $clickedEmails->sum(function ($email) {
            return $email->sent_at->diffInSeconds($email->clicked_at);
        });

        return round($totalSeconds / $clickedEmails->count());
    }
}
