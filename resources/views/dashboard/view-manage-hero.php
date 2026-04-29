<?php
require_once __DIR__ . '/../../../includes/app.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /guest-dashboard");
    exit();
}

require_once __DIR__ . '/../../../data/database.php';

$search_name = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_id = isset($_GET['search_id']) ? trim($_GET['search_id']) : '';
$role_filter = $_GET['role_filter'] ?? 'all';
$order = (isset($_GET['order']) && $_GET['order'] === 'ASC') ? 'ASC' : 'DESC';
$userTypes = [];
$adminTypeId = null;
$defaultUserTypeId = null;

try {
    $userTypes = cq_get_user_types($pdo);
    $adminTypeId = cq_get_admin_type_id($pdo);
    $defaultUserTypeId = cq_get_default_user_type_id($pdo);

    $query = "SELECT id, username, email, type_id, created_at FROM users";
    $conditions = [];
    $params = [];

    if ($search_id !== '') {
        $conditions[] = "id = :search_id";
        $params[':search_id'] = $search_id;
    }

    if ($role_filter === 'admin' && $adminTypeId !== null) {
        $conditions[] = "type_id = :admin_type_id";
        $params[':admin_type_id'] = $adminTypeId;
    } elseif ($role_filter === 'user' && $defaultUserTypeId !== null) {
        $conditions[] = "type_id = :default_user_type_id";
        $params[':default_user_type_id'] = $defaultUserTypeId;
    }

    if ($search_name !== '') {
        $conditions[] = "username LIKE :search";
        $params[':search'] = "%$search_name%";
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY id $order";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die("The Registry is corrupted: " . $e->getMessage());
}

app_render_document_start('CodeQuest | Realm Registry', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/admin-common.css',
    '/assets/css/pages/admin-users.css',
], 'dashboard-layout admin-page admin-users-page');

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
                <span class="admin-page-title-icon">⚖️</span> The Realm Registry
            </h2>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th width="80" class="admin-table-sort" data-toggle-sort>
                        ID <?php echo ($order === 'ASC') ? '🔼' : '🔽'; ?>
                    </th>
                    <th>Hero Name</th>
                    <th>Email Address</th>
                    <th width="150">Rank</th>
                    <th width="100" style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <?php
                    $roleName = cq_get_user_role_name($pdo, (int) $u['type_id']);
                    $is_admin = cq_get_session_role_for_type_id($pdo, (int) $u['type_id']) === 'admin';
                    $role_class = $is_admin ? 'admin' : 'user';
                    $role_text = strtoupper($roleName);
                    ?>
                    <tr
                        data-open-user-briefing
                        data-user-id="<?php echo (int) $u['id']; ?>"
                        data-user-name="<?php echo htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-user-email="<?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-user-role-text="<?php echo htmlspecialchars($role_text, ENT_QUOTES, 'UTF-8'); ?>"
                        data-user-type-id="<?php echo (int) $u['type_id']; ?>"
                        data-user-created-at="<?php echo htmlspecialchars($u['created_at'], ENT_QUOTES, 'UTF-8'); ?>"
                    >
                        <td style="color: #7f8c8d;">#<?php echo (int) $u['id']; ?></td>
                        <td class="hero-name-cell"><?php echo htmlspecialchars($u['username']); ?></td>
                        <td style="color: #7f8c8d;"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span class="role-badge <?php echo $role_class; ?>">
                                <?php echo $role_text; ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button
                                class="admin-action-button"
                                data-open-user-edit
                                data-user-id="<?php echo (int) $u['id']; ?>"
                                data-user-type-id="<?php echo (int) $u['type_id']; ?>"
                                data-user-name="<?php echo htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?>"
                            >📋</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <aside class="admin-sidebar">
        <div class="admin-filter-card">
            <h2>Filters</h2>
            <form action="/admin-users" method="GET" id="filterForm">
                <input type="hidden" name="order" id="sort_order_input" value="<?php echo htmlspecialchars($order, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="number" name="search_id" class="admin-filter-input" placeholder="Hero ID..." value="<?php echo htmlspecialchars($search_id); ?>">
                <input type="text" name="search" class="admin-filter-input" placeholder="Search Names..." value="<?php echo htmlspecialchars($search_name); ?>">
                <label class="admin-filter-label">Filter by Rank:</label>
                <select name="role_filter" class="admin-filter-select">
                    <option value="all">All Citizens</option>
                    <option value="user" <?php echo ($role_filter == 'user') ? 'selected' : ''; ?>>Heroes</option>
                    <option value="admin" <?php echo ($role_filter == 'admin') ? 'selected' : ''; ?>>Overseers</option>
                </select>
            </form>
            <div class="admin-count"><?php echo count($users); ?></div>
            <p class="admin-count-copy">Heroes Registered</p>
        </div>
    </aside>
</div>

<div id="choiceModal" class="admin-modal-overlay">
    <div class="admin-modal-card admin-modal-card-sm">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">📜 Hero Briefing #<span id="brief_id"></span></h3>
            <button class="admin-close-button" data-modal-close="choiceModal">&times;</button>
        </div>
        <div class="admin-modal-content-box">
            <p class="briefing-copy">
                <span class="briefing-meta">NAME:</span> <span id="brief_name" class="briefing-strong"></span><br>
                <span class="briefing-meta">EMAIL:</span> <span id="brief_email"></span><br>
                <span class="briefing-meta">RANK:</span> <span id="brief_role"></span><br>
                <span class="briefing-meta">JOINED:</span> <span id="brief_date"></span>
            </p>
        </div>
        <div class="admin-modal-actions">
            <button class="btn-pixel admin-primary-button" id="brief_edit_btn">REASSIGN RANK</button>
        </div>
    </div>
</div>

<div id="editUserModal" class="admin-modal-overlay">
    <div class="admin-modal-card admin-modal-card-sm">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">⚖️ Moderate Hero</h3>
            <button class="admin-close-button" data-modal-close="editUserModal">&times;</button>
        </div>
        <form action="/admin/update-hero.php" method="POST">
            <input type="hidden" name="id" id="edit_user_id">

            <div class="admin-form-section">
                <label class="admin-form-label">Edit Hero Name:</label>
                <input type="text" name="username" id="edit_username" class="admin-filter-input" required>
            </div>

            <div class="admin-form-section">
                <label class="admin-form-label">Change Rank:</label>
                <select name="type_id" id="edit_user_type_id" class="admin-filter-select">
                    <?php foreach ($userTypes as $type): ?>
                        <option value="<?php echo (int) $type['id']; ?>">
                            <?php echo htmlspecialchars(strtoupper((string) $type['name']), ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-pixel admin-primary-button" style="width: 100%; font-size: 1.6rem;">APPLY CHANGES</button>
        </form>
    </div>
</div>

<div id="successModal" class="admin-modal-overlay">
    <div class="admin-modal-card admin-modal-card-xs success-modal">
        <h3 class="success-modal-title">✨ RANK UPDATED</h3>
        <p class="success-modal-copy">The Hero's status has been modified in the Realm Registry.</p>
        <button class="btn-pixel admin-primary-button" data-modal-close="successModal">EXCELLENT</button>
    </div>
</div>

<?php app_render_document_end(['/assets/js/pages/admin-users.js']); ?>
