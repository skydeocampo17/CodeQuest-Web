<?php
require_once __DIR__ . '/../../../includes/app.php';

var_dump($_SESSION);
exit();

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$config_path = __DIR__ . '/../../../data/database.php';
if (!file_exists($config_path)) {
    die("Critical: The Forge cannot find 'data/database.php' at: " . $config_path);
}
require_once $config_path;

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

$target_username = $_GET['target_hero'] ?? null;
$user_id = (int) $_SESSION['user_id'];
$session_username = (string) ($_SESSION['username'] ?? '');
$is_own_profile = $target_username === null || $target_username === '' || $target_username === $session_username;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    if (!$is_own_profile) {
        header("Location: /hero/" . urlencode($target_username));
        exit();
    }

    try {
        if ($_POST['action_type'] === 'update_full_profile') {
            $avatar = $_POST['avatar'];
            $title = trim($_POST['hero_title']);

            if (cq_column_exists($pdo, 'users', 'title')) {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ?, title = ? WHERE id = ?");
                $stmt->execute([$avatar, $title, $user_id]);
                $_SESSION['hero_title'] = $title !== '' ? $title : cq_get_user_title($pdo, $user_id);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$avatar, $user_id]);
                $_SESSION['hero_title'] = cq_get_user_title($pdo, $user_id);
            }

            $_SESSION['avatar'] = $avatar;

            if (!empty($_POST['current_password'])) {
                $current = $_POST['current_password'];
                $new = $_POST['new_password'];
                $confirm = $_POST['confirm_password'];

                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && password_verify($current, $user['password'])) {
                    if (!empty($new) && $new === $confirm) {
                        $hashed = password_hash($new, PASSWORD_DEFAULT);
                        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $update->execute([$hashed, $user_id]);
                    } else { throw new Exception("New passwords do not match!"); }
                } else { throw new Exception("Current password incorrect!"); }
            }

            $_SESSION['show_success_modal'] = true;
            header("Location: /hero");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: " . ($is_own_profile ? "/hero?status=error" : "/hero/" . urlencode($target_username) . "?status=error"));
        exit();
    }
}

try {
    if ($is_own_profile) {
        $stmt = $pdo->prepare("SELECT id, username, avatar FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("SELECT id, username, avatar FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$target_username]);
    }
    $main_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$main_stats) {
        header("Location: /heroes");
        exit();
    }

    $profile_user_id = (int) $main_stats['id'];

    $profile_xp = (float) cq_get_user_total_xp($pdo, $profile_user_id);
    $profile_title = cq_get_user_title($pdo, $profile_user_id, "Novice Scripter");
    $profile_avatar = $main_stats['avatar'] ?? '👤';
    $profile_name = $main_stats['username'];
    $overall_level = floor($profile_xp / 1000) + 1;

    $globalHeroes = cq_get_global_leaderboard_rows($pdo, null);
    $profile_rank = '???';

    foreach ($globalHeroes as $index => $hero) {
        if ((int) $hero['id'] === $profile_user_id) {
            $profile_rank = $index + 1;
            break;
        }
    }

    $profile_mastery = cq_get_user_mastery_rows($pdo, $profile_user_id);

    $all_badges_stmt = $pdo->query("SELECT id, name, description, icon_symbol FROM badges ORDER BY id ASC");
    $all_badges = $all_badges_stmt->fetchAll(PDO::FETCH_ASSOC);

    $earned_stmt = $pdo->prepare("SELECT badge_id FROM user_badges WHERE user_id = ?");
    $earned_stmt->execute([$profile_user_id]);
    
    $earned_ids = $earned_stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Hero Profile DB Error: " . $e->getMessage());
    $_SESSION['error'] = "The forge could not load that hero profile.";
    header("Location: /heroes");
    exit();
}
app_render_document_start('Hero Dashboard | CodeQuest', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/admin-common.css',
    '/assets/css/pages/hero-profile.css',
], 'dashboard-layout hero-mode hero-profile-page');
app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>
<div id="profilePageState" data-show-profile-success="<?php echo isset($_SESSION['show_success_modal']) ? '1' : '0'; ?>"></div>
    <div class="dashboard-body content-stack">
        
        <aside class="fb-sidebar-left">
            <div class="avatar-box"><?php echo $profile_avatar; ?></div>
            
            <div class="profile-summary">
                <h1 class="text-outline-black profile-name">
                    <?php echo htmlspecialchars($profile_name); ?>
                </h1>
                <p class="profile-level">
                    LVL <?php echo $overall_level; ?> ADVENTURER
                </p>
            </div>

            <div class="stat-line"><span>STRENGTH</span> <strong><?php echo number_format($profile_xp); ?> XP</strong></div>
            <div class="stat-line"><span>RANK</span> <strong style="color: var(--ts-red);">#<?php echo $profile_rank; ?></strong></div>
            <div class="stat-line" style="border-bottom: 2px dashed var(--ts-stone-dark); padding-bottom: 15px; margin-bottom: 15px;">
                <span>TITLE</span> <strong style="font-size: 0.9rem;"><?php echo htmlspecialchars($profile_title); ?></strong>
            </div>

            <nav class="profile-settings-link">
                <?php if ($is_own_profile): ?>
                    <a href="#" data-settings-open class="nav-link-simple">🎒 EDIT PROFILE</a>
                <?php endif; ?>
            </nav>
        </aside>

        <main class="fb-feed-center">
            <div class="feed-header"><h2 class="text-outline-black">⚔️ COMBAT ARTS</h2></div>
            
            <?php if (empty($profile_mastery)): ?>
                <div class="feed-card profile-combat-empty">
                    Begin thy first quest to unlock thy arts...
                </div>
            <?php else: ?>
                <?php foreach ($profile_mastery as $stat): 
                    $clean_lang = strtolower(str_replace('#', 'sharp', $stat['lang']));
                    $progress = ($stat['current_xp'] % 1000) / 10; ?>
                    <div class="feed-card card-<?php echo $clean_lang; ?>">
                        <div class="admin-feed-card-copy">
                            <div class="stat-line">
                                <span class="profile-mastery-name"><?php echo htmlspecialchars($stat['lang']); ?></span>
                                <strong style="color: var(--ts-red);">LVL <?php echo $stat['level']; ?></strong>
                            </div>
                            <div class="progress-container"><div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <aside class="fb-sidebar-right">
            <h3 class="text-outline-black profile-trophy-title">
                🏆 TROPHIES
            </h3>
            
        <div class="badge-grid">
                <?php foreach ($all_badges as $badge): 
                    $is_earned = in_array($badge['id'], $earned_ids);
                    $icon_data = $badge['icon_symbol'];
                    
                    // --- THE SMART ICON CHECK ---
                    $display_icon = '🔒'; // Default if locked
                    
                    if ($is_earned && !empty($icon_data)) {
                        // Check if the data is an emoji (usually very short) or a real image
                        if (strlen($icon_data) < 10) {
                            // It's a simple icon/emoji!
                            $display_icon = $icon_data;
                        } else {
                            // It's a real pixel-art image BLOB
                            $base64 = base64_encode($icon_data);
                            $display_icon = '<img src="data:image/png;base64,' . $base64 . '" style="width: 100%; height: 100%; image-rendering: pixelated;">';
                        }
                    }
                ?>
                    <div class="badge-slot <?php echo $is_earned ? 'earned' : 'locked'; ?>" 
                         title="<?php echo $is_earned ? htmlspecialchars($badge['name'] . ': ' . $badge['description']) : 'Quest Hidden...'; ?>">
                        
                        <div class="badge-icon" style="font-size: 1.8rem; display: flex; justify-content: center; align-items: center;">
                            <?php echo $display_icon; ?>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>

    <div id="settingsModal" class="app-modal-overlay" hidden aria-hidden="true" style="display: none;">
        <form action="" method="POST" class="app-modal profile-settings-form">
            <input type="hidden" name="action_type" value="update_full_profile">
            
            <div class="admin-modal-header">
                <h2 class="text-outline-black admin-modal-title">⚙️ Armory</h2>
                <button type="button" data-settings-close class="btn-pixel admin-neutral-button">CLOSE</button>
            </div>
            
            <div class="profile-armory-grid">
                <div class="profile-armory-panel identity">
                    <h3 class="profile-armory-title">🛡️ Identity</h3>
                    <label class="admin-form-label">Avatar</label>
                    <select name="avatar" class="form-control">
                        <?php $avs = ['👤' => 'Specter', '🧙‍♂️' => 'Wizard', '🛡️' => 'Knight', '⚔️' => 'Warrior'];
                        foreach($avs as $val => $name): ?>
                            <option value="<?= $val ?>" <?= ($profile_avatar == $val) ? 'selected' : '' ?>><?= $val ?> <?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="admin-form-label">Title</label>
                    <input type="text" name="hero_title" class="form-control" value="<?php echo htmlspecialchars($profile_title); ?>">
                </div>

                <div class="profile-armory-panel security">
                    <h3 class="profile-armory-title security">🗝️ Security</h3>
                    <input type="password" name="current_password" class="form-control" placeholder="Current Password">
                    <input type="password" name="new_password" class="form-control" placeholder="New Password">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New">
                </div>
            </div>

            <button type="submit" class="btn-pixel btn-signup profile-save-button">💾 SAVE ALL CHANGES</button>
        </form>
    </div>

    <div id="updateSuccessModal" class="app-modal-overlay" hidden aria-hidden="true" style="display: none;">
        <div class="app-modal app-modal-sm profile-success-modal">
            <h2 class="profile-success-title">✨ IDENTITY REFORGED</h2>
            <p class="profile-success-copy">Thy changes have been recorded!</p>
            <button data-profile-success-close class="btn-pixel admin-primary-button">EXCELLENT</button>
        </div>
    </div>
<?php unset($_SESSION['show_success_modal']); ?>
<?php app_render_document_end(['/assets/js/pages/hero-profile.js']); ?>
