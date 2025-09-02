<?php

require_once 'jwtMiddleware.php';

$decoded = validateJWT();

echo json_encode([
    'id' => $decoded->id,
    'role' => $decoded->role,
    'name' => $decoded->name
]);