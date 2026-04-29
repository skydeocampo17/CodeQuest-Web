<?php
require_once __DIR__ . '/../../../includes/app.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /guest-dashboard");
    exit();
}

require_once __DIR__ . '/../../../data/database.php';

$selected_lang = $_GET['lang_filter'] ?? 'all';
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_id = isset($_GET['search_id']) ? trim($_GET['search_id']) : '';
$selected_quiz = $_GET['quiz_filter'] ?? 'all';
$selected_level = $_GET['level_filter'] ?? 'all';
$order = (isset($_GET['order']) && $_GET['order'] === 'ASC') ? 'ASC' : 'DESC';

try {
    $languagesTable = cq_languages_table($pdo);
    $quizTypesTable = cq_quiz_types_table($pdo);

    if ($languagesTable === null || $quizTypesTable === null) {
        throw new RuntimeException('Required lookup tables are missing.');
    }

    $safeLanguagesTable = cq_safe_identifier($languagesTable);
    $safeQuizTypesTable = cq_safe_identifier($quizTypesTable);

    $query = "
        SELECT q.id, q.question_text, q.correct_answer, q.wrong_1, q.wrong_2, q.wrong_3,
               q.quiz_id, q.language_id, q.level_id, pl.name AS language, qt.name AS quiz_type
        FROM questions q
        JOIN `{$safeLanguagesTable}` pl ON q.language_id = pl.id
        JOIN `{$safeQuizTypesTable}` qt ON q.quiz_id = qt.id
    ";

    $conditions = [];
    $params = [];

    if ($search_id !== '') {
        $conditions[] = "q.id = :search_id";
        $params[':search_id'] = $search_id;
    }
    if ($selected_lang !== 'all') {
        $conditions[] = "q.language_id = :lang_id";
        $params[':lang_id'] = $selected_lang;
    }
    if ($search_term !== '') {
        $conditions[] = "q.question_text LIKE :search";
        $params[':search'] = "%$search_term%";
    }
    if ($selected_quiz !== 'all') {
        $conditions[] = "q.quiz_id = :quiz_id";
        $params[':quiz_id'] = $selected_quiz;
    }
    if ($selected_level !== 'all') {
        $conditions[] = "q.level_id = :level_id";
        $params[':level_id'] = $selected_level;
    }
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY q.id $order";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $langs = cq_get_languages($pdo);
    $quiz_types = cq_get_quiz_types($pdo);
    $difficulty_levels = cq_get_difficulty_levels($pdo);
} catch (Throwable $e) {
    die("The Forge is cold: " . $e->getMessage());
}

app_render_document_start('CodeQuest | Quest Forge', [
    '/assets/css/layout-dashboard.css',
    '/assets/css/pages/admin-common.css',
    '/assets/css/pages/admin-questions.css',
], 'dashboard-layout admin-page admin-page-wide admin-question-page');

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
                <span class="admin-page-title-icon">⚒️</span> The Quest Forge
            </h2>
            <button class="admin-primary-button" data-open-new-quest>+ NEW QUEST</button>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th width="80" class="admin-table-sort" data-toggle-sort>
                        ID <?php echo ($order === 'ASC') ? '🔼' : '🔽'; ?>
                    </th>
                    <th width="80">Rune</th>
                    <th>Challenge Description</th>
                    <th width="150">Trial Type</th>
                    <th width="100" style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $q): ?>
                    <tr
                        data-open-choice
                        data-quest-id="<?php echo (int) $q['id']; ?>"
                        data-quest-text="<?php echo htmlspecialchars($q['question_text'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-quest-correct="<?php echo htmlspecialchars($q['correct_answer'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-quest-wrong1="<?php echo htmlspecialchars((string) ($q['wrong_1'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                        data-quest-wrong2="<?php echo htmlspecialchars((string) ($q['wrong_2'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                        data-quest-wrong3="<?php echo htmlspecialchars((string) ($q['wrong_3'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                        data-quest-quiz-id="<?php echo (int) $q['quiz_id']; ?>"
                        data-quest-language-id="<?php echo (int) $q['language_id']; ?>"
                        data-quest-level-id="<?php echo (int) $q['level_id']; ?>"
                    >
                        <td style="color: #7f8c8d;">#<?php echo (int) $q['id']; ?></td>
                        <td><div class="lang-icon-badge"><?php echo htmlspecialchars(substr($q['language'], 0, 1), ENT_QUOTES, 'UTF-8'); ?></div></td>
                        <td class="quest-question-cell"><?php echo htmlspecialchars($q['question_text']); ?></td>
                        <td style="color: #7f8c8d;"><?php echo htmlspecialchars($q['quiz_type']); ?></td>
                        <td style="text-align: center;">
                            <div class="admin-action-group">
                                <button class="admin-action-button">📋</button>
                                <button class="admin-action-button" data-quest-delete="<?php echo (int) $q['id']; ?>">🗑️</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <aside class="admin-sidebar">
        <div class="admin-filter-card">
            <h2>Forge Filters</h2>
            <form action="/admin-quests" method="GET" id="filterForm">
                <input type="hidden" name="order" id="sort_order_input" value="<?php echo htmlspecialchars($order, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="number" name="search_id" class="admin-filter-input" placeholder="Hero ID..." value="<?php echo htmlspecialchars($search_id); ?>">
                <input type="text" name="search" class="admin-filter-input" placeholder="Search Keywords..." value="<?php echo htmlspecialchars($search_term); ?>">

                <label class="admin-filter-label">Filter by Rune:</label>
                <select name="lang_filter" class="admin-filter-select">
                    <option value="all">All Languages</option>
                    <?php foreach ($langs as $l): ?>
                        <option value="<?php echo (int) $l['id']; ?>" <?php echo ($selected_lang == $l['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($l['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="admin-filter-label">Filter by Trial Type:</label>
                <select name="quiz_filter" class="admin-filter-select">
                    <option value="all">All Types</option>
                    <?php foreach ($quiz_types as $qt): ?>
                        <option value="<?php echo (int) $qt['id']; ?>" <?php echo ($selected_quiz == $qt['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($qt['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="admin-filter-label">Filter by Level:</label>
                <select name="level_filter" class="admin-filter-select">
                    <option value="all">All Levels</option>
                    <?php foreach ($difficulty_levels as $level): ?>
                        <option value="<?php echo (int) $level['id']; ?>" <?php echo ($selected_level == $level['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($level['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <div class="admin-count"><?php echo count($questions); ?></div>
            <p class="admin-count-copy">Quests Forged</p>
        </div>
    </aside>
</div>

<div id="choiceModal" class="admin-modal-overlay">
    <div class="admin-modal-card">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">📜 Quest Briefing #<span id="choice_quest_id"></span></h3>
            <button class="admin-close-button" data-modal-close="choiceModal">&times;</button>
        </div>
        <div class="admin-modal-content-box">
            <label class="quest-briefing-label">The Challenge:</label>
            <p id="info_question_text" class="quest-briefing-copy"></p>
            <div class="quest-briefing-grid">
                <div>
                    <label class="quest-briefing-label quest-briefing-label-success">Correct Loot:</label>
                    <p id="info_correct" class="quest-briefing-text"></p>
                </div>
                <div id="info_wrong_wrapper">
                    <label class="quest-briefing-label quest-briefing-label-danger">Deceptive Traps:</label>
                    <ul id="info_wrong_list" class="quest-briefing-list"></ul>
                </div>
            </div>
        </div>
        <div class="admin-modal-actions">
            <button class="btn-pixel admin-primary-button" id="choice_edit_btn">REFORGE QUEST</button>
            <button class="btn-pixel admin-danger-button" id="choice_delete_btn">PURGE</button>
        </div>
    </div>
</div>

<div id="editModal" class="admin-modal-overlay">
    <div class="admin-modal-card">
        <div class="admin-modal-header">
            <h3 id="modal_title" class="admin-modal-title">⚒️ Reforge Details</h3>
            <button class="admin-close-button" data-modal-close="editModal">&times;</button>
        </div>
        <form action="/admin/update-quest.php" method="POST">
            <input type="hidden" name="id" id="modal_id">

            <div class="admin-modal-grid">
                <div class="admin-form-section">
                    <label class="admin-form-label">Language:</label>
                    <select name="language_id" id="modal_language" class="admin-filter-select">
                        <?php foreach ($langs as $l): ?>
                            <option value="<?php echo (int) $l['id']; ?>"><?php echo htmlspecialchars($l['name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="admin-form-label admin-form-label-success">Correct Answer:</label>
                    <input type="text" name="correct_answer" id="modal_correct_input" class="admin-filter-input">
                    <select name="correct_answer_tf" id="modal_correct_tf" class="admin-filter-select hidden">
                        <option value="True">True</option>
                        <option value="False">False</option>
                    </select>
                </div>

                <div class="admin-form-section">
                    <label class="admin-form-label">Trial Category:</label>
                    <select name="quiz_id" id="modal_quiz_type" class="admin-filter-select">
                        <?php foreach ($quiz_types as $qt): ?>
                            <option value="<?php echo (int) $qt['id']; ?>"><?php echo htmlspecialchars($qt['name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="admin-form-label">Difficulty Level:</label>
                    <select name="level_id" id="modal_level" class="admin-filter-select">
                        <?php foreach ($difficulty_levels as $level): ?>
                            <option value="<?php echo (int) $level['id']; ?>"><?php echo htmlspecialchars($level['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="admin-form-section">
                <label class="admin-form-label">Question Text / Code Block:</label>
                <textarea name="question_text" id="modal_text" class="admin-filter-input admin-form-textarea"></textarea>
            </div>

            <div id="wrong_answers_section" class="admin-form-section">
                <label class="admin-form-label admin-form-label-danger">Wrong Options (Multiple Choice Only):</label>
                <div class="admin-wrong-answers-grid">
                    <input type="text" name="wrong_1" id="modal_w1" class="admin-filter-input">
                    <input type="text" name="wrong_2" id="modal_w2" class="admin-filter-input">
                    <input type="text" name="wrong_3" id="modal_w3" class="admin-filter-input">
                </div>
            </div>

            <button type="submit" class="btn-pixel admin-primary-button" style="width: 100%; font-size: 1.6rem;">SAVE TO THE TOME</button>
        </form>
    </div>
</div>

<div id="successModal" class="admin-modal-overlay">
    <div class="admin-modal-card admin-modal-card-xs" style="text-align: center;">
        <h3 class="admin-modal-title" style="color: #6ab04c;">✨ FORGE COMPLETE</h3>
        <p id="success_message" class="admin-status-copy">The Realm's challenges have been updated.</p>
        <button class="btn-pixel admin-primary-button" data-modal-close="successModal">EXCELLENT</button>
    </div>
</div>

<?php app_render_document_end(['/assets/js/pages/admin-questions.js']); ?>
