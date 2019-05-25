<?php

namespace MyCrawler\Demo2;

use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * 
 */
class Demo2Observer extends CrawlObserver
{
    
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ){
        dump("==> Crawled Crawled " . $url);
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ){
        dump("==| Can not crawl " . $url);
    }

}