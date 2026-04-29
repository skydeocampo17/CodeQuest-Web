<?php
session_start();
require_once __DIR__ . '/../../data/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['target_user'];
    $action = $_POST['admin_action'];

    switch ($action) {
        case 'resolve':
            // Logic to just hide the report or mark it 'done' in DB
            $msg = "Report regarding $user has been filed away.";
            break;
            
        case 'warn':
            // Logic to insert a notification for the user
            $msg = "A warning raven has been sent to $user.";
            break;

        case 'mute':
            // Logic to update a 'mute_until' column
            $msg = "$user has been silenced for a full moon cycle.";
            break;

        case 'ban':
            $banTypeId = cq_get_user_type_id_by_name($pdo, ['banned', 'banished', 'inactive', 'suspended']);
            if ($banTypeId !== null) {
                $stmt = $pdo->prepare("UPDATE users SET type_id = ? WHERE username = ?");
                $stmt->execute([$banTypeId, $user]);
                $msg = "$user has been exiled from the Kingdom.";
            } else {
                $msg = "No banned user type exists in user_types, so $user was not modified.";
            }
            break;
    }

    $_SESSION['admin_msg'] = $msg;
    header("Location: /admin-dashboard");
    exit();
}
