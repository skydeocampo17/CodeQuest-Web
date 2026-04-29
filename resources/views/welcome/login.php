<?php
require_once __DIR__ . '/../../../includes/app.php';

app_render_document_start('CodeQuest | Hero Login', [
    '/assets/css/layout-auth.css',
]);

app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>

<div class="quest-container auth-card fade-in-up content-stack">
    <div class="rivet r-tl"></div><div class="rivet r-tr"></div>
    <div class="rivet r-bl"></div><div class="rivet r-br"></div>

    <div class="quest-sidebar">
        <img src="/assets/img/logo/logo.png" alt="CodeQuest Logo" class="sidebar-logo">
        <div class="sidebar-title">Return<br>Adventurer</div>
    </div>

    <div class="quest-form-section">
        <h2 class="form-title">Identify Thyself</h2>

        <div class="auth-form-container">
            <?php if (isset($_GET['status']) && $_GET['status'] === 'logged_out'): ?>
                <div class="quest-alert success" style="background: rgba(149, 197, 90, 0.1); border: 2px solid var(--ts-grass); padding: 12px; margin-bottom: 15px; font-family: 'VT323'; color: #d4edbc; display: flex; align-items: center; gap: 10px;">
                    <span>🕊️</span>
                    <span>Safe travels, Hero! Thy session has been closed.</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="quest-alert error" style="background: rgba(255, 0, 0, 0.1); border: 2px solid var(--ts-red); padding: 10px; margin-bottom: 15px; font-family: 'VT323'; color: #ff6b6b;">
                    ⚔️ <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
                <div class="quest-alert success" style="background: rgba(0, 255, 0, 0.1); border: 2px solid #4ade80; padding: 10px; margin-bottom: 15px; font-family: 'VT323'; color: #4ade80;">
                    📜 Thy enrollment is complete! Login to begin.
                </div>
            <?php endif; ?>

            <form action="/action-login" method="POST">
                <div class="form-group">
                    <label>HERO EMAIL</label>
                    <input type="email" name="email" class="form-control" placeholder="knight@castle.com" required>
                </div>

                <div class="form-group">
                    <label>SECRET RUNE (PASSWORD)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="btn-group-custom">
                    <a href="/register" class="btn-pixel btn-signin btn-shine" style="text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">📜 SIGN UP</a>
                    <button type="submit" class="btn-pixel btn-signup btn-shine">⚔️ ENTER</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php app_render_document_end(); ?>
