<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/db.php';

function getUserInfo(string $username)
{
    global $client;

    $query = 'MATCH (u:User {username: $username}) RETURN u LIMIT 1';
    $result = $client->readTransaction(static function ($tsx) use ($query, $username) {
        return $tsx->run($query, ['username' => $username]);
    });

    if ($result->isEmpty()) {
        return false;
    }

    return $result->first()->get('u');
}

function updateUserProfile(string $username, array $newProfileData): bool
{
    global $client;

    $allowedFields = ['display_name', 'bio', 'latitude', 'longitude'];
    $profileData = array_intersect_key($newProfileData, array_flip($allowedFields));
    if ($profileData === []) {
        return false;
    }

    $query = 'MATCH (u:User {username: $username}) SET u += $profileData RETURN u';
    $params = ['username' => $username, 'profileData' => $profileData];

    $result = $client->writeTransaction(static function ($tsx) use ($query, $params) {
        return $tsx->run($query, $params);
    });

    return !$result->isEmpty();
}

function registerUser(string $username, string $password): array
{
    global $client;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = <<<'CYPHER'
MERGE (u:User {username: $username})
ON CREATE SET u.password = $hashedPassword, u.created_at = timestamp()
RETURN u.username AS username, u.password = $hashedPassword AS created
CYPHER;

    $result = $client->writeTransaction(static function ($tsx) use ($query, $username, $hashedPassword) {
        return $tsx->run($query, [
            'username' => $username,
            'hashedPassword' => $hashedPassword,
        ]);
    });

    $record = $result->first();
    if ($record === null || !$record->get('created')) {
        return ['success' => false, 'message' => 'That username is already registered.'];
    }

    return ['success' => true, 'username' => $username];
}

function loginUser(string $username, string $password): array
{
    $userInfo = getUserInfo($username);
    if ($userInfo === false) {
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }

    $hashedPassword = (string) $userInfo->getProperty('password');
    if (!password_verify($password, $hashedPassword)) {
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }

    return ['success' => true, 'username' => $username];
}
