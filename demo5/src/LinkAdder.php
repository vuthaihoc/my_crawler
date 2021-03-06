<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 5/24/19
 * Time: 11:37
 */

namespace MyCrawler\Demo5;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Arr;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlUrl;
use Tightenco\Collect\Support\Collection;
use Tree\Node\Node;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class LinkAdder
{
	/** @var \Spatie\Crawler\Crawler */
	protected $crawler;
	
	protected $rules = null;
//	[
//		'root' => [
//			'new_documents' => [
//				'selector' => 'nav.navbar-static-top ul.navbar-left li:nth-child(2) a',
//			]
//		],
//		'new_documents' => [
//			'new_documents' => [
//				'selector' => 'ul.pagination a[rel=next]',
//			],
//			'document_detail' => [
//				'selectors' => 'ul.media-list .media-heading a',
//			],
//		]
//	];
	
	public function __construct(Crawler $crawler)
	{
		$this->crawler = $crawler;
	}
	
	public function addFromHtml(string $html, UriInterface $foundOnUrl)
	{
        $allLinks = $this->extractLinksFromHtml($html, $foundOnUrl);
        
        collect($allLinks)
            ->filter(function (UriInterface $url) {
                return $this->hasCrawlableScheme($url);
            })
            ->map(function (UriInterface $url) {
                return $this->normalizeUrl($url);
            })
            ->filter(function (UriInterface $url) use ($foundOnUrl) {
                if (! $node = $this->crawler->addToDepthTree($url, $foundOnUrl)) {
                    return false;
                }
                
                return $this->shouldCrawl($node);
            })
            ->filter(function (UriInterface $url) {
                return strpos($url->getPath(), '/tel:') === false;
            })
            ->each(function (UriInterface $url) use ($foundOnUrl) {
                if ($this->crawler->maximumCrawlCountReached()) {
                    return;
                }
                
                $crawlUrl = CrawlUrl::create($url, $foundOnUrl);
                
                $this->crawler->addToCrawlQueue($crawlUrl);
            });
	}
	
	protected function addFromHtmlByRule(string $html, UriInterface $foundOnUrl){
        $from_step = $foundOnUrl->getStep();
        $ways = Arr::get( $this->rules, $from_step);
        
        if(!$ways){
            return;
        }
        
        foreach ($ways as $way => $way_options){
            $allLinks = $this->extractLinksFromHtmlByRules($html, $foundOnUrl, $way, $way_options);
            
            (new Collection($allLinks))
                ->filter(function (UriInterface $url) {
                    return $this->hasCrawlableScheme($url);
                })
                ->map(function (UriInterface $url) {
                    return $this->normalizeUrl($url);
                })
                ->filter(function (UriInterface $url) use ($foundOnUrl) {
                    if (! $node = $this->crawler->addToDepthTree($url, $foundOnUrl)) {
                        return false;
                    }
                    
                    return $this->shouldCrawl($node);
                })
                ->filter(function (UriInterface $url) {
                    return strpos($url->getPath(), '/tel:') === false;
                })
                ->each(function (UriInterface $url) use ($foundOnUrl) {
                    if ($this->crawler->maximumCrawlCountReached()) {
                        return;
                    }
                    
                    $crawlUrl = CrawlUrl::create($url, $foundOnUrl);
                    
                    $this->crawler->addToCrawlQueue($crawlUrl);
                });
        }
    }
	
	/**
	 * @param string $html
	 * @param \Psr\Http\Message\UriInterface $foundOnUrl
	 *
	 * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection|null
	 */
	protected function extractLinksFromHtmlByRules(string $html, UriInterface $foundOnUrl, $way, array $way_options)
	{
		$domCrawler = new DomCrawler($html, $foundOnUrl);
		if(isset( $way_options['selector'])){
			try{
				$el = $domCrawler->filter($way_options['selector'])->first()->links();
			}catch (\Exception $ex){
				$el = [];
			}
			return (new Collection($el))->map(function (Link $link) use ($way){
											try {
												return new MyUri($link->getUri(), $way);
											} catch (InvalidArgumentException $exception) {
												return;
											}
										})->filter();
		}
		if(isset( $way_options['selectors'])){
			
			try{
				$el = $domCrawler->filter($way_options['selectors'])->links();
			}catch (\Exception $ex){
				$el = [];
			}
			return (new Collection($el))->map(function (Link $link) use ($way){
											try {
												return new MyUri($link->getUri(), $way);
											} catch (InvalidArgumentException $exception) {
												return;
											}
										})->filter();
		}
		return (new Collection([]));// no link
	}
    
    /**
     * @param string $html
     * @param \Psr\Http\Message\UriInterface $foundOnUrl
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection|null
     */
    protected function extractLinksFromHtml(string $html, UriInterface $foundOnUrl)
    {
        $domCrawler = new DomCrawler($html, $foundOnUrl);
        
        return collect($domCrawler->filterXpath('//a | //link[@rel="next" or @rel="prev"]')->links())
            ->reject(function (Link $link) {
                if ($this->isInvalidHrefNode($link)) {
                    return true;
                }
                
                if ($this->crawler->mustRejectNofollowLinks() && $link->getNode()->getAttribute('rel') === 'nofollow') {
                    return true;
                }
                
                return false;
            })
            ->map(function (Link $link) {
                try {
                    return new MyUri($link->getUri(), '');
                } catch (InvalidArgumentException $exception) {
                    return;
                }
            })
            ->filter();
    }
	
	protected function hasCrawlableScheme(UriInterface $uri): bool
	{
		return in_array($uri->getScheme(), ['http', 'https']);
	}
	
	protected function normalizeUrl(UriInterface $url): UriInterface
	{
		return $url->withFragment('');
	}
	
	protected function shouldCrawl(Node $node): bool
	{
		if ($this->crawler->mustRespectRobots() && ! $this->crawler->getRobotsTxt()->allows($node->getValue())) {
			return false;
		}
		
		$maximumDepth = $this->crawler->getMaximumDepth();
		
		if (is_null($maximumDepth)) {
			return true;
		}
		
		return $node->getDepth() <= $maximumDepth;
	}
    
    protected function isInvalidHrefNode(Link $link): bool
    {
        if ($link->getNode()->nodeName !== 'a') {
            return false;
        }
        
        if ($link->getNode()->nextSibling !== null) {
            return false;
        }
        
        if ($link->getNode()->childNodes->length !== 0) {
            return false;
        }
        
        return true;
    }
}
