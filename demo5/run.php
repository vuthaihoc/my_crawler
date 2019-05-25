<?php

include __DIR__ . "/../vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use MyCrawler\Demo5\Demo5Observer;
use Spatie\Crawler\Crawler;

$abserver = new Demo5Observer();

Crawler::create()
	->setCrawlQueue( new \MyCrawler\Demo5\CrawlSqliteQueue())
    ->setCrawlObserver(new Demo5Observer())
	->setCrawlFulfilledHandlerClass( \MyCrawler\Demo5\CrawlRequestFulfilled::class )
    ->startCrawling(new \MyCrawler\Demo5\MyUri('https://www.epubbooks.com/', 'root'));