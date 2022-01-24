<?php

namespace App\Application\Controllers;

use Laudis\Neo4j\Basic\Session;
use Neo4j\Domain\Uuid;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use function json_encode;

final class UserController
{
    public function __construct(private Session $session)
    {
    }

    public function listUsers(Request $request, Response $response): Response
    {
        $users = $this->session->run(<<<'CYPHER'
        MATCH (u:User)
        RETURN  u.firstName AS firstname,
                u.secondName AS secondName,
                u.id AS id
        CYPHER);

        $response->getBody()->write(json_encode($users->getResults(), JSON_THROW_ON_ERROR));

        return $response;
    }

    public function getUser(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();
        $user = $this->session->run(<<<'CYPHER'
        MATCH (u:User {id: $id})
        RETURN  u.firstName AS firstname,
                u.secondName AS secondName,
                u.id AS id
        CYPHER, $query);

        if ($user->isEmpty()) {
            throw new HttpNotFoundException($request, sprintf('Cannot find User with id: %s', $query['id']));
        }

        $response->getBody()->write(json_encode($user->first(), JSON_THROW_ON_ERROR));

        return $response;
    }

    public function createUser(Request $request, Response $response): Response
    {
        $id = Uuid::generate();
        /** @var array */
        $parsedBody = $request->getParsedBody();
        $parsedBody['id'] = $id;

        $this->session->run(<<<'CYPHER'
        CREATE (u:User {id: $id, firstName: $firstName, secondName: $secondName})
        CYPHER, $parsedBody);

        return $response->withStatus(201)
            ->withHeader('Location', '/user?id=' . $id);
    }

    public function deleteUser(Request $request, Response $response): Response
    {
        $this->session->run(<<<'CYPHER'
        MATCH (u:User {id: $id})
        DETACH DELETE u
        CYPHER, $request->getQueryParams());

        return $response->withStatus(204);
    }
}