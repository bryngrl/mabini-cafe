<?php
require_once __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


function validateJWT() {
    $token = $_COOKIE['token'] ?? '';

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Login required']);
        exit;
    }

    try {
        return JWT::decode($token, new Key('supersecretkey', 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired token']);
        exit;
    }
}
function isAdminAuthorized($decoded)
{
    return ($decoded && isset($decoded->role) && $decoded->role === 'admin');
}


