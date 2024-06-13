<?php
require_once 'db.php';

function redirect_to_https() {
    if (!is_local()) {
        if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirect);
            exit();
        }
    }
}

function custom_header() {
    if (!is_local()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function hashToken($token) {
    return password_hash($token, PASSWORD_BCRYPT);
}

function verifyToken($token, $hash) {
    return password_verify($token, $hash);
}
use Laudis\Neo4j\Databags\Statement;

function getUserInfo($userid) {
    global $client;

    $query = 'MATCH (u:User {id: $userid}) RETURN u';
    $result = $client->readTransaction(static function ($tsx) use ($query, $userid) {
        return $tsx->run($query, ['userid' => $userid]);
    });

    // Check if any records were returned
    if ($result->isEmpty()) {
        return false;
    }

    return $result->first()->get('u');
}

function updateUserProfile($userid, $newProfileData) {
    global $client;

    $query = 'MATCH (u:User {id: $userid}) SET u = $newProfileData RETURN u';
    $params = ['userid' => $userid, 'newProfileData' => $newProfileData];

    $client->writeTransaction(static function ($tsx) use ($query, $params) {
        $tsx->run($query, $params);
    });

    return true;
}

function registerUser($username, $password) {
    global $client;

    if (getUserInfo($username)) {
        return 'User already exists.';
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $query = 'CREATE (u:User {username: $username, password: $hashedPassword})';
    $client->writeTransaction(static function ($tsx) use ($query, $username, $hashedPassword) {
        $tsx->run($query, ['username' => $username, 'hashedPassword' => $hashedPassword]);
    });

    return 'User registered successfully.';
}

function loginUser($username, $password) {
    global $client;

    $userInfo = getUserInfo($username);
    if (!$userInfo) {
        return 'User does not exist.';
    }

    $hashedPassword = $userInfo->get('password');
    if (!password_verify($password, $hashedPassword)) {
        return 'Incorrect password.';
    }

    $token = createAuthToken($username);
    return $token;
}

function createAuthToken($userid) {
    global $client;

    $token = generateSecureToken();
    $hashedToken = hashToken($token);
    $expiryTime = time() + 1800;

    $query = 'CREATE (t:AuthToken {token: $token, userid: $userid, created_at: timestamp(), expires_at: $expiryTime}) RETURN t';
    $params = ['token' => $hashedToken, 'userid' => $userid, 'expiryTime' => $expiryTime];

    $client->writeTransaction(static function ($tsx) use ($query, $params) {
        $tsx->run($query, $params);
    });

    return $token;
}

function validateAuthToken($token) {
    global $client;

    $query = 'MATCH (t:AuthToken) WHERE t.token = $token AND t.expires_at > timestamp() RETURN t.token, t.userid';
    $result = $client->readTransaction(static function ($tsx) use ($query, $token) {
        return $tsx->run($query, ['token' => $token]);
    });

    if (!$result->first()) {
        return false;
    }

    foreach ($result->records() as $record) {
        $authToken = $record->get('t');
        if (!verifyToken($token, $authToken->value('token'))) {
            return false;
        }
        return $authToken->value('userid');
    }
}

function blacklistToken($token) {
    global $client;

    $query = 'MATCH (t:AuthToken {token: $token}) DETACH DELETE t';
    $client->writeTransaction(static function ($tsx) use ($query, $token) {
        $tsx->run($query, ['token' => $token]);
    });
}

?>