<?php

include __DIR__ . "/../vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Spatie\Crawler\Crawler;
use MyCrawler\Demo2\Demo2Observer;

$observer = new Demo2Observer();

Crawler::create()
	->doNotExecuteJavaScript()
	->setCrawlObserver($observer)
    ->startCrawling('https://www.youtube.com');