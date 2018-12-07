# XML sitemap generator
Generator of sitemap XML files for unlimited sets of links

### Usage example
```
$sitemapGenerator = new SitemapGenerator();
$siteUrl = 'http://example.com';
$links = [
    [
        'link' => 'http://example.com/123',
        'date' => '2018-12-07',
        'frequency' => 'weekly',
        'priority' => 0.5,
    ],
    [
        'link' => 'http://example.com/456',
        'date' => '2018-12-07',
        'frequency' => 'weekly',
        'priority' => 0.5,
    ]
];

$sitemapGenerator->setSiteUrl($siteUrl)->setLinks($links)->createSitemaps();
$sitemapGenerator->saveAsFiles('/var/www/site');
```