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
    const LINKS_PER_FILE_LIMIT = 50000;

    /**
     * @var array
     * @example [
     *      'link' => 'http://example.com/123',
     *      'date' => '2018-12-07',
     *      'frequency' => 'weekly',
     *      'priority' => 0.5,
     *  ]
     */
    protected $links = [];

    /**
     * Each sitemap is a set of links
     * @var array
     */
    protected $sitemaps = [];

    /**
     * The website URL
     * @var string
     */
    protected $siteUrl;


    /**
     * Setting array of links
     * @param array $links
     * @return SitemapGenerator
     */
    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     * Setting the website URL
     * @param string $siteUrl
     * @return $this
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;
        return $this;
    }

    /**
     * Creating an array of sitemaps. Each element is XML with up to LINKS_PER_FILE_LIMIT links
     * @return array
     */
    public function createSitemaps()
    {
        $sitemapCounter = 0;

        foreach ($this->links as $counter => $link) {
            if ($counter % self::LINKS_PER_FILE_LIMIT == 0) {
                $sitemapCounter++;
            }
            $this->sitemaps[$sitemapCounter][] = "<url>
              <loc>" . $link['link'] . "</loc>
              <lastmod>" . $link['date'] . "</lastmod>
              <changefreq>" . $link['frequency'] . "</changefreq>
              <priority>" . $link['priority'] . "</priority>
           </url>";
        }

        return $this->sitemaps;
    }

    /**
     * Saving files to a folder
     * @param string $folder absolute path to website root folder
     */
    public function saveAsFiles($folder)
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
     * @return string
     */
    private function getHeader()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * Getting a footer of XML
     * @return string
     */
    private function getFooter()
    {
        return '</urlset>';
    }
}
