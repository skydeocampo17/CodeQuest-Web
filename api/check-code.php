<?php
header("Content-Type: application/json");

require_once __DIR__ . "/groq-config.php";

$data = json_decode(file_get_contents("php://input"), true);

$question = $data["question"] ?? "";
$code = $data["code"] ?? "";

if (empty($question) || empty($code)) {
    echo json_encode(["error" => "Missing question or code"]);
    exit();
}

if (empty($GROQ_API_KEY)) {
    http_response_code(500);
    echo json_encode(["error" => "Groq API key is missing."]);
    exit();
}

$url = "https://api.groq.com/openai/v1/chat/completions";

$prompt = <<<PROMPT
You are a C programming judge for a beginner-friendly game.

Question:
$question

Player Code:
$code

Rules:
- ONLY check if the function does what the question asks
- IGNORE minor style issues
- IGNORE printf newline differences unless explicitly required
- Function name, parameters, and logic must match
- Must be valid C syntax
- If correct, feedback should be short
- If wrong, explain the main issue briefly

Return ONLY JSON:
{
  "is_correct": true,
  "feedback": "..."
}
PROMPT;

$payload = [
    "model" => "llama-3.1-8b-instant",
    "messages" => [
        ["role" => "system", "content" => "Return only valid JSON."],
        ["role" => "user", "content" => $prompt]
    ],
    "temperature" => 0,
    "response_format" => ["type" => "json_object"]
];

$parsed = null;
$content = "";
$max_retries = 2;

for ($attempt = 0; $attempt <= $max_retries; $attempt++) {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer " . $GROQ_API_KEY
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        curl_close($ch);
        usleep(200000);
        continue;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($http_code < 200 || $http_code >= 300) {
        usleep(200000);
        continue;
    }

    $content = trim($result["choices"][0]["message"]["content"] ?? "");
    $parsed = json_decode($content, true);

    if (is_array($parsed) && array_key_exists("is_correct", $parsed)) {
        break;
    }

    usleep(200000);
}

if (!is_array($parsed)) {
    echo json_encode([
        "is_correct" => false,
        "feedback" => "AI judge failed. Please try again.",
        "raw_response" => $content
    ]);
    exit();
}

echo json_encode([
    "is_correct" => (bool)($parsed["is_correct"] ?? false),
    "feedback" => strval($parsed["feedback"] ?? "")
]);