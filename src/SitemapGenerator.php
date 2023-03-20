<?php

namespace SitemapGenerator;


/**
 * XML sitemaps generator with support of sitemaps with 50000+ pages
 * Class SitemapGenerator
 * @package SitemapGenerator
 * @author Michael Krutikov. https://github.com/Sunsetboy
 */
class SitemapGenerator
{
    private int $linksPerFileLimit = 50_000;

    private int $fileSizeLimit = 10 * 1024 * 1024 - 1000;

    /**
     * @var array
     * @example [
     *      'link' => 'http://example.com/123',
     *      'date' => '2018-12-07',
     *      'frequency' => 'weekly',
     *      'priority' => 0.5,
     *  ]
     */
    protected array $links = [];

    /**
     * Each sitemap is a set of links
     */
    protected array $sitemaps = [];

    /**
     * The website URL
     */
    protected string $siteUrl = '';

    /**
     * @param int $linksPerFileLimit
     * @return SitemapGenerator
     */
    public function setLinksPerFileLimit(int $linksPerFileLimit): SitemapGenerator
    {
        $this->linksPerFileLimit = $linksPerFileLimit;
        return $this;
    }

    /**
     * @param int $fileSizeLimit
     * @return SitemapGenerator
     */
    public function setFileSizeLimit(int $fileSizeLimit): SitemapGenerator
    {
        $this->fileSizeLimit = $fileSizeLimit;
        return $this;
    }

    /**
     * Setting array of links
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    /**
     * Setting the website URL
     */
    public function setSiteUrl(string $siteUrl): self
    {
        $this->siteUrl = $siteUrl;
        return $this;
    }

    /**
     * Creating an array of sitemaps. Each element is XML with up to $linksPerFileLimit links
     */
    public function createSitemaps(): array
    {
        $sitemapCounter = 0;
        $currentFileSize = 0;

        foreach ($this->links as $counter => $link) {
            if ($counter % $this->linksPerFileLimit == 0 || $currentFileSize > $this->fileSizeLimit) {
                $sitemapCounter++;
                $currentFileSize = 0;
            }
            $sitemapElement = "<url>
              <loc>" . $link['link'] . "</loc>
              <lastmod>" . $link['date'] . "</lastmod>
              <changefreq>" . $link['frequency'] . "</changefreq>
              <priority>" . $link['priority'] . "</priority>
           </url>";
            $this->sitemaps[$sitemapCounter][] = $sitemapElement;
            $currentFileSize += mb_strlen($sitemapElement);
        }

        return $this->sitemaps;
    }

    /**
     * Saving files to a folder
     */
    public function saveAsFiles(string $folder): void
    {
        $today = (new \DateTime())->format('Y-m-d');
        $sitemapIndexItems = [];

        foreach ($this->sitemaps as $sitemapNumber => $sitemapItems) {

            $sitemapContent = $this->getHeader() .
                implode(PHP_EOL, $sitemapItems) .
                $this->getFooter();
            file_put_contents($folder . '/sitemap_' . $sitemapNumber . '.xml', $sitemapContent);

            $sitemapIndexItems[] = '<sitemap>
                  <loc>' . $this->siteUrl . '/sitemap_' . $sitemapNumber . '.xml</loc>
                  <lastmod>' . $today . '</lastmod></sitemap>';
        }

        $sitemapIndexContent = $this->getHeader() .
            implode(PHP_EOL, $sitemapIndexItems) .
            '</sitemapindex>';
        file_put_contents($folder . '/sitemap.xml', $sitemapIndexContent);
    }

    /**
     * Getting a header of XML
     */
    private function getHeader(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * Getting a footer of XML
     */
    private function getFooter(): string
    {
        return '</urlset>';
    }
}
