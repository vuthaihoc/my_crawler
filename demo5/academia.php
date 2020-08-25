<?php

include __DIR__ . "/../vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use MyCrawler\Demo5\Demo5Observer;
use Spatie\Crawler\Crawler;

$abserver = new Demo5Observer();
$profile = new \MyCrawler\Demo5\Academia\KeywordsPageOnlyProfile();
$queue = new \MyCrawler\Demo5\CrawlSqliteQueue();

$queue->init(true);

Crawler::create([
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1 (compatible; AdsBot-Google-Mobile; +http://www.google.com/mobile/adsbot.html)'
    ]
])
    ->setDelayBetweenRequests( 1 )
    ->setConcurrency( 2 )
	->setCrawlQueue( $queue )
    ->ignoreRobots()
    ->setCrawlProfile( $profile )
    ->setCrawlObserver(new \MyCrawler\Demo5\Academia\GetKeywordsObserver())
	->setCrawlFulfilledHandlerClass( \MyCrawler\Demo5\CrawlRequestFulfilled::class )
    ->startCrawling(new \MyCrawler\Demo5\MyUri('https://www.academia.edu/Documents/in/Mathematics', ''));