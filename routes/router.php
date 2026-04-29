<?php

require_once __DIR__ . '/../bootstrap/app.php';

session_start();

$webRoutes = require __DIR__ . '/web.php';
$url = rtrim($_GET['url'] ?? 'index', '/');
$segments = explode('/', $url);
$baseRoute = $segments[0] === 'index' ? 'home' : $segments[0];

$protectedRoutes = ['dashboard', 'quests', 'guild', 'battle'];
$adminRoutes = ['admin-dashboard', 'admin-quests', 'admin-quiz-types', 'admin-users'];
$guestOnlyRoutes = ['login', 'register', 'guest-dashboard'];

if ((in_array($baseRoute, $protectedRoutes, true) || in_array($baseRoute, $adminRoutes, true)) && !isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

if (in_array($baseRoute, $adminRoutes, true) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header('Location: /dashboard');
    exit();
}

if (in_array($baseRoute, $guestOnlyRoutes, true) && isset($_SESSION['user_id'])) {
    $redirect = ($_SESSION['role'] ?? 'user') === 'admin' ? 'admin-dashboard' : 'dashboard';
    header('Location: /' . $redirect);
    exit();
}

$renderView = static function (string $view): void {
    $targetView = view_path($view);

    if (file_exists($targetView)) {
        require $targetView;
        return;
    }

    echo 'Critical Error: The scroll <b>' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8') . '</b> was not found.';
};

switch ($baseRoute) {
    case 'home':
        if (isset($_SESSION['user_id'])) {
            $redirect = ($_SESSION['role'] ?? 'user') === 'admin' ? 'admin-dashboard' : 'dashboard';
            header('Location: /' . $redirect);
            exit();
        }
        break;

    case 'logout':
        require base_path('logic/actions/action-logout.php');
        exit();

    case 'action-login':
        require base_path('logic/actions/action-login.php');
        exit();

    case 'action-register':
        require base_path('logic/actions/action-register.php');
        exit();

    case 'action-update-profile':
        require base_path('logic/actions/action-update-profile.php');
        exit();

    case 'admin-dashboard':
        $logicFile = base_path('logic/logics/logic-admin.php');
        if (file_exists($logicFile)) {
            require_once $logicFile;
        }
        break;

    case 'admin-users':
        $logicFile = base_path('logic/logics/logic-admin-users.php');
        if (file_exists($logicFile)) {
            require_once $logicFile;
        }
        break;

    case 'heroes':
        require base_path('logic/logics/logic-leaderboard.php');
        break;

    case 'hero':
        if (isset($segments[1]) && $segments[1] !== '') {
            $_GET['target_hero'] = urldecode($segments[1]);
            break;
        }

        header('Location: /heroes');
        exit();

    default:
        break;
}

if (!isset($webRoutes[$baseRoute])) {
    http_response_code(404);
    $errorView = view_path('errors/404.php');

    if (file_exists($errorView)) {
        require $errorView;
    } else {
        echo '<h1>404 - Thou art lost, traveler!</h1>';
    }
    exit();
}

$route = $webRoutes[$baseRoute];
$controller = new $route['controller']();
$view = $controller->{$route['method']}();

$renderView($view);
