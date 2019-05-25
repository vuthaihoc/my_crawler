<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 5/24/19
 * Time: 11:28
 */

namespace MyCrawler\Demo4;


use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlerRobots;
use Spatie\Crawler\CrawlSubdomains;
use Spatie\Crawler\Handlers\CrawlRequestFulfilled as CrawlRequestFulfilledBase;

class CrawlRequestFulfilled extends CrawlRequestFulfilledBase {
	/** @var \Spatie\Crawler\Crawler */
	protected $crawler;
	
	/** @var \Spatie\Crawler\LinkAdder */
	protected $linkAdder;
	
	public function __construct(Crawler $crawler)
	{
		$this->crawler = $crawler;
		
		$this->linkAdder = new LinkAdder($this->crawler);
	}
	
	public function __invoke(ResponseInterface $response, $index)
	{
		$robots = new CrawlerRobots($response, $this->crawler->mustRespectRobots());
		
		$crawlUrl = $this->crawler->getCrawlQueue()->getUrlById($index);
		
		if ($this->crawler->mayExecuteJavaScript()) {
			$html = $this->getBodyAfterExecutingJavaScript($crawlUrl->url);
			
			$response = $response->withBody(stream_for($html));
		}
		
		if ($robots->mayIndex()) {
			$this->handleCrawled($response, $crawlUrl);
		}
		
		if (! $this->crawler->getCrawlProfile() instanceof CrawlSubdomains) {
			if ($crawlUrl->url->getHost() !== $this->crawler->getBaseUrl()->getHost()) {
				return;
			}
		}
		
		if (! $robots->mayFollow()) {
			return;
		}
		
		$body = $this->convertBodyToString($response->getBody(), $this->crawler->getMaximumResponseSize());
		
		$this->linkAdder->addFromHtml($body, $crawlUrl->url);
		
		usleep($this->crawler->getDelayBetweenRequests());
	}
	
	protected function handleCrawled(ResponseInterface $response, \Spatie\Crawler\CrawlUrl $crawlUrl)
	{
		$this->crawler->getCrawlObservers()->crawled($crawlUrl, $response);
	}
	
	protected function convertBodyToString(StreamInterface $bodyStream, $readMaximumBytes = 1024 * 1024 * 2): string
	{
		$bodyStream->rewind();
		
		$body = $bodyStream->read($readMaximumBytes);
		
		return $body;
	}
	
	protected function getBodyAfterExecutingJavaScript(UriInterface $url): string
	{
		$browsershot = $this->crawler->getBrowsershot();
		
		$html = $browsershot->setUrl((string) $url)->bodyHtml();
		
		return html_entity_decode($html);
	}
}