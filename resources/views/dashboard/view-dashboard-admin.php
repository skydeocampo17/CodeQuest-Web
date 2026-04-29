<?php
require_once __DIR__ . '/../../../includes/app.php';
// 1. SECURITY CHECK: Ensure only Admins can access The Throne Room
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /guest-dashboard");
    exit();
}

// 2. DATABASE CONNECTION
require_once __DIR__ . '/../../../data/database.php';

// 3. FETCH SIDEBAR STATS (Live from DB)
try {
    $publicUserFilter = cq_get_public_user_filter($pdo, 'users');
    $hero_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE {$publicUserFilter['sql']}");
    foreach ($publicUserFilter['params'] as $key => $value) {
        $hero_count_stmt->bindValue($key, $value, PDO::PARAM_INT);
    }
    $hero_count_stmt->execute();
    $active_heroes = $hero_count_stmt->fetchColumn();

    // Get 5 most recent registrations
    $recent_stmt = $pdo->query("SELECT username FROM users ORDER BY created_at DESC LIMIT 5");
    $recent_registrations = $recent_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $active_heroes = "??";
    $recent_registrations = ["Error fetching realm data"];
}

// 4. FETCH FEED DATA (Reports & Feedback)
$database_results = [];
try {
    $stmt = $pdo->query("
        SELECT r.id, r.type, r.description, r.created_at, u.username 
        FROM reports_feedback r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
        LIMIT 50
    ");
    $database_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}

// 5. CALCULATE ATTENTION DESK STATS
// Dynamically count the number of reports and feedback from the fetched data
$report_count = count(array_filter($database_results, fn($r) => strtolower((string) $r['type']) === 'report'));
$feedback_count = count(array_filter($database_results, fn($r) => strtolower((string) $r['type']) === 'feedback'));
app_render_document_start('CodeQuest | Realm Admin', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/admin-dashboard.css',
], 'dashboard-layout admin-dashboard-page');

app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>

    <div class="dashboard-body content-stack">
        
        <main class="fb-feed-center">
            <?php if (isset($_SESSION['admin_msg'])): ?>
                <div class="quest-alert success">
                    <span>📜</span>
                    <span><?php echo htmlspecialchars($_SESSION['admin_msg'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['admin_msg']); ?></span>
                </div>
            <?php endif; ?>

            <div class="feed-header">
                <h2>Admin Command Center</h2>
                <div class="filter-container">
                    <button class="filter-btn active" data-admin-tab="all">All</button>
                    <button class="filter-btn" data-admin-tab="reports">Reports</button>
                    <button class="filter-btn" data-admin-tab="feedback">Feedback</button>
                </div>
            </div>

            <div id="view-reports">
                <?php foreach ($database_results as $row): 
                    if (strtolower((string) $row['type']) !== 'report') continue;
                    $time = date("g:i A", strtotime($row['created_at'])); 
                ?>
                    <div class="feed-card card-report admin-feed-card">
                        <div class="admin-feed-card-copy">
                            <div class="stat-line admin-item-header">
                                <strong>🚩 Report: <?php echo htmlspecialchars($row['username']); ?></strong>
                                <div class="admin-item-meta">
                                    <span class="admin-item-time"><?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <button
                                        class="filter-btn admin-danger-button"
                                        data-report-open
                                        data-report-user="<?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-report-type="Report"
                                        data-report-time="<?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?>"
                                        data-report-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?>"
                                    >
                                        INVESTIGATE
                                    </button>
                                </div>
                            </div>
                            <p class="admin-item-description">
                                "<?php echo htmlspecialchars($row['description']); ?>"
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="view-feedback">
                <?php foreach ($database_results as $row): 
                    if (strtolower((string) $row['type']) !== 'feedback') continue;
                    $time = date("g:i A", strtotime($row['created_at']));
                ?>
                    <div class="feed-card card-feedback admin-feed-card">
                        <div class="admin-feed-card-copy">
                            <div class="stat-line admin-item-header">
                                <strong>💡 Feedback: <?php echo htmlspecialchars($row['username']); ?></strong>
                                <div class="admin-item-meta">
                                    <span class="admin-item-time"><?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <button
                                        class="filter-btn"
                                        data-report-open
                                        data-report-user="<?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-report-type="Feedback"
                                        data-report-time="<?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?>"
                                        data-report-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?>"
                                    >
                                        VIEW
                                    </button>
                                </div>
                            </div>
                            <p class="admin-item-description">
                                "<?php echo htmlspecialchars($row['description']); ?>"
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>

        <aside class="fb-sidebar-right">
            <h2 class="text-outline-black admin-page-title">Attention Required</h2>
            <div class="admin-stat-panel">
                <div class="stat-line">
                    <span>🚩 Open Reports:</span> 
                    <span class="admin-stat-accent-red"><?php echo $report_count; ?></span>
                </div>
                <div class="stat-line">
                    <span>💡 New Feedback:</span> 
                    <span class="admin-stat-accent-green"><?php echo $feedback_count; ?></span>
                </div>
            </div>

            <h2 class="text-outline-black admin-page-title">Recent Registrations</h2>
            <div class="hero-list">
                <ul class="admin-registrations">
                    <?php foreach ($recent_registrations as $hero): ?>
                        <li>
                            <span style="color: #4ade80;">+</span> <?php echo htmlspecialchars($hero); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="admin-stat-panel-muted">
                <span style="font-family: 'VT323'; font-size: 1.2rem; color: var(--ts-stone-dark);">Total Active Heroes: <strong style="color: white;"><?php echo number_format($active_heroes); ?></strong></span>
            </div>
        </aside>
    </div>

    <div id="reportDetailModal" class="admin-modal-overlay">
        <div class="app-modal">
            <div class="rivet r-tl"></div><div class="rivet r-tr"></div>
            <div class="rivet r-bl"></div><div class="rivet r-br"></div>
            
            <h2 id="modalTitle" class="app-modal-title app-modal-title-danger">Detail</h2>
            
            <div class="admin-modal-copy">
                <p style="font-family: 'VT323'; font-size: 1.2rem; color: var(--ts-stone-dark); margin-bottom: 5px;">SUBMITTED BY: <span id="modalUser" style="color: var(--ts-red); font-weight: bold;"></span></p>
                <p style="font-family: 'VT323'; font-size: 1.2rem; color: var(--ts-stone-dark); margin-bottom: 20px;">TIME: <span id="modalTime"></span></p>
                
                <div class="admin-modal-scroll-box">
                    <span id="modalDescription"></span>
                </div>
            </div>
            
            <div class="admin-modal-actions-center">
                <button type="button" class="btn-pixel" data-report-close>OK</button>
            </div>
        </div>
    </div>
<?php app_render_document_end(['/assets/js/pages/admin-dashboard.js']); ?>
