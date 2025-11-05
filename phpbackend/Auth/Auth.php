<?php
require __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    protected $secret_key = "supersecretkey";

    public function generateToken($user) {
        $issuedAt = time();
        $expire = $issuedAt + 172800; // 48 hour expiration
        $payload = [
            'id' => $user['id'],
            'username' => $user['username'],
             'role' => $user['role'],
            'iat' => $issuedAt,
            'exp' => $expire
        ];

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

  


}
