<?php

return [
    'home' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'home',
    ],
    'about' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'about',
    ],
    'login' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'login',
    ],
    'register' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'register',
    ],
    'admin-dashboard' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'adminDashboard',
    ],
    'admin-quests' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'adminQuests',
    ],
    'admin-quiz-types' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'adminQuizTypes',
    ],
    'admin-users' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'adminUsers',
    ],
    'dashboard' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'dashboard',
    ],
    'guest-dashboard' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'guestDashboard',
    ],
    'heroes' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'heroes',
    ],
    'hero' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'heroProfile',
    ],
    'play' => [
        'controller' => \App\Http\Controllers\Web\PageController::class,
        'method' => 'play',
    ],
];
