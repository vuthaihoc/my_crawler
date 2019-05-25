<?php

include __DIR__ . "/../vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use MyCrawler\Demo4\Demo4Observer;
use Spatie\Crawler\Crawler;

$abserver = new Demo4Observer();

Crawler::create()
    ->setCrawlObserver(new Demo4Observer())
	->setCrawlFulfilledHandlerClass( \MyCrawler\Demo4\CrawlRequestFulfilled::class )
    ->startCrawling(new \MyCrawler\Demo4\MyUri('https://www.epubbooks.com/', 'root'));