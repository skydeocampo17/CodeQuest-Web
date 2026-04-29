<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized.");
}

require_once __DIR__ . '/../data/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: /admin-quests?status=deleted");
            exit();
        } catch (PDOException $e) {
            header("Location: /admin-quests?status=error");
            exit();
        }
    }
}
header("Location: /admin-quests");
