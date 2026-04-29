<?php
// 1. Start session and enable error reporting for debugging
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Database Connection - Double check this path!
// If update-hero.php is in /admin/, and data-config.php is in /data/
require_once __DIR__ . '/../data/database.php';

// 3. Security: Check if the traveler is an Overseer
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Thou art not permitted in the inner sanctum.");
}

// 4. Process the changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Capture the inputs from the Registry Modal
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $new_name = isset($_POST['username']) ? trim($_POST['username']) : '';
    $type_id = isset($_POST['type_id']) ? (int) $_POST['type_id'] : (int) (cq_get_default_user_type_id($pdo) ?? 0);

    if (empty($id) || empty($new_name)) {
        header("Location: /admin-users?status=error&message=missing_fields");
        exit();
    }

    if ($type_id <= 0) {
        header("Location: /admin-users?status=error&message=invalid_type");
        exit();
    }

    try {
        // Prepare the royal update
        // Note: Using type_id as found in your database schema
        $stmt = $pdo->prepare("UPDATE users SET username = :username, type_id = :type_id WHERE id = :id");
        
        $result = $stmt->execute([
            ':username' => $new_name,
            ':type_id'  => $type_id,
            ':id'       => $id
        ]);

        if ($result) {
            // Success! Return to the Registry
            header("Location: /admin-users?status=updated&id=" . $id);
            exit();
        } else {
            throw new Exception("The database refused the update.");
        }

    } catch (PDOException $e) {
        // If the name is already taken by another hero
        if ($e->getCode() == 23000) {
            header("Location: /admin-users?status=error&message=name_taken");
        } else {
            error_log("Update Hero Error: " . $e->getMessage());
            die("The Registry scrolls are stuck: " . $e->getMessage());
        }
        exit();
    }
} else {
    // If someone tries to access this file directly without the form
    header("Location: /admin-users");
    exit();
}
