<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Path Correction: Reach data-config.php from the /logic folder
require_once __DIR__ . '/../../data/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // 2. Validation: Do the runes match?
    if ($password !== $confirm) {
        $_SESSION['error'] = "Thy Secret Runes do not match! Return and try again.";
        header("Location: /register"); // Updated to clean URL
        exit();
    }

    try {
        $defaultTypeId = 2; // default = player
        if ($defaultTypeId === null) {
            throw new RuntimeException("No default user type is configured in user_types.");
        }

        // 3. Duplicate Check: Is the name or scroll already taken?
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $checkStmt->execute([$username, $email]);

        if ($checkStmt->fetch()) {
            $_SESSION['error'] = "This Hero Alias or Email is already registered in the Guild.";
            header("Location: /register"); // Updated to clean URL
            exit();
        }

        // 4. Encrypt the Rune
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // 5. Enrollment: Insert into the database
        $insertStmt = $pdo->prepare("INSERT INTO users (type_id, username, email, password) VALUES (?, ?, ?, ?)");
        
        if ($insertStmt->execute([$defaultTypeId, $username, $email, $hashedPassword])) {
            // Success: Pass a success flag to the login page
            header("Location: /login?registration=success");
            exit();
        }

    } catch (Throwable $e) {
        // Error handling for the "Bug Hunter" rule logic
        $_SESSION['error'] = "The King's Scribes failed to record thy entry. Error: " . $e->getMessage();
        header("Location: /register");
        exit();
    }
} else {
    header("Location: /register");
    exit();
}
