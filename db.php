<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Databags\SessionConfiguration;

$neo4jUri = getenv('NEO4J_URI') ?: '';
$neo4jUsername = getenv('NEO4J_USERNAME') ?: '';
$neo4jPassword = getenv('NEO4J_PASSWORD') ?: '';
$neo4jDatabase = getenv('NEO4J_DATABASE') ?: '';
$neo4jDriver = getenv('NEO4J_DRIVER') ?: 'neo4j';

if ($neo4jUri === '' || $neo4jUsername === '' || $neo4jPassword === '') {
    throw new RuntimeException('Neo4j configuration is incomplete. Set NEO4J_URI, NEO4J_USERNAME, and NEO4J_PASSWORD.');
}

if (!in_array($neo4jDriver, ['neo4j', 'bolt'], true)) {
    throw new RuntimeException('NEO4J_DRIVER must be neo4j or bolt.');
}

$builder = ClientBuilder::create()
    ->withDriver($neo4jDriver, $neo4jUri, Authenticate::basic($neo4jUsername, $neo4jPassword))
    ->withDefaultDriver($neo4jDriver);

if ($neo4jDatabase !== '') {
    $builder = $builder->withDefaultSessionConfiguration(
        SessionConfiguration::default()->withDatabase($neo4jDatabase)
    );
}

$client = $builder->build();
