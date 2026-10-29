<?php

namespace App\Http\Controllers;

use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    protected EmailTrackingService $trackingService;

    public function __construct(EmailTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Track email open via 1x1 transparent pixel
     */
    public function trackOpen(Request $request, string $token): Response
    {
        $metadata = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
        ];

        $this->trackingService->recordOpen($token, $metadata);

        // Return 1x1 transparent GIF
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        
        return response($gif, 200)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Track email link click and redirect to original URL
     */
    public function trackClick(Request $request, string $token): \Illuminate\Http\RedirectResponse
    {
        $linkIndex = (int) $request->query('link', 0);
        $encodedUrl = $request->query('url');

        $metadata = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
        ];

        $originalUrl = $this->trackingService->recordClick($token, $linkIndex, $metadata);

        // Decode the original URL
        if (!$originalUrl && $encodedUrl) {
            $originalUrl = base64_decode($encodedUrl);
        }

        // Redirect to original URL or fallback
        if ($originalUrl && filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return redirect()->away($originalUrl);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Invalid tracking link');
    }

    /**
     * Get tracking statistics for an email
     */
    public function getStats(Request $request, string $token): \Illuminate\Http\JsonResponse
    {
        $email = \App\Models\Email::where('tracking_token', $token)->first();

        if (!$email) {
            return response()->json([
                'error' => 'Email not found',
            ], 404);
        }

        $stats = $this->trackingService->getTrackingStats($email);

        return response()->json($stats);
    }
}
