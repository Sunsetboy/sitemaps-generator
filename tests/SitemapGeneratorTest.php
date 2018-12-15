<?php

use SitemapGenerator\SitemapGenerator;
use PHPUnit\Framework\TestCase;


class SitemapGeneratorTest extends TestCase
{
    public function testCreateSitemap()
    {
        $sitemapGenerator = new SitemapGenerator();

        $links = [
            [
                'link' => 'http://www.100yuristov.com/123',
                'date' => '2018-12-07',
                'frequency' => 'weekly',
                'priority' => 0.5,
            ],
        ];

        $sitemaps = $sitemapGenerator->setSiteUrl('http://www.100yuristov.com')
            ->setLinks($links)
            ->createSitemaps();

        $this->assertEquals(1, sizeof($sitemaps));
    }
}
