<?php

namespace SitemapGenerator;


/**
 * Генератор XML карты сайта с разбивкой на несколько файлов с лимитом 50000 ссылок в файле
 * Class SitemapGenerator
 * @package SitemapGenerator
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
    protected $sitemaps = [];


    /**
     * Загрузка массива ссылок
     * @param array $links
     * @return SitemapGenerator
     */
    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     * Создание массива карт сайта. Каждый элемент - XML с картой максимум из LINKS_PER_FILE_LIMIT ссылок
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
     * Сохранение карты сайта в папку
     * @param string $folder Полный путь к папке
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
                  <loc>' . $folder . '/sitemap_' . $sitemapNumber . '.xml</loc>
                  <lastmod>' . $today . '</lastmod></sitemap>';
        }

        $sitemapIndexContent = '<?xml version="1.0" encoding="UTF-8"?>
            <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
            implode(PHP_EOL, $sitemapIndexItems) .
            '</sitemapindex>';
        file_put_contents($folder . '/sitemap.xml', $sitemapIndexContent);
    }

    /**
     * Получение шапки XML документа
     * @return string
     */
    private function getHeader()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * Получение футера XML
     * @return string
     */
    private function getFooter()
    {
        return '</urlset>';
    }
}
