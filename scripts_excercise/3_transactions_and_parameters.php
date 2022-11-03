<?php

use Dotenv\Dotenv;
use App\Domain\Uuid;
use Laudis\Neo4j\Basic\Driver;

require __DIR__ . '/../vendor/autoload.php';

Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();

$driver = Driver::create($_ENV['NEO4J_URI']);

$session = $driver->createSession();

$session->run('MATCH (x) DETACH DELETE x');

// Begin a transaction, create a node and roll it back

// Try to commit a rolled back transaction and catch the exception

// Verify on the session no nodes are in the database.


// Use a self-contained transaction creating a node

// Test if it is created.

// Create a node with a slug and id using parameters

// Unwind a list of slugs and create a node for each
$records = ['records' => [
    ['slug' => 'slug-a', 'id' => Uuid::generate()],
    ['slug' => 'slug-b', 'id' => Uuid::generate()]
]];

