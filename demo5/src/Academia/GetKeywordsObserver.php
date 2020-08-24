<?php


namespace MyCrawler\Demo5\Academia;


use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler;

class GetKeywordsObserver extends CrawlObserver{
    
    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled( UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null ) {
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
    
    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed( UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null ) {
        dump("==| Can not crawl " . $url);
        dump( "==| EEEEEEEE " . $requestException->getMessage() );
    }
}