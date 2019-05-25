<?php

namespace MyCrawler\Demo3;

use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * 
 */
class Demo3Observer extends CrawlObserver
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
	    $title = trim($dom_crawler->filter('title')->text());
	    echo"==> Crawled " . $url . "\n |_ Title : " . $title . "\n";
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ){
        dump("=========!!! Can not crawl " . $url . "========");
    }

}