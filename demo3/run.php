<?php

include __DIR__ . "/../vendor/autoload.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use MyCrawler\Demo3\Demo3Observer;
use Spatie\Crawler\Crawler;

$abserver = new Demo3Observer();

Crawler::create()
    ->setCrawlObserver(new Demo3Observer())
    ->startCrawling('https://www.youtube.com');