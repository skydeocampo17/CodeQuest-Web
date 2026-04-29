<?php
// 1. START SESSION & SECURITY CHECK
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only the King/Admin can send decrees!
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login");
    exit();
}

// 2. CONNECT TO THE KINGDOM'S DATABASE
require_once __DIR__ . '/../../data/database.php';

// 3. PROCESS THE POST REQUEST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize the incoming message
    $message = trim($_POST['broadcast_message']);
    $admin_id = $_SESSION['user_id'];

    if (!empty($message)) {
        try {
            // Insert the decree into the database
            // (Assuming you have a table named 'server_broadcasts')
            $stmt = $pdo->prepare("INSERT INTO server_broadcasts (admin_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$admin_id, $message]);

            // Set a success rune (session message) to show on the dashboard
            $_SESSION['success_message'] = "🦅 The Raven has flown! Your decree was sent to the realm.";

        } catch (PDOException $e) {
            $_SESSION['error_message'] = "The ravens are sleeping. Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "You cannot send an empty scroll!";
    }

    // 4. REDIRECT BACK TO THE THRONE ROOM
    header("Location: /admin-dashboard");
    exit();

} else {
    // If accessed directly without POST, send back to dashboard
    header("Location: /admin-dashboard");
    exit();
}