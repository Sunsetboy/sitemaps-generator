<?php

use PHPUnit\Framework\TestCase;
use SitemapGenerator\SitemapGenerator;


class SitemapGeneratorTest extends TestCase
{
    const TMP_SITEMAPS_FOLDER = '/tmp/sitemaps';

    public function setUp(): void
    {
        parent::setUp();
        if (!file_exists(self::TMP_SITEMAPS_FOLDER)) {
            mkdir(self::TMP_SITEMAPS_FOLDER);
        }
    }

    /**
     * @dataProvider providerCreateSitemap
     */
    public function testCreateSitemap(
        array $links,
        int   $elementsLimit,
        int   $sizeLimit,
        int   $expectedFilesNumber
    ) {
        $sitemapGenerator = new SitemapGenerator();

        $sitemaps = $sitemapGenerator->setSiteUrl('https://www.100yuristov.com')
            ->setFileSizeLimit($sizeLimit)
            ->setLinksPerFileLimit($elementsLimit)
            ->setLinks($links)
            ->createSitemaps();

        $sitemapGenerator->saveAsFiles(self::TMP_SITEMAPS_FOLDER);

        $this->assertEquals($expectedFilesNumber, sizeof($sitemaps));
        $this->assertFileExists(self::TMP_SITEMAPS_FOLDER . '/sitemap.xml');
        for ($fileNumber = 1; $fileNumber <= $expectedFilesNumber; $fileNumber++) {
            $this->assertFileExists(self::TMP_SITEMAPS_FOLDER . '/sitemap_' . $fileNumber . '.xml');
        }
    }

    public static function providerCreateSitemap(): array
    {
        return [
            'one link, default limits' => [
                'links' => [
                    [
                        'link' => 'https://www.100yuristov.com/123',
                        'date' => '2018-12-07',
                        'frequency' => 'weekly',
                        'priority' => 0.5,
                    ],
                ],
                'elementsLimit' => 50000,
                'sizeLimit' => 10 * 1024 * 1024,
                'expectedFilesNumber' => 1,
            ],
            'two links, limit 1 element' => [
                'links' => [
                    [
                        'link' => 'https://www.100yuristov.com/123',
                        'date' => '2018-12-07',
                        'frequency' => 'weekly',
                        'priority' => 0.5,
                    ],
                    [
                        'link' => 'https://www.100yuristov.com/123456',
                        'date' => '2018-12-07',
                        'frequency' => 'weekly',
                        'priority' => 0.5,
                    ],
                ],
                'elementsLimit' => 1,
                'sizeLimit' => 10 * 1024 * 1024,
                'expectedFilesNumber' => 2,
            ],
            'two links, file size limit' => [
                'links' => [
                    [
                        'link' => 'https://www.100yuristov.com/123',
                        'date' => '2018-12-07',
                        'frequency' => 'weekly',
                        'priority' => 0.5,
                    ],
                    [
                        'link' => 'https://www.100yuristov.com/123456',
                        'date' => '2018-12-07',
                        'frequency' => 'weekly',
                        'priority' => 0.5,
                    ],
                ],
                'elementsLimit' => 10,
                'sizeLimit' => 100,
                'expectedFilesNumber' => 2,
            ],
        ];
    }

    /**
     * @dataProvider createFromGeneratorProvider
     */
    public function testCreateFilesFromLinksGenerator(
        Generator $linksGenerator,
        int $elementsLimit,
        int $sizeLimit,
        int $expectedFilesNumber
    ) {
        $sitemapGenerator = new SitemapGenerator();

        $sitemapsCount = $sitemapGenerator->setSiteUrl('https://www.100yuristov.com')
            ->setFileSizeLimit($sizeLimit)
            ->setLinksPerFileLimit($elementsLimit)
            ->createFilesFromLinksGenerator($linksGenerator, self::TMP_SITEMAPS_FOLDER);

        $this->assertEquals($expectedFilesNumber, $sitemapsCount);
        $this->assertFileExists(self::TMP_SITEMAPS_FOLDER . '/sitemap.xml');
        for ($fileNumber = 1; $fileNumber <= $expectedFilesNumber; $fileNumber++) {
            $this->assertFileExists(self::TMP_SITEMAPS_FOLDER . '/sitemap_' . $fileNumber . '.xml');
        }
    }

    public static function createFromGeneratorProvider(): array
    {
        return [
            'one link, default limits' => [
                'linksGenerator' => self::createLinksGenerator(1),
                'elementsLimit' => 50000,
                'sizeLimit' => 10 * 1024 * 1024,
                'expectedFilesNumber' => 1,
            ],
            '100 links, 50 links per file limit' => [
                'linksGenerator' => self::createLinksGenerator(100),
                'elementsLimit' => 50,
                'sizeLimit' => 10 * 1024 * 1024,
                'expectedFilesNumber' => 2,
            ],
        ];
    }

    private static function createLinksGenerator(int $size): Generator
    {
        for ($i = 0; $i < $size; $i++) {
            yield [
                'link' => 'https://www.100yuristov.com/' . $i . '.html',
                'date' => '2018-12-07',
                'frequency' => 'weekly',
                'priority' => 0.5,
            ];
        }
    }
}
