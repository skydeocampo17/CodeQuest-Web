<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../../../includes/app.php';
require_once __DIR__ . '/../../../data/database.php';

// 1. Capture the filter and handle URL encoding for C#
$filter = isset($_GET['lang']) ? $_GET['lang'] : 'ALL';

try {
    if ($filter === 'ALL') {
        $heroes = cq_get_global_leaderboard_rows($pdo, 50);
    } else {
        $heroes = cq_get_language_leaderboard_rows($pdo, $filter, 50);
    }

} catch (PDOException $e) {
    $heroes = [];
    error_log("Leaderboard Filter Error: " . $e->getMessage());
}
app_render_document_start('CodeQuest | Hall of Heroes', [
    '/assets/css/layout-dashboard.css',
], 'dashboard-layout');
app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>

    <div class="dashboard-body content-stack">
        <aside class="fb-sidebar-left">
            <h3 style="font-family: 'Chelsea Market'; color: var(--ts-red);">Filter Scrolls</h3>
            <div class="divider" style="margin: 15px 0;"></div>
            
            <a href="heroes?lang=ALL" class="nav-link-simple" 
               style="display:block; <?php echo ($filter == 'ALL') ? 'color: var(--ts-red); font-weight: bold;' : ''; ?>">
               🌍 ALL LANGUAGES
            </a>

            <a href="heroes?lang=C" class="nav-link-simple" 
               style="display:block; <?php echo ($filter == 'C') ? 'color: var(--ts-red); font-weight: bold;' : ''; ?>">
               ⚙️ C MASTERS
            </a>

            <a href="heroes?lang=C%23" class="nav-link-simple" 
               style="display:block; <?php echo ($filter == 'C#') ? 'color: var(--ts-red); font-weight: bold;' : ''; ?>">
               🎯 C# PALADINS
            </a>

            <a href="heroes?lang=JAVA" class="nav-link-simple" 
               style="display:block; <?php echo ($filter == 'JAVA') ? 'color: var(--ts-red); font-weight: bold;' : ''; ?>">
               ☕ JAVA KNIGHTS
            </a>

            <a href="heroes?lang=PHP" class="nav-link-simple" 
               style="display:block; <?php echo ($filter == 'PHP') ? 'color: var(--ts-red); font-weight: bold;' : ''; ?>">
               🐘 PHP WIZARDS
            </a>
        </aside>

        <main class="fb-feed-center">
            <h2 style="font-family: 'Chelsea Market'; color: white; text-align: center; font-size: 2.2rem; text-shadow: 3px 3px var(--ts-outline);">
                🏆 Hall of Heroes
            </h2>
            
            <div class="quest-container" style="background: var(--ts-parchment); border: 4px solid var(--ts-outline); border-radius: 12px; padding: 20px; margin-top: 20px;">
                <table style="width: 100%; border-collapse: collapse; font-family: 'VT323'; font-size: 1.5rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--ts-outline); color: var(--ts-stone-dark);">
                            <th style="text-align: left; padding: 10px;">RANK</th>
                            <th style="text-align: left;">ADVENTURER</th>
                            <th style="text-align: right;">STRENGTH (XP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($heroes)): ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding: 20px; color: #666;">
                                    No heroes have mastered the <strong><?php echo htmlspecialchars($filter); ?></strong> scrolls yet...
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($heroes as $hero): ?>
                            <tr style="border-bottom: 1px dashed rgba(0,0,0,0.1);">
                                <td style="padding: 15px 10px;">#<?php echo $i++; ?></td>
                                <td>
                                    <strong style="color: var(--ts-red);"><?php echo htmlspecialchars($hero['username']); ?></strong>
                                    <br><small style="font-size: 0.9rem; color: #666;"><?php echo htmlspecialchars($hero['rank_title'] ?? 'Novice Scripter'); ?></small>
                                </td>
                                <td style="text-align: right; font-weight: bold;"><?php echo number_format($hero['score']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <aside class="fb-sidebar-right">
            <h3 style="font-family: 'Chelsea Market'; color: white; text-align: center;">Thy Standing</h3>
            <div style="text-align: center; margin-top: 20px;">
                <div style="font-size: 3rem;">🥇</div>
                <p style="font-family: 'VT323'; color: white; font-size: 1.2rem;">
                    Seek the top of the scrolls to earn thy place in legend!
                </p>
            </div>
        </aside>
    </div>
<?php app_render_document_end(); ?>
