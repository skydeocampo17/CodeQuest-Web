<?php

require_once __DIR__ . '/../bootstrap/app.php';

// Legacy scripts still expect a ready-to-use `$pdo` after requiring this file.
$connection = config('database.connections.mysql', []);
$host = $connection['host'] ?? 'localhost';
$port = $connection['port'] ?? '3306';
$db = $connection['database'] ?? '';
$user = $connection['username'] ?? '';
$pass = $connection['password'] ?? '';
$charset = $connection['charset'] ?? 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection failed.',
    ]);
    exit();
}
