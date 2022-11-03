<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();

$uri = $_ENV['NEO4J_URI'];

// Create a standard driver using the uri.

// Creates a bolt driver with credentials neo4j and password test
// with the default port of 7687

// Create an auto routed driver with credentials neo4j and password test
// with the custom port 7777

// Creates a http driver with credentials neo4j and password test on the default port 7474
// and on database MyDatabase

// Creates a bolt driver with disabled authentication on default port 7687
// and with custom user agent MyAmazingApp 9000


// Verify the connectivity on the driver

// Create a session on the working driver
