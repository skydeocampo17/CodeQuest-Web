<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../../data/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, type_id, avatar FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = cq_get_session_role_for_type_id($pdo, (int) $user['type_id']);
            
            // ⚔️ FETCH THESE SO THEY APPEAR ON DASHBOARD IMMEDIATELY
            $_SESSION['avatar']     = $user['avatar'] ?? '👤';
            $_SESSION['hero_title'] = cq_get_user_title($pdo, (int) $user['id']);
        
            header("Location: " . ($_SESSION['role'] === 'admin' ? "/admin-dashboard" : "/dashboard"));
            exit();
        } else {
                $_SESSION['error'] = "Invalid Email or Secret Rune. Access denied!";
                header("Location: /login");
                exit();
            }
    
        } catch (PDOException $e) {
            $_SESSION['error'] = "The Gatekeeper is busy. Error: " . $e->getMessage();
            header("Location: /login");
            exit();
        }
    } else {
    header("Location: /login");
    exit();
}
