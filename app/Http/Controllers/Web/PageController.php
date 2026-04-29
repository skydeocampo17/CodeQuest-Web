<?php

namespace App\Http\Controllers\Web;

class PageController
{
    public function home(): string
    {
        return 'welcome/home.php';
    }

    public function about(): string
    {
        return 'welcome/about.php';
    }

    public function login(): string
    {
        return 'welcome/login.php';
    }

    public function register(): string
    {
        return 'welcome/register.php';
    }

    public function adminDashboard(): string
    {
        return 'dashboard/view-dashboard-admin.php';
    }

    public function adminQuests(): string
    {
        return 'dashboard/view-manage-questions.php';
    }

    public function adminQuizTypes(): string
    {
        return 'dashboard/view-manage-quiz-types.php';
    }

    public function adminUsers(): string
    {
        return 'dashboard/view-manage-hero.php';
    }

    public function dashboard(): string
    {
        return 'dashboard/hero-dashboard.php';
    }

    public function guestDashboard(): string
    {
        return 'dashboard/guest-dashboard.php';
    }

    public function heroes(): string
    {
        return 'social/view-heroes.php';
    }

    public function heroProfile(): string
    {
        return 'social/view-hero-profile.php';
    }

    public function play(): string
    {
        return 'game/portal.php';
    }
}

