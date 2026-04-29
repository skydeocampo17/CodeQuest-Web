<?php
require_once __DIR__ . '/../../data/database.php';

header('Content-Type: application/json');

$offset = $_GET['offset'] ?? 0;
$limit = 10;
$lang = $_GET['lang'] ?? 'ALL';

try {
    $activities = cq_get_social_feed_rows($pdo, (string) $lang, (int) $limit, (int) $offset);

    // Return as JSON for the JavaScript to inject into the feed
    echo json_encode($activities);

} catch (Throwable $e) {
    http_response_code(500);
    // You can check your browser's network tab to see this exact error if it fails
    echo json_encode(['error' => 'The royal scribes failed to fetch the logs: ' . $e->getMessage()]);
}
