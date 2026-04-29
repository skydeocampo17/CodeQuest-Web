<?php
require_once __DIR__ . '/../../../includes/app.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Admin Auth Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /guest-dashboard");
    exit();
}

require_once __DIR__ . '/../../../data/database.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $quizTypesTable = cq_quiz_types_table($pdo);
    if ($quizTypesTable === null) {
        throw new RuntimeException('Quiz types table is missing.');
    }

    $safeQuizTypesTable = cq_safe_identifier($quizTypesTable);
    $query = "SELECT id, name FROM `{$safeQuizTypesTable}`";
    $params = [];

    if ($search !== '') {
        $query .= " WHERE name LIKE :search";
        $params[':search'] = "%$search%";
    }

    $query .= " ORDER BY id ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    die("The Tome is sealed: " . $e->getMessage());
}
app_render_document_start('CodeQuest | Tome of Trials', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/admin-common.css',
    '/assets/css/pages/admin-quiz-types.css',
], 'dashboard-layout admin-page admin-quiz-types-page');
app_include('layout/navbar.php');
?>
    <div class="mobile-admin-warning">
        <div class="mobile-admin-box">
            <h2>⚒️ Desktop Required</h2>
            <p>The Quest Forge is optimized for desktop editing.</p>
            <p>Please open this page on a PC to create or modify quests.</p>
            <a href="/" class="mobile-admin-btn">Return</a>
        </div>
    </div>

    <div class="admin-grid content-stack">
        <main class="admin-main">
            <div class="admin-header">
                <h2 class="admin-page-title">
                    📜 Tome of Trials
                </h2>
                <button class="admin-primary-button" data-type-modal-open>+ NEW TRIAL TYPE</button>
            </div>

            <table class="admin-table simple-table">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Trial Name</th>
                        <th width="120" style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $t): ?>
                    <tr>
                        <td style="color: #7f8c8d;">#<?php echo $t['id']; ?></td>
                        <td class="trial-name-cell"><?php echo htmlspecialchars($t['name']); ?></td>
                        <td style="text-align: center;">
                            <div class="admin-action-group">
                                <button class="admin-action-button" data-type-modal-open data-type-id="<?php echo $t['id']; ?>" data-type-name="<?php echo htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8'); ?>">📝</button>
                                <button class="admin-action-button" data-type-delete="<?php echo $t['id']; ?>">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>

        <aside class="admin-sidebar">
            <div class="admin-filter-card">
                <h2>Search Tome</h2>
                <form method="GET">
                    <input type="text" name="search" class="admin-filter-input" placeholder="Type name..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="admin-primary-button search-button">SEARCH</button>
                </form>
            </div>
        </aside>
    </div>

    <div id="typeModal" class="admin-modal-overlay">
        <div class="admin-modal-card admin-modal-card-sm">
            <h3 id="modalTitle" class="admin-modal-title">⚒️ Reforge Trial Type</h3>
            <form action="/admin/process-type.php" method="POST" class="type-form">
                <input type="hidden" name="id" id="type_id">
                <label class="admin-form-label">TRIAL NAME:</label>
                <input type="text" name="name" id="type_name" class="type-modal-input" required>
                
                <div class="type-modal-actions">
                    <button type="submit" class="admin-primary-button">SAVE TO TOME</button>
                    <button type="button" class="admin-primary-button admin-danger-button" data-type-modal-close>CANCEL</button>
                </div>
            </form>
        </div>
    </div>
<?php app_render_document_end(['/assets/js/pages/admin-quiz-types.js']); ?>
