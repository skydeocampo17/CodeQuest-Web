<?php
require_once __DIR__ . '/../../../includes/app.php';

$ctaLink = isset($_SESSION['user_id']) ? '/dashboard' : '/guest-dashboard';
$ctaText = isset($_SESSION['user_id']) ? 'RETURN TO KINGDOM' : 'BEGIN YOUR QUEST';

app_render_document_start('CodeQuest | Welcome', [
    '/assets/css/layout-index.css',
    '/assets/css/pages/home.css',
], 'welcome-layout');

app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>

<div class="quest-container-2col content-stack" id="main-board">
    <div class="stone-left fade-in-up" style="animation-delay: 0.1s;">
        <div class="rivet r-tl"></div>
        <div class="rivet r-bl"></div>

        <h1 class="hero-title">
            Welcome<br>to<br>
            <span>CodeQuest</span>
        </h1>

        <div class="divider"></div>

        <div class="typewriter-container">
            <p class="welcome-subtitle">Embark on legendary coding adventures.</p>
        </div>

        <div class="hero-cta-group">
            <a href="<?php echo $ctaLink; ?>" class="btn-pixel btn-shine btn-primary-quest">
                <?php echo htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
    </div>

    <div class="parchment-right fade-in-up" style="animation-delay: 0.3s;">
        <div class="rivet r-tr"></div>
        <div class="rivet r-br"></div>
        <img src="/assets/img/logo/logo.png" alt="CodeQuest Logo" class="floating-logo">
    </div>
</div>

<div class="quest-stats-bar content-stack">
    <div class="stat-item">
        <span class="stat-icon">⚔️</span>
        <div class="stat-text">
            <span class="stat-value">4</span>
            <span class="stat-label">Languages</span>
        </div>
    </div>

    <div class="stat-divider"></div>

    <div class="stat-item">
        <span class="stat-icon">📜</span>
        <div class="stat-text">
            <span class="stat-value">120+</span>
            <span class="stat-label">Epic Quests</span>
        </div>
    </div>

    <div class="stat-divider"></div>

    <div class="stat-item">
        <span class="stat-icon">👥</span>
        <div class="stat-text">
            <span class="stat-value">5,000+</span>
            <span class="stat-label">Active Heroes</span>
        </div>
    </div>

    <div class="stat-divider"></div>

    <div class="stat-item">
        <span class="stat-icon">🤖</span>
        <div class="stat-text">
            <span class="stat-value">Available</span>
            <span class="stat-label">On Android</span>
        </div>
    </div>
</div>

<?php app_render_document_end(); ?>
