<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitemapTest extends TestCase
{
    public function test_sitemap_is_accessible(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    public function test_sitemap_contains_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        
        // Check for XML declaration
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        
        // Check for urlset namespace
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        
        // Check for closing urlset tag
        $this->assertStringContainsString('</urlset>', $content);
    }

    public function test_sitemap_contains_main_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        
        // Check for main pages
        $this->assertStringContainsString('<loc>' . url('/') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('/features') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('/pricing') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('/contact') . '</loc>', $content);
    }

    public function test_sitemap_urls_have_required_elements(): void
    {
        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();
        
        // Check that URLs have required elements
        $this->assertStringContainsString('<changefreq>', $content);
        $this->assertStringContainsString('<priority>', $content);
    }
}
