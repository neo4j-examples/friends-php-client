<?php

use Dotenv\Dotenv;
use Laudis\Neo4j\Databags\SummarizedResult;

require __DIR__ . '/../vendor/autoload.php';


Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();

$uri = $_ENV['NEO4J_URI'];

// Create a basic driver

// Create a session do run queries on

// Return any result on Neo4j and echo its

// Improve it with better IDE integration

// Use a more complex query and use multiline with NOWDOC creating a user named Tom

// Use the summarize helper on the result

// Delete the user named Tom

// Use the summarize helper on the result

// Run some multiple statements at once and summarize them

function summarise(SummarizedResult $result): void
{
    $summary = $result->getSummary();
    $counters = $summary->getCounters();
    echo PHP_EOL . '--------------------------------------' . PHP_EOL;
    echo 'Ran query: ' . PHP_EOL . $summary->getStatement()->getText() . PHP_EOL.PHP_EOL;
    echo 'Results available after (in ms): ' . round(1000 * $summary->getResultAvailableAfter()) . PHP_EOL;
    echo 'Query was of type: ' . $summary->getQueryType() . PHP_EOL;
    echo 'Query was run on type: ' . $summary->getServerInfo()->getAddress() . PHP_EOL;

    echo 'Created ' . $counters->nodesCreated() . ' nodes'. PHP_EOL;
    echo 'Deleted ' . $counters->nodesDeleted() . ' nodes'. PHP_EOL;
    echo '--------------------------------------' . PHP_EOL . PHP_EOL;
}