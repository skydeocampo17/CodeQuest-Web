<?php
header("Content-Type: application/json");

require_once __DIR__ . "/groq-config.php";

if (empty($GROQ_API_KEY)) {
    http_response_code(500);
    echo json_encode(["error" => "Groq API key is missing."]);
    exit();
}

$url = "https://api.groq.com/openai/v1/chat/completions";

$prompt = <<<PROMPT
Generate ONE beginner-friendly C programming coding challenge.

Rules:
- Must require writing a C function
- Function can return a value OR be void
- Must include function name
- Must include parameters if needed
- Must clearly describe what the function should do
- Keep it simple
- Do NOT include the answer
- Return exactly one field: question

Return ONLY JSON:
{
  "question": "Write a C function called print_sum that takes two integers and prints their sum."
}
PROMPT;

$payload = [
    "model" => "llama-3.1-8b-instant",
    "messages" => [
        ["role" => "system", "content" => "Return only valid JSON with exactly one field: question."],
        ["role" => "user", "content" => $prompt]
    ],
    "temperature" => 0.7,
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

    if (is_array($parsed)) {
        $question = $parsed["question"] ?? $parsed["task"] ?? null;

        if (!$question && isset($parsed["function"], $parsed["description"])) {
            $question = "Write a C function called " . $parsed["function"] . " that " . strtolower($parsed["description"]);
        }

        if ($question) {
            echo json_encode(["question" => strval($question)]);
            exit();
        }
    }

    usleep(200000);
}

echo json_encode([
    "question" => "Write a C function called print_sum that takes two integers and prints their sum."
]);