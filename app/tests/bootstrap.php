<?php

require dirname(__DIR__).'/vendor/autoload.php';

$app = new \App\Application('test');
$app->init();
$app->createDatabase();
