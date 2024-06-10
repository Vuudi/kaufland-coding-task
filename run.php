<?php

require 'vendor/autoload.php';

use App\FeedProcessor;
use App\Storage\MySQLDatabase;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$database = new MySQLDatabase();
$processor = new FeedProcessor($database);
$processor->process('feed.xml');
