<?php
require_once __DIR__ . '/../../data/database.php';

$allowedFilters = ['ALL', 'C', 'C#', 'JAVA', 'PHP'];
$requestedFilter = $_GET['lang'] ?? 'ALL';
$filter = is_string($requestedFilter) ? strtoupper(trim($requestedFilter)) : 'ALL';

if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'ALL';
}

try {
    if ($filter !== 'ALL') {
        $heroes = cq_get_language_leaderboard_rows($pdo, $filter, 50);
    } else {
        $heroes = cq_get_global_leaderboard_rows($pdo, 50);
    }
} catch (Throwable $e) {
    $heroes = [];
    error_log("Leaderboard Logic Error: " . $e->getMessage());
}
