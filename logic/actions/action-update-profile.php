<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../data/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $action_type = $_POST['action_type'] ?? '';

    try {
        if ($action_type === 'update_full_profile') {
            $avatar = $_POST['avatar'];
            $title = trim($_POST['hero_title']);

            if (cq_column_exists($pdo, 'users', 'title')) {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ?, title = ? WHERE id = ?");
                $stmt->execute([$avatar, $title, $user_id]);
                $_SESSION['hero_title'] = $title !== '' ? $title : cq_get_user_title($pdo, $user_id);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$avatar, $user_id]);
                $_SESSION['hero_title'] = cq_get_user_title($pdo, $user_id);
            }

            $_SESSION['avatar'] = $avatar;

            if (!empty($_POST['current_password'])) {
                $current = $_POST['current_password'];
                $new = $_POST['new_password'];
                $confirm = $_POST['confirm_password'];

                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($current, $user['password'])) {
                    if (!empty($new) && $new === $confirm) {
                        $hashed = password_hash($new, PASSWORD_DEFAULT);
                        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $update->execute([$hashed, $user_id]);
                    } else {
                        throw new Exception("New passwords do not match!");
                    }
                } else {
                    throw new Exception("Current password incorrect!");
                }
            }
            
            $redirect = ($_SESSION['role'] === 'admin') ? 'admin-dashboard' : 'dashboard';

            // Ensure there is a leading slash and no extra spaces
            header("Location: /" . $redirect . "?status=success");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /dashboard");
        exit();
    }
} else {
    header("Location: /dashboard");
    exit();
}
