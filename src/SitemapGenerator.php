<?php

namespace SitemapGenerator;

use Generator;

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
     * @var SitemapUrl[]
     *
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
              <loc>" . $link->getLink() . "</loc>
              <lastmod>" . $link->getDate() . "</lastmod>
              <changefreq>" . $link->getFrequency() . "</changefreq>
              <priority>" . $link->getPriority() . "</priority>
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

        $sitemapIndexContent = '<?xml version="1.0" encoding="UTF-8"?>
            <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
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

    public function createFilesFromLinksGenerator(Generator $linksGenerator, string $folder): int
    {
        // empty iterator
        if (is_null($linksGenerator->current())) {
            return 0;
        }

        $today = (new \DateTime())->format('Y-m-d');
        $sitemapIndexItems = [];
        $currentFileLinksCount = 0;
        $currentFileSize = 0;
        $sitemapCounter = 0;

        $currentFile = fopen($this->getSitemapFilePath($folder, $sitemapCounter + 1), 'w');
        fwrite($currentFile, $this->getHeader());

        while($link = $linksGenerator->current()) {
            /** @var SitemapUrl $link */

            // store links into files
            if ($currentFileLinksCount == $this->linksPerFileLimit || $currentFileSize > $this->fileSizeLimit) {
                // current sitemap file reached the limits
                fwrite($currentFile, $this->getFooter());
                fclose($currentFile);

                $sitemapIndexItems[] = '<sitemap>
                  <loc>' . $this->siteUrl . '/sitemap_' . $sitemapCounter . '.xml</loc>
                  <lastmod>' . $today . '</lastmod></sitemap>';
                $sitemapCounter++;
                $currentFileSize = 0;
                $currentFileLinksCount = 0;

                $currentFile = fopen($this->getSitemapFilePath($folder, $sitemapCounter + 1), 'w');
                fwrite($currentFile, $this->getHeader());
            }
            $sitemapElement = "<url>
              <loc>" . $link->getLink() . "</loc>
              <lastmod>" . $link->getDate() . "</lastmod>
              <changefreq>" . $link->getFrequency() . "</changefreq>
              <priority>" . $link->getPriority() . "</priority>
            </url>";

            fwrite($currentFile, $sitemapElement . PHP_EOL);
            $currentFileSize += mb_strlen($sitemapElement);
            $currentFileLinksCount++;

            $linksGenerator->next();
        }

        // finalize the last sitemap file
        fwrite($currentFile, $this->getFooter());
        fclose($currentFile);

        $sitemapIndexItems[] = '<sitemap>
                  <loc>' . $this->siteUrl . '/sitemap_' . $sitemapCounter . '.xml</loc>
                  <lastmod>' . $today . '</lastmod></sitemap>';

        // write the index sitemap file to disk
        $sitemapIndexContent = '<?xml version="1.0" encoding="UTF-8"?>
            <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
            implode(PHP_EOL, $sitemapIndexItems) . PHP_EOL .
            '</sitemapindex>';
        file_put_contents($folder . '/sitemap.xml', $sitemapIndexContent);

        return count($sitemapIndexItems);
    }

    private function getSitemapFilePath(string $folder, int $sitemapNumber): string
    {
        return $folder . '/sitemap_' . $sitemapNumber . '.xml';
    }
}
