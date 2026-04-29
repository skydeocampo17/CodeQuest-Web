<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

require_once __DIR__ . '/../../../includes/app.php';
require_once __DIR__ . '/../../../data/database.php';

$userId = $_SESSION['user_id'];
$adventurerName = $_SESSION['username'] ?? 'Wanderer';
$xp = 0;
$rank = 'Novice';
$masteryStats = [
    ['lang' => 'C', 'level' => 1, 'current_xp' => 0],
    ['lang' => 'C#', 'level' => 1, 'current_xp' => 0],
    ['lang' => 'JAVA', 'level' => 1, 'current_xp' => 0],
    ['lang' => 'PHP', 'level' => 1, 'current_xp' => 0],
];
$topHeroes = [];

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $heroData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($heroData) {
        $xp = (float) cq_get_user_total_xp($pdo, (int) $heroData['id']);
        $rank = cq_get_user_title($pdo, (int) $heroData['id'], 'Wanderer');
    }

    $fetchedMastery = cq_get_user_mastery_rows($pdo, (int) $userId);

    if ($fetchedMastery) {
        $masteryStats = [];

        foreach ($fetchedMastery as $mastery) {
            $masteryStats[] = [
                'lang' => $mastery['lang'],
                'level' => (int) ($mastery['level'] ?? (floor(($mastery['current_xp'] ?? 0) / 1000) + 1)),
                'current_xp' => (int) ($mastery['current_xp'] ?? 0),
            ];
        }
    }

    $topHeroes = cq_get_global_leaderboard_rows($pdo, 5);
} catch (PDOException $e) {
    error_log("Hero Dashboard DB Error: " . $e->getMessage());
}

$overallLevel = floor($xp / 1000) + 1;

app_render_document_start('CodeQuest | Hero Dashboard', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/dashboard-hero.css',
], 'dashboard-layout hero-mode');

app_include('layout/background-effects.php');
?>

<div class="dashboard-wrapper content-stack">
    <?php app_include('layout/navbar.php'); ?>

    <div class="dashboard-body">
        <aside class="fb-sidebar-left">
            <div style="text-align: center; margin-bottom: 25px;">
                <div class="sidebar-title" style="font-size: 1.5rem; color: var(--ts-red); text-shadow: 1px 1px 0 rgba(0,0,0,0.2);">
                    <?php echo htmlspecialchars($adventurerName, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div style="color: var(--ts-outline); font-family: 'VT323'; letter-spacing: 2px;">LVL <?php echo $overallLevel; ?> ADVENTURER</div>
            </div>

            <div class="stat-line" style="margin-bottom: 10px; color: var(--ts-outline);">
                <span>STRENGTH</span>
                <strong><?php echo number_format($xp); ?> XP</strong>
            </div>

            <div class="stat-line" style="margin-bottom: 10px; color: var(--ts-outline);">
                <span>RANK</span>
                <strong style="color: var(--ts-red);"><?php echo htmlspecialchars($rank, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>

            <div class="mastery-section" style="margin-top: 30px; border-top: 2px dashed var(--ts-outline); padding-top: 20px;">
                <div style="font-family: 'Chelsea Market'; font-size: 0.8rem; margin-bottom: 15px; color: var(--ts-outline); font-weight: bold;">LANGUAGE MASTERY</div>

                <?php foreach ($masteryStats as $stat): ?>
                    <?php
                    $currentXp = $stat['current_xp'];
                    $progress = ($currentXp % 1000) / 10;
                    if ($currentXp == 0) {
                        $progress = 5;
                    }
                    ?>
                    <div class="stat-line" style="font-size: 1.1rem; margin-bottom: 5px; color: var(--ts-outline);">
                        <span><?php echo htmlspecialchars($stat['lang'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <strong>LVL <?php echo (int) $stat['level']; ?></strong>
                    </div>
                    <div class="progress-container" style="margin-bottom: 15px; border: 1px solid rgba(0,0,0,0.2);">
                        <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>

        <main class="fb-feed-center">
            <?php if (isset($_SESSION['login_success'])): ?>
                <div class="quest-alert success fade-out-auto" style="background: rgba(149, 197, 90, 0.2); border: 2px solid var(--ts-grass); padding: 15px; margin-bottom: 20px; border-radius: 8px; font-family: 'VT323'; color: white; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                    <span style="font-size: 1.5rem;">🏰</span>
                    <span style="font-size: 1.3rem;"><?php echo htmlspecialchars($_SESSION['login_success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['login_success']); ?></span>
                </div>
            <?php endif; ?>

            <div class="feed-header">
                <h2>⚔️ Kingdom Activity</h2>
                <div class="filter-container">
                    <button class="filter-btn active" data-feed-filter="ALL">ALL</button>
                    <button class="filter-btn" data-feed-filter="C">C</button>
                    <button class="filter-btn" data-feed-filter="C#">C#</button>
                    <button class="filter-btn" data-feed-filter="JAVA">JAVA</button>
                    <button class="filter-btn" data-feed-filter="PHP">PHP</button>
                </div>
            </div>

            <div id="quest-feed"></div>

            <div id="loading-trigger" style="text-align: center; color: white; padding: 20px; font-family: 'VT323'; font-size: 1.5rem;">
                Summoning more activities...
            </div>
        </main>

        <aside class="fb-sidebar-right">
            <h3 class="text-outline-black" style="font-family: 'Chelsea Market'; color: white; font-size: 1.5rem; text-align: center; margin-bottom: 20px;">
                Hall of Heroes
            </h3>

            <?php $position = 1; ?>
            <?php foreach ($topHeroes as $hero): ?>
                <div class="stat-line text-outline-black" style="color: white; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 8px 0;">
                    <span>#<?php echo $position++; ?> <span style="font-weight: bold;"><?php echo htmlspecialchars($hero['username'], ENT_QUOTES, 'UTF-8'); ?></span></span>
                    <strong style="color: #ffd700;"><?php echo number_format(((float) $hero['score']) / 1000, 1); ?>k</strong>
                </div>
            <?php endforeach; ?>
        </aside>
    </div>
</div>

<?php app_render_document_end([
    '/assets/js/feed-dashboard.js',
    '/assets/js/pages/dashboard-hero.js',
]); ?>
