<?php
require 'vendor/autoload.php';

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;

// Create a client with authentication
$client = ClientBuilder::create()
    ->withDriver('https', 'neo4j+s://99eb7a68.databases.neo4j.io', Authenticate::basic('neo4j', 'hLdjWFd5vwHjXgaToFkIKMuGGCvyozCHTbZl7-l4rgQ')) 
    // creates an HTTP driver with basic authentication
    ->withDefaultDriver('https') // Set the default driver to HTTP
    ->build();

// Now you can use $client to interact with your Neo4j database
?>
