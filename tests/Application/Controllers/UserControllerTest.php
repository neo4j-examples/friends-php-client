<?php

namespace Tests\Application\Controllers;

use Laudis\Neo4j\Basic\Session;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;
use function json_decode;

class UserControllerTest extends TestCase
{
    public function testListUsers(): void
    {
        $this->getAppInstance()->getContainer()->get(Session::class)->run('MATCH (x) DETACH DELETE (x)');

        $request = $this->createRequest('GET', '/users');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonBody([], $response);
    }

    /**
     * @depends testListUsers
     */
    public function testCreateUser(): void
    {
        $request = $this->createRequest('POST', '/user')->withParsedBody([
            'firstName' => 'a',
            'secondName' => 'b'
        ]);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
        $request = $this->createRequest('POST', '/user')->withParsedBody([
            'firstName' => 'c',
            'secondName' => 'd'
        ]);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @depends testCreateUser
     */
    public function testUsersCreated(): void
    {
        $request = $this->createRequest('GET', '/users');
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonBody(
            [['firstName' => 'a', 'secondName' => 'b'], ['firstName' => 'c', 'secondName' => 'd']],
            $response,
            static function (array &$x) {
                foreach ($x as &$y) {
                    unset($y['id']);
                }

                return $x;
            }
        );
    }

    /**
     * @depends testUsersCreated
     */
    public function testDeleteUser(): void
    {
        $id = $this->fetchId();

        $request = $this->createRequest('DELETE', '/user')->withQueryParams(['id' => $id]);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @depends testDeleteUser
     */
    public function test404(): void
    {
        $request = $this->createRequest('GET', '/user')->withQueryParams(['id' => 'abc']);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @depends test404
     */
    public function testActualUser(): void
    {
        $id = $this->fetchId();
        $request = $this->createRequest('GET', '/user')->withQueryParams(['id' => $id]);
        $response = $this->getAppInstance()->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonBody(['firstName' => 'c', 'secondName' => 'd', 'id' => $id], $response);
    }


    private function assertJsonBody(mixed $expected, ResponseInterface $response, $transformer = null): void
    {
        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();
        $body = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        $transformer ??= static fn ($x) => $x;
        $this->assertEqualsCanonicalizing($expected, $transformer($body));
    }

    private function fetchId(): string
    {
        $request = $this->createRequest('GET', '/users');
        $response = $this->getAppInstance()->handle($request);

        $response->getBody()->rewind();
        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $body[0]['id'];
    }
}
