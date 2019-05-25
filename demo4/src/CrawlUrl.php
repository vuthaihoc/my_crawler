<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 5/24/19
 * Time: 11:25
 */

namespace MyCrawler\Demo4;


use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlUrl as CrawlUrlBase;

class CrawlUrl extends CrawlUrlBase {
	
	public static function create(UriInterface $url, ?UriInterface $foundOnUrl = null, $id = null)
	{
		$static = new static($url, $foundOnUrl);
		
		if ($id !== null) {
			$static->setId($id);
		}
		
		return $static;
	}
	
	protected function __construct(UriInterface $url, $foundOnUrl = null)
	{
		$this->url = $url;
		$this->foundOnUrl = $foundOnUrl;
	}
	
}