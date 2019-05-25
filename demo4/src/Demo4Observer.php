<?php

namespace MyCrawler\Demo4;

use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * 
 */
class Demo4Observer extends CrawlObserver
{
    
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ){
	    /** @var Crawler $dom_crawler */
	    $dom_crawler = (new Crawler());
	    $response->getBody()->rewind();
	    $dom_crawler->addHtmlContent( $response->getBody()->getContents());
	    dump("==> [" . ($url->getStep()) . "] Crawled " . $url, "\t" . $dom_crawler->filter('title')->text());
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ){
        dump("==| Can not crawl " . $url);
    }

}