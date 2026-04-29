<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../../includes/app.php';

app_render_document_start('Quest Portal | CodeQuest', [
    '/assets/css/layout-game.css',
    '/assets/css/pages/game-portal.css',
], 'game-layout');

app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>

<?php if (!isset($_SESSION['user_id'])): ?>
    <main class="game-viewport-wrapper content-stack">
        <div class="quest-container">
            <div class="rivet r-tl"></div>
            <div class="rivet r-tr"></div>
            <div class="rivet r-bl"></div>
            <div class="rivet r-br"></div>

            <aside class="quest-sidebar">
                <img src="/assets/img/logo/logo.png" alt="CodeQuest Logo" class="portal-logo">
                <h2 class="sidebar-title" style="margin-top: 20px;">HALT!</h2>
            </aside>

            <section class="quest-form-section">
                <h2 class="form-title">The Quest Portal</h2>

                <div id="mobile-notice" class="mobile-notice">
                    <p class="mobile-notice-copy" data-mobile-notice-text>
                        For the best experience on mobile, please download the official app.
                    </p>
                    <a href="https://play.google.com/store" target="_blank" rel="noreferrer">
                        <img src="/assets/img/google-play-badge.png" alt="Get it on Google Play" class="mobile-play-badge">
                    </a>
                    <p class="mobile-notice-divider">OR CONTINUE BELOW</p>
                </div>

                <p class="portal-desc">
                    Only enlisted Heroes of the King's Guard may enter. Enlist now to save your progress!
                </p>
            </section>
        </div>
    </main>
<?php else: ?>
    <main class="game-viewport-wrapper content-stack">
        <div class="game-frame" id="gameFrame">
            <div class="rivet r-tl"></div><div class="rivet r-tr"></div>
            <div class="rivet r-bl"></div><div class="rivet r-br"></div>

            <iframe src="/game/index.html" id="godotFrame" allow="autoplay; fullscreen"></iframe>
        </div>
    </main>
<?php endif; ?>

<?php app_render_document_end(['/assets/js/pages/game-portal.js']); ?>
