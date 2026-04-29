<?php
// 1. Start Session & Enable Error Reporting (Temporary for debugging)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized Access: You are not an Overseer.");
}

// 3. Robust Path Loading for data-config.php
// We try to find the config file relative to this script
$configPath = __DIR__ . '/../data/database.php'; 
if (!file_exists($configPath)) {
    // If it's not one level up, try two levels up
    $configPath = __DIR__ . '/../../data/database.php';
}

if (file_exists($configPath)) {
    require_once $configPath;
} else {
    die("The Forge is cold: Database config not found at " . $configPath);
}

// 4. Handle the Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $language_id = $_POST['language_id'] ?? null;
    $quiz_id = $_POST['quiz_id'] ?? null;
    $level_id = $_POST['level_id'] ?? 1;
    $question_text = trim($_POST['question_text'] ?? '');
    $correct_answer = trim($_POST['correct_answer'] ?? ($_POST['correct_answer_tf'] ?? ''));

    if (!$language_id || !$quiz_id || !$level_id || $question_text === '' || $correct_answer === '') {
        die("Forge Error: Missing required quest fields.");
    }
    
    // Default wrong answers
    $w1 = 'N/A';
    $w2 = 'N/A';
    $w3 = 'N/A';

    // Only process wrong answers for Multiple Choice (ID 1)
    if ($quiz_id == "1") {
        $w1 = trim($_POST['wrong_1'] ?? 'N/A');
        $w2 = trim($_POST['wrong_2'] ?? 'N/A');
        $w3 = trim($_POST['wrong_3'] ?? 'N/A');
    }

    try {
        if (!empty($id)) {
            $sql = "UPDATE questions SET 
                    quiz_id = :quiz_id,
                    language_id = :language_id,
                    level_id = :level_id,
                    question_text = :text,
                    correct_answer = :correct,
                    wrong_1 = :w1,
                    wrong_2 = :w2,
                    wrong_3 = :w3
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':quiz_id'     => $quiz_id,
                ':language_id' => $language_id,
                ':level_id'    => $level_id,
                ':text'        => $question_text,
                ':correct'     => $correct_answer,
                ':w1'          => $w1,
                ':w2'          => $w2,
                ':w3'          => $w3,
                ':id'          => $id
            ]);
            $redirectId = $id;
        } else {
            $insertSql = "INSERT INTO questions (
                    language_id,
                    quiz_id,
                    level_id,
                    question_text,
                    correct_answer,
                    wrong_1,
                    wrong_2,
                    wrong_3
                ) VALUES (
                    :language_id,
                    :quiz_id,
                    :level_id,
                    :text,
                    :correct,
                    :w1,
                    :w2,
                    :w3
                )";

            $stmt = $pdo->prepare($insertSql);
            $result = $stmt->execute([
                ':language_id' => $language_id,
                ':quiz_id'     => $quiz_id,
                ':level_id'    => $level_id,
                ':text'        => $question_text,
                ':correct'     => $correct_answer,
                ':w1'          => $w1,
                ':w2'          => $w2,
                ':w3'          => $w3,
            ]);
            $redirectId = $pdo->lastInsertId();
        }

        if ($result) {
            header("Location: /admin-quests?status=updated&id=" . urlencode((string) $redirectId));
            exit();
        } else {
            throw new Exception("Update failed in the database.");
        }

    } catch (Exception $e) {
        die("Forge Error: " . $e->getMessage());
    }
} else {
    header("Location: /admin-quests");
    exit();
}
