# XML sitemap generator
XML sitemap files generator for unlimited sets of links

![](https://img.shields.io/travis/Sunsetboy/sitemaps-generator.svg) ![](https://img.shields.io/github/release/Sunsetboy/sitemaps-generator.svg) 
![](https://img.shields.io/github/license/Sunsetboy/sitemaps-generator.svg) 
![](https://img.shields.io/codeclimate/maintainability/Sunsetboy/sitemaps-generator.svg)


### What is a XML sitemap?
XML sitemap is a text file with links to pages of a website. Search engines like Google or Yandex use sitemap files to 
discover web pages. You can help your website to be crawled more quickly by adding a sitemap file to it's root directory.

By standard each sitemap file can contain up to 50 000 links. What if you have a large website with thousands of pages? 
Just create few sitemaps and link them together!

This package helps you to create sitemap files for any number of pages on your website.
 
### How it works?
My solution takes any number of links provided by you and packs them into XML files up to 50000 links and 10 MB each 
(sitemap_1.xml, sitemap_2.xml, etc). These limitations are configurable. It also creates an index sitemap file sitemap.xml. 

After generation of sitemap files you can add index sitemap to the search engine using tools like Google Webmaster.
You can also add to your robots.txt file:
```
Sitemap: https://yoursite.com/sitemap.xml 
```

### Requirements
PHP 7.4+ with mb_string extension

### Installation
You can install this package using Composer.
```
composer require yurcrm/sitemap-generator
```

### Usage example
```
$sitemapGenerator = new SitemapGenerator();
// your website URL
$siteUrl = 'https://example.com';

/*
*   You should generate this array of links according to your website content
*/
$links = [
    new SitemapUrl(
        'https://www.100yuristov.com/123',
        '2018-12-07',
        'weekly',
        0.5,
    ),
    new SitemapUrl(
        'https://www.100yuristov.com/123456',
        '2018-12-07',
        'weekly',
        0.5,
    ),
];

$sitemapGenerator->setSiteUrl($siteUrl)
->setLinks($links)
->setFileSizeLimit(5*1024*1024)
->setLinksPerFileLimit(30_000)
->createSitemaps();

// save files to the folder (use absolute path) 
$sitemapGenerator->saveAsFiles('/var/www/site');
```

### Usage with generator
Use this approach for big sitemaps to limit memory usage
```
// $linksGenerator - PHP generator which returns SitemapUrl objects

$sitemapsCount = $sitemapGenerator->setSiteUrl('https://www.100yuristov.com')
    ->setFileSizeLimit(5*1024*1024)
    ->setLinksPerFileLimit(30_000)
    ->createFilesFromLinksGenerator($linksGenerator, self::TMP_SITEMAPS_FOLDER);
```

### Any questions?
Feel free to ask me about my solution or report a bug by sending me an email: misha.sunsetboy@gmail.com
