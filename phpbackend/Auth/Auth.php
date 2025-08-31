<?php
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private $secret_key = "supersecretkey";

    public function generateToken($user) {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // 1 hour expiration
        $payload = [
            'id' => $user['id'],
            'username' => $user['username'],
            'iat' => $issuedAt,
            'exp' => $expire
        ];
        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    public function verifyToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return (array)$decoded;
        } catch(Exception $e){
            return false;
        }
    }
}
