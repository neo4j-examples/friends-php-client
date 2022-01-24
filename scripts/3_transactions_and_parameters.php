<?php

use Dotenv\Dotenv;
use Neo4j\FriendsApiExample\Uuid;
use Laudis\Neo4j\Basic\Driver;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\SummarizedResult;
use Laudis\Neo4j\Exception\Neo4jException;
use Laudis\Neo4j\ParameterHelper;

require __DIR__ . '/../vendor/autoload.php';


Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
$driver = Driver::create($_ENV['NEO4J_URI']);

$session = $driver->createSession();

$session->run('MATCH (x) DETACH DELETE x');

$tsx = $session->beginTransaction();
$tsx->run('CREATE (x:Node)');
$tsx->rollback();

try {
    $tsx->commit();
} catch (Neo4jException $e) {
    // This gets improved in the next minor version release
    echo $e->getMessage() . PHP_EOL;
}

if ($session->run('MATCH (x:Node) RETURN x')->isEmpty()) {
    echo 'Transaction successfully rolled back' . PHP_EOL;
}


/** @var SummarizedResult $results */
$results = $session->transaction(static function (TransactionInterface $tsx) {
    $test = 'abc';
    return $tsx->run('CREATE (x:Node {test: "'. $test .'"}) RETURN x');
});

$node = $results->getAsCypherMap(0)->getAsNode(0);
if ($node->getProperties()->isEmpty() && !$session->run('MATCH (x:Node) RETURN x')->isEmpty()) {
    echo 'Node sucessfully created' . PHP_EOL;
}

$session->run('CREATE (x:NodeIdentifier {test: $test}) RETURN x', ['test' => 'abc']);

$session->run(<<<'CYPHER'
MATCH (x:NodeIdentifier {slug: $slug})
SET x.id = $id
CYPHER, ['slug' => 'test-slug', 'id' => Uuid::generate()]);

$session->run(<<<'CYPHER'
UNWIND $records as mapping
CREATE (x:NodeIdentifier {slug: mapping['slug'], id: mapping['id']})
CYPHER, ['records' => [
    ['slug' => 'slug-a', 'id' => Uuid::generate()],
    ['slug' => 'slug-b', 'id' => Uuid::generate()]
]]);

$session->run(<<<'CYPHER'
UNWIND $mappings as mapping
CREATE (x:NodeIdentifier {slug: mapping['slug'], id: mapping['id']})
CYPHER, ['mappings' => ParameterHelper::asList([])]);

