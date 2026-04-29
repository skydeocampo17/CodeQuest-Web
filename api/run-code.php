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
You are simulating a beginner C code runner.

Question:
$question

Player Code:
$code

STRICT RULES:
- DO NOT return the code itself
- Assume sample input values: (2, 3)
- If correct, simulate output
- If there is an error, identify it clearly
- Detect syntax errors, wrong function usage, missing return if needed
- Keep explanation VERY SHORT

Return ONLY JSON:
{
  "run_result": "...",
  "has_error": false,
  "error_type": "",
  "error_message": ""
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
$last_error = null;
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
        $last_error = curl_error($ch);
        curl_close($ch);
        usleep(200000);
        continue;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($http_code < 200 || $http_code >= 300) {
        $last_error = $result;
        usleep(200000);
        continue;
    }

    $content = trim($result["choices"][0]["message"]["content"] ?? "");
    $parsed = json_decode($content, true);

    if (is_array($parsed)) {
        break;
    }

    usleep(200000);
}

if (!is_array($parsed)) {
    echo json_encode([
        "run_result" => "AI runner failed. Please try again.",
        "has_error" => true,
        "error_type" => "System Error",
        "error_message" => "Invalid AI response.",
        "raw_response" => $content
    ]);
    exit();
}

echo json_encode([
    "run_result" => strval($parsed["run_result"] ?? "No result."),
    "has_error" => (bool)($parsed["has_error"] ?? false),
    "error_type" => strval($parsed["error_type"] ?? ""),
    "error_message" => strval($parsed["error_message"] ?? "")
]);