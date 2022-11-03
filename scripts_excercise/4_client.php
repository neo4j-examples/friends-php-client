<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';


Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();

$uri = $_ENV['NEO4J_URI'];

// Create a client with 5 random drivers, specify a default app, and make them all inherit the same user agent name.


// Verify all the drivers connectivity
$aliases = [];
foreach ($aliases as $alias) {
}

// Run a query on the default driver

// Run a query on any other driver