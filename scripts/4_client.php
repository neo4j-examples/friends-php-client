<?php

use Dotenv\Dotenv;
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\Basic\Driver;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Databags\DriverConfiguration;
use Laudis\Neo4j\Databags\SessionConfiguration;

require __DIR__ . '/../vendor/autoload.php';


Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();

$driver = Driver::create($_ENV['NEO4J_URI']);

$client = ClientBuilder::create()
    ->withDriver('bolt', 'bolt://neo4j:test@localhost')
    ->withDriver('neo4j', 'neo4j://neo4j:test@localhost', Authenticate::disabled())
    ->withDriver('http', 'http://localhost?database=MyDatabase', Authenticate::basic('neo4j', 'test'))
    ->withDriver('oidc', 'bolt://localhost', Authenticate::basic('neo4j', 'test'))
    ->withDriver('default', $_ENV['NEO4J_URI'])
    ->withDefaultDriverConfiguration(DriverConfiguration::default()->withUserAgent('MyAmazingApp9000'))
    ->withDefaultDriver('default')
    ->build();


$aliases = ['bolt', 'neo4j', 'http', 'oidc', 'default'];
foreach ($aliases as $alias) {
    $driver = $client->getDriver($alias);
    echo 'Found driver with class: ' . $driver::class . PHP_EOL;
    if ($driver->verifyConnectivity()) {
        echo 'Can connect with driver: ' . $alias . PHP_EOL;
    } else {
        echo 'Cannot connect with driver: ' . $alias . PHP_EOL;
    }
}

// Runs on driver default
$result = $client->run(statement: 'RETURN 1 AS one', parameters: [], alias: 'default');