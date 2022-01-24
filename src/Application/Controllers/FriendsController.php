<?php

namespace App\Application\Controllers;

use Laudis\Neo4j\Basic\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_values;
use function json_encode;
use function sort;
use const JSON_THROW_ON_ERROR;

final class FriendsController
{
    public function __construct(private Session $session)
    {
    }

    public function listFriends(Request $request, Response $response): Response
    {
        $users = $this->session->run(<<<'CYPHER'
        MATCH (u:User {id: $id}) - [:FriendOf] - (friend:User)
        RETURN  collect({id: friend.id, firstName: friend.firstName, secondName: friend.secondName}) AS friends
        CYPHER, $request->getQueryParams());

        $response->getBody()->write($users->getResults());

        return $response;
    }

    public function makeFriends(Request $request, Response $response): Response
    {
        $friends = array_values($request->getParsedBody());
        sort($friends);
        $result = $this->session->run(<<<'CYPHER'
        MATCH (a:User {id: $friendA}), (b:User {id: $friendB})
        MERGE (a) - [:FriendOf] -> (b)
        CYPHER, ['friendA' => $friends[0], 'friendB' => $friends[1]]);

        $response = $response->withAddedHeader('Location', '/user/friends?id=' . $friends[0]);
        if ($result->getSummary()->getCounters()->containsUpdates()) {
            return $response->withStatus(201);
        }

        return $response->withStatus(200);
    }

    public function breakupFriends(Request $request, Response $response): Response
    {
        $this->session->run(<<<'CYPHER'
        MATCH (:User {id: $friendA}) - [f:FriendOf] - (b:User {id: $friendB})
        DELETE f
        CYPHER, $request->getQueryParams());

        return $response->withStatus(204);
    }

    public function distance(Request $request, Response $response): Response
    {
        $results = $this->session->run(<<<'CYPHER'
        MATCH p = shortestPath((a:User {id: $friendA}) - [:FriendOf*1..] - (b:User {id: $friendB}))
        RETURN length(p) AS length
        LIMIT 1
        CYPHER, $request->getQueryParams());

        if ($results->isEmpty()) {
            $response->getBody()->write(json_encode(null, JSON_THROW_ON_ERROR));
        } else {
            $response->getBody()->write(json_encode($results->first()->get('length'), JSON_THROW_ON_ERROR));
        }

        return $response;
    }
}
