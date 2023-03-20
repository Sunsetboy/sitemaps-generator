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
My solution takes any number of links provided by you and packs them into XML files up to 50000 links each 
(sitemap_1.xml, sitemap_2.xml, etc). It also creates an index sitemap file sitemap.xml. 

After generation of sitemap files you can add index sitemap to the search engine using tools like Google Webmaster.
You can also add to your robots.txt file:
```
Sitemap: http://yoursite.com/sitemap.xml 
```

### Requirements
PHP 7.3+

### Installation
You can install this package using Composer.
```
composer require yurcrm/sitemap-generator
```

### Usage example
```
$sitemapGenerator = new SitemapGenerator();
// your website URL
$siteUrl = 'http://example.com';

/*
*   You should generate this array of links according to your website content
*/
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

$sitemapGenerator->setSiteUrl($siteUrl)
->setLinks($links)
->createSitemaps();

// save files to the folder (use absolute path) 
$sitemapGenerator->saveAsFiles('/var/www/site');
```

### Any questions?
Feel free to ask me about my solution or report a bug by sending me an email: misha.sunsetboy@gmail.com
