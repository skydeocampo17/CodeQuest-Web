<?php
require_once __DIR__ . '/../../../includes/app.php';

app_render_document_start('CodeQuest | Join the Guild', [
    '/assets/css/layout-auth.css',
    '/assets/css/pages/register.css',
]);

app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>

<div class="quest-container auth-card fade-in-up content-stack">
    <div class="rivet r-tl"></div><div class="rivet r-tr"></div>
    <div class="rivet r-bl"></div><div class="rivet r-br"></div>

    <div class="quest-sidebar">
        <img src="/assets/img/logo/logo.png" alt="CodeQuest Logo" class="sidebar-logo">
        <div class="sidebar-title">Welcome<br>Adventurer</div>
    </div>

    <div class="quest-form-section">
        <h2 class="form-title">New Recruit</h2>

        <div class="auth-form-container">
            <form action="/action-register" method="POST">
                <div class="form-group">
                    <label>HERO ALIAS (USERNAME)</label>
                    <input type="text" name="username" class="form-control" placeholder="Sir Codes-a-Lot" required>
                </div>

                <div class="form-group">
                    <label>CONTACT SCROLL (EMAIL)</label>
                    <input type="email" name="email" class="form-control" placeholder="knight@castle.com" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>SECRET RUNE</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label>VERIFY RUNE</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I pledge to the <span id="rulesLink" class="link-span">King's Rules</span>
                    </label>
                </div>

                <div class="btn-group-custom">
                    <a href="/login" class="btn-pixel btn-signin btn-shine" style="text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">📜 LOGIN</a>
                    <button type="submit" class="btn-pixel btn-signup btn-shine">⚔️ REGISTER</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['error'])): ?>
<div id="errorModal" class="app-modal-overlay" style="display: flex;" aria-hidden="false">
    <div class="app-modal modal-error">
        <div class="rivet r-tl"></div><div class="rivet r-tr"></div>
        <div class="rivet r-bl"></div><div class="rivet r-br"></div>
        <h2 class="app-modal-title modal-title">BATTLE ERROR</h2>
        <div class="modal-body" style="text-align: center; padding: 20px;">
            <p style="font-size: 1.4rem;">
                <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
            </p>
        </div>
        <div class="app-modal-actions">
            <button type="button" class="btn-pixel btn-signin" id="closeErrorBtn">BACK TO CAMP</button>
        </div>
    </div>
</div>
<?php endif; ?>

<div id="rulesModal" class="app-modal-overlay" hidden aria-hidden="true" style="display: none;">
    <div class="app-modal">
        <div class="rivet r-tl"></div><div class="rivet r-tr"></div>
        <div class="rivet r-bl"></div><div class="rivet r-br"></div>
        <h2 class="app-modal-title">The King's Decree</h2>
        <div class="modal-body">
            <p>Hear ye, hear ye! By enlisting in the Royal Guard, thou swearest to stay in <strong>character</strong> and follow the script:</p>
            <ol>
                <li><strong>Guard Thy Secrets:</strong> Thy Secret Rune (password) is for thy eyes only.</li>
                <li><strong>Honor the Guild:</strong> Treat every adventurer with respect.</li>
                <li><strong>Ban Dark Magic:</strong> No cheats or foul witchcraft.</li>
                <li><strong>Bolster the Defenses:</strong> Report bugs immediately and help the Scribes squash them.</li>
            </ol>
            <p style="font-family: 'VT323'; font-size: 1.1rem; color: var(--ts-stone-dark); margin-top: 15px; border-top: 1px dashed rgba(0, 0, 0, 0.1); padding-top: 10px;">
                <strong>P.S.</strong> A Hero who does not comment their logic is just a chaotic wizard with a death wish.
            </p>
        </div>
        <div class="app-modal-actions">
            <button type="button" class="btn-pixel modal-full-btn" id="closeModalBtn">I VOW TO COMPLY</button>
        </div>
    </div>
</div>

<?php app_render_document_end(['/assets/js/pages/register.js']); ?>
