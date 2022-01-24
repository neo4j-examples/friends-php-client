<?php

use Dotenv\Dotenv;
use Laudis\Neo4j\Basic\Driver;
use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Databags\SummarizedResult;

require __DIR__ . '/../vendor/autoload.php';


// Basic driver construction
Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
$driver = Driver::create($_ENV['NEO4J_URI']);
echo 'Created driver' . PHP_EOL;

// A session is a lightweight object that requests and works with connections
$session = $driver->createSession();

// A result always returns a "SummarizedResult"
// A summarized holds a "CypherMap" for each row in the result set
$result = $session->run('RETURN 1 AS one');
echo $result->first()->get('one') . PHP_EOL;

// Better IDE integration
// IDE Integration gets improved by explicitly requesting the type of object you expect
$result = $session->run('RETURN 1 AS one');
echo $result->getAsMap(0)->getAsInt('one') . PHP_EOL;

// Using HEREDOC can help with readability for longer queries
$result = $session->run(<<<'CYPHER'
CREATE (user:User {name: 'Tom'})
RETURN user
CYPHER);
// A result summary hold multiple pieces of meta information for easy digestion.
summarise($result);


$result = $session->run(<<<'CYPHER'
MATCH (user:User {name: 'Tom'})
DELETE user
CYPHER);

summarise($result);

// Working with multiple statements at once is especially beneficial when working with the HTTP protocol
// The SummarizedResults are no first wrapped in a CypherList
$results = $session->runStatements([
    Statement::create('MATCH (x) RETURN x'),
    Statement::create(<<<'CYPHER'
        UNWIND ['tom', 'bart', 'lindsey'] AS name
        CREATE (:User {name: name})
        CYPHER
    ),
    Statement::create('MATCH (x) RETURN x')
]);

foreach ($results as $result) {
    summarise($result);
}

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