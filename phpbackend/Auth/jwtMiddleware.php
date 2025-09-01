<?php
require_once '../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

function validateJWT() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(['error' => 'Token required']);
        exit;
    }

    $token = str_replace('Bearer ', '', $authHeader);

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


