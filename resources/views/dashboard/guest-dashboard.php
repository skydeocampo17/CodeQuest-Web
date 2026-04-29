<?php
require_once __DIR__ . '/../../../includes/app.php';
require_once __DIR__ . '/../../../data/database.php';

$top_heroes = [];

try {
    $top_heroes = cq_get_global_leaderboard_rows($pdo, 5);
} catch (PDOException $e) {
    error_log("Guest Hall Leaderboard Error: " . $e->getMessage());
}

app_render_document_start('CodeQuest | Guest Hall', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/dashboard-guest.css',
], 'dashboard-layout guest-mode');

app_include('layout/background-effects.php');
?>

<div class="dashboard-wrapper content-stack">
    <?php app_include('layout/navbar.php'); ?>

    <div class="dashboard-body">
        <aside class="fb-sidebar-left guest-mode">
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="font-size: 4rem; filter: grayscale(100%);">👤</div>
                <div class="sidebar-title" style="font-size: 1.5rem; margin-top: 10px;">Unknown Traveler</div>
                <div class="label" style="color: #888; letter-spacing: 2px; font-family: 'VT323';">NOT ENLISTED</div>
            </div>

            <div class="stat-line"><span>STRENGTH</span> <strong>??? XP</strong></div>
            <div class="stat-line"><span>RANK</span> <strong style="color: #888;">Wanderer</strong></div>

            <div class="mastery-section">
                <div class="label" style="font-size: 0.8rem; margin-bottom: 15px; color: var(--ts-stone-dark); font-family: 'Chelsea Market';">LOCKED ABILITIES</div>
                <?php foreach (['C', 'C#', 'JAVA', 'PHP'] as $lang): ?>
                    <div class="stat-line" style="opacity: 0.5;">
                        <span><?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?></span>
                        <strong>LVL ?</strong>
                    </div>
                    <div class="progress-container"><div class="progress-bar" style="width: 10%;"></div></div>
                <?php endforeach; ?>
            </div>

            <a href="/play" class="btn-pixel btn-signup btn-shine" style="width: 100%; display: flex; align-items: center; justify-content: center; text-decoration: none; margin-top: 25px; padding: 8px; font-size: 1.3rem; box-sizing: border-box;">
                START YOUR QUEST
            </a>
        </aside>

        <main class="fb-feed-center">
            <div class="guest-welcome-card">
                <h2 class="form-title" style="color: white; border: none; font-size: 2rem; margin-bottom: 10px; text-shadow: 2px 2px black;">Halt, Traveler!</h2>
                <p style="font-family: 'VT323'; font-size: 1.5rem; color: #ccc;">
                    The King's Guard is always looking for new talent.<br>
                    Register to start earning XP and appear in the logs below!
                </p>
            </div>

            <div style="border-bottom: 2px dashed var(--ts-stone-dark); padding-bottom: 10px; margin-bottom: 25px;">
                <h2 style="font-family: 'Chelsea Market'; color: white; margin: 0; text-shadow: 2px 2px var(--ts-outline);">Kingdom Chronicles</h2>
            </div>

            <div id="quest-feed"></div>

            <div id="loading-trigger" style="text-align: center; color: white; padding: 30px; font-family: 'VT323'; font-size: 1.5rem;">
                Summoning recent events...
            </div>
        </main>

        <aside class="fb-sidebar-right">
            <h3 class="form-title" style="font-size: 1.4rem; border: none; margin-bottom: 20px; text-align: center; color: white;">Hall of Heroes</h3>
            <div class="hero-list">
                <?php if (empty($top_heroes)): ?>
                    <p style="text-align: center; font-family: 'VT323'; color: #ccc;">The scrolls are currently empty...</p>
                <?php else: ?>
                    <?php $rankNum = 1; ?>
                    <?php foreach ($top_heroes as $hero): ?>
                        <div class="stat-line" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding: 10px 0; color: white;">
                            <span><span style="color: var(--ts-red); font-weight: bold;">#<?php echo $rankNum++; ?></span> <?php echo htmlspecialchars($hero['username'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <strong style="color: #ffd700;"><?php echo number_format(((float) $hero['score']) / 1000, 1); ?>k</strong>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php app_render_document_end(['/assets/js/feed-dashboard.js']); ?>
