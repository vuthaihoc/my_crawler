<?php

namespace MyCrawler\Demo5;

use Spatie\Crawler\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * 
 */
class Demo5Observer extends CrawlObserver
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
	    try{
            dump("==> [" . ($url->getStep()) . "] Crawled " . $url, "\t" . $dom_crawler->filter('title')->text());
        }catch (\Exception $ex){
	        dump( "Error " . $ex->getMessage() );
        }
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ){
        dump("==| Can not crawl " . $url);
    }

}