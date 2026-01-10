<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate and return XML sitemap
     */
    public function index(): Response
    {
        // Define all public routes
        $urls = [
            [
                'loc' => url('/'),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => url('/features'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => url('/pricing'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => url('/contact'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => url('/login'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => url('/register'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
        ];

        // Generate XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
