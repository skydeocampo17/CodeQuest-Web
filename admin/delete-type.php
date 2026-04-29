<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized Access: You are not an Overseer.");
}

require_once __DIR__ . '/../data/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: /admin-quiz-types?status=error");
    exit();
}

try {
    $quizTypesTable = cq_quiz_types_table($pdo);
    if ($quizTypesTable === null) {
        throw new RuntimeException('Quiz types table is missing.');
    }

    $safeQuizTypesTable = cq_safe_identifier($quizTypesTable);
    $stmt = $pdo->prepare("DELETE FROM `{$safeQuizTypesTable}` WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: /admin-quiz-types?status=deleted");
    exit();
} catch (Throwable $e) {
    error_log("Delete Type Error: " . $e->getMessage());
    header("Location: /admin-quiz-types?status=error");
    exit();
}
