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

Crawler::create()
    ->setDelayBetweenRequests( 1 )
    ->setConcurrency( 1 )
	->setCrawlQueue( $queue )
    ->ignoreRobots()
    ->setCrawlProfile( $profile )
    ->setCrawlObserver(new \MyCrawler\Demo5\Academia\GetKeywordsObserver())
	->setCrawlFulfilledHandlerClass( \MyCrawler\Demo5\CrawlRequestFulfilled::class )
    ->startCrawling(new \MyCrawler\Demo5\MyUri('https://www.academia.edu/Documents/in/Mathematics', ''));