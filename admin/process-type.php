<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized Access: You are not an Overseer.");
}

require_once __DIR__ . '/../data/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /admin-quiz-types");
    exit();
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$name = trim($_POST['name'] ?? '');

if ($name === '') {
    header("Location: /admin-quiz-types?status=error");
    exit();
}

try {
    $quizTypesTable = cq_quiz_types_table($pdo);
    if ($quizTypesTable === null) {
        throw new RuntimeException('Quiz types table is missing.');
    }

    $safeQuizTypesTable = cq_safe_identifier($quizTypesTable);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE `{$safeQuizTypesTable}` SET name = :name WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':id' => $id,
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO `{$safeQuizTypesTable}` (name) VALUES (:name)");
        $stmt->execute([
            ':name' => $name,
        ]);
    }

    header("Location: /admin-quiz-types?status=updated");
    exit();
} catch (Throwable $e) {
    error_log("Process Type Error: " . $e->getMessage());
    header("Location: /admin-quiz-types?status=error");
    exit();
}
