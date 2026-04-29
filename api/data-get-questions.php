<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit;

require_once __DIR__ . '/../data/database.php';

try {
    $language_id = isset($_GET["language"]) ? intval($_GET["language"]) : 1;
    $level_id = isset($_GET["level_id"]) ? intval($_GET["level_id"]) : null;
    $quiz_id = isset($_GET["quiz_id"]) ? intval($_GET["quiz_id"]) : null;

    $sql = "SELECT id, quiz_id, level_id, question_text, correct_answer, wrong_1, wrong_2, wrong_3
            FROM questions
            WHERE language_id = :language_id";

    $params = [
        ":language_id" => $language_id
    ];

    if ($level_id !== null) {
        $sql .= " AND level_id = :level_id";
        $params[":level_id"] = $level_id;
    }

    if ($quiz_id !== null) {
        $sql .= " AND quiz_id = :quiz_id";
        $params[":quiz_id"] = $quiz_id;
    }

    $sql .= " ORDER BY RAND()";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($questions);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "The Royal Library is disorganized: " . $e->getMessage()
    ]);
}
?>