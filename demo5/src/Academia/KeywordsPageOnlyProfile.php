<?php


namespace MyCrawler\Demo5\Academia;


use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfile;

class KeywordsPageOnlyProfile extends CrawlProfile {
    
    /**
     * Determine if the given url should be crawled.
     *
     * @param \Psr\Http\Message\UriInterface $url
     *
     * @return bool
     */
    public function shouldCrawl( UriInterface $url ): bool {
        return mb_strpos($url->__toString(), "academia.edu/Documents/in/") !== false;
    }
}