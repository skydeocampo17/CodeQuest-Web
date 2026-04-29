<?php
require_once __DIR__ . '/../../data/database.php';

// 1. Get filter and handle the C# URL encoding trap
$filter = $_GET['lang'] ?? 'ALL';

try {
    if ($filter !== 'ALL') {
        $heroes = cq_get_language_leaderboard_rows($pdo, $filter, 50);
    } else {
        $heroes = cq_get_global_leaderboard_rows($pdo, 50);
    }
} catch (PDOException $e) {
    $heroes = [];
    error_log("Database Error: " . $e->getMessage()); 
}
