# Neo4j PHP Friends API

This project is an example of how to create a JSON API with Neo4j and PHP

Please refer to the OpenAPI definition at: https://app.swaggerhub.com/apis-docs/Laudis/neo4j-php-friends-api/dev-main.

The live stream using the API can be found here: https://www.youtube.com/watch?v=qwz5XVtbfSY


## Requirements

- PHP 8.2.35+, 8.3.36+, 8.4.27+ or 8.5.12+ with the `curl`, `json` and `mbstring` extensions. Earlier patch releases are vulnerable to CVE-2026-109618 and CVE-2026-109619, and `composer install` refuses to run on them.
- Composer 2
- A Neo4j database (tested with Neo4j 4.4 and 5)


## Running the server

1. Run in cli: `composer install`
2. Create a .env based on the .env.example file. `NEO4J_URI` accepts the `bolt`, `bolt+s`, `bolt+ssc`, `neo4j`, `neo4j+s` and `neo4j+ssc` schemes, e.g. `bolt://neo4j:test@localhost`. Use `bolt://` for a single server and `neo4j://` for a cluster or Aura. Without a .env file the app connects to `bolt://neo4j:test@localhost`.
3. Run in cli: `composer start`. The API is served on http://localhost:8080.

Alternatively, run `composer install` and then `docker compose up` to serve the API on http://localhost:8080 with PHP 8.4. Inside the container `localhost` is the container itself, so `NEO4J_URI` must point to a host the container can reach.


## Running the tests

The tests need a running Neo4j database. They use the `NEO4J_URI` from the .env file, or `bolt://neo4j:test@localhost` without one.

```
composer install
vendor/bin/phpunit
vendor/bin/phpstan
vendor/bin/phpcs
```


## Basic CLI examples

The scripts/ folder also includes examples on how the driver and and client works. Run them with e.g. `php scripts/1_drivers_uris.php`; they read `NEO4J_URI` from the .env file.
