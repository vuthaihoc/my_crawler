<?php

include __DIR__ . "/../vendor/autoload.php";

$content = file_get_contents('https://google.com');

dump($content);