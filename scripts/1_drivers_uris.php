<?php

use Dotenv\Dotenv;
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\Basic\Driver;
use Laudis\Neo4j\Databags\DriverConfiguration;

require __DIR__ . '/../vendor/autoload.php';

Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();

// This is the standard way to construct a driver
// Load the uri with credentials in the server environment
// and pass them to the driver to dynamically create the correct driver
$driver = Driver::create($_ENV['NEO4J_URI']);

// Creates a bolt driver with credentials neo4j and password test
// with the default port of 7687
$boltDriver = Driver::create('bolt://neo4j:test@localhost');

// Creates an auto routed driver with credentials neo4j and password test
// with the custom port 7777
$neo4jDriver = Driver::create(uri: 'neo4j://localhost:7777', authenticate: Authenticate::disabled());

// Creates a http driver with credentials neo4j and password test on the default port 7474
// and on database MyDatabase
$http = Driver::create(uri: 'http://localhost?database=MyDatabase', authenticate: Authenticate::basic('neo4j', 'test'));

// Creates a bolt driver with disabled authentication on default port 7687
// and with custom user agent MyAmazingApp 9000
$oidcDriver = Driver::create(
    uri: 'bolt://localhost',
    configuration: DriverConfiguration::default()->withUserAgent('MyAmazingApp/9000'),
    authenticate: Authenticate::disabled()
);

echo 'Created driver' . PHP_EOL;
$session = $driver->createSession();

echo 'Created session' . PHP_EOL;
if ($driver->verifyConnectivity()) {
    echo 'Can connect to database' . PHP_EOL;
} else {
    echo 'Cannot connect to database' . PHP_EOL;
}