<nav class="quest-nav">
    <div class="nav-brand">
        <a href="/" class="brand-link">
            <img src="/assets/img/logo/logo.png" class="nav-logo" alt="CodeQuest Logo">
            <h1 class="nav-title">CODEQUEST</h1>
        </a>
    </div>

    <button type="button" class="nav-burger" id="navBurger" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="nav-collapse-wrapper" id="navMenu">
        <div class="nav-main-actions">
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/admin-dashboard" class="nav-link-simple">THE THRONE</a>
                <span class="nav-divider"></span>
                <a href="/admin-quests" class="nav-link-simple">FORGE QUESTS</a>
                <span class="nav-divider"></span>
                <a href="/admin-quiz-types" class="nav-link-simple">TRIAL TYPES</a>
                <span class="nav-divider"></span>
                <a href="/admin-users" class="nav-link-simple">MANAGE REALM</a>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <a href="/dashboard" class="nav-link-simple">MY KINGDOM</a>
                <span class="nav-divider"></span>
                <a href="/play" class="nav-link-simple">PLAY</a>
                <span class="nav-divider"></span>
                <a href="/heroes" class="nav-link-simple">HEROES</a>
            <?php else: ?>
                <a href="/guest-dashboard" class="nav-link-simple">THE KINGDOM</a>
                <span class="nav-divider"></span>
                <a href="/play" class="nav-link-simple">PLAY</a>
                <span class="nav-divider"></span>
                <a href="/heroes" class="nav-link-simple">HEROES</a>
            <?php endif; ?>

            <span class="nav-divider"></span>
            <a href="/about" class="nav-link-simple">LORE</a>
        </div>

        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="hero-profile-snippet">
                    <a href="/hero/<?php echo urlencode($_SESSION['username']); ?>" class="nav-link-simple">
                        <?php echo ($_SESSION['role'] === 'admin') ? 'OVERSEER' : 'MY PROFILE'; ?>
                    </a>
                </div>
                <a href="/logout" class="btn-pixel nav-btn-small btn-red" data-logout-open>LEAVE</a>
            <?php else: ?>
                <a href="/login" class="nav-link-simple">SIGN IN</a>
                <a href="/register" class="btn-pixel nav-btn-small btn-red">SIGN UP</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['user_id'])): ?>
<div id="logoutConfirmModal" class="app-modal-overlay" aria-hidden="true">
    <div class="app-modal app-modal-sm">
        <h2 class="app-modal-title app-modal-title-danger">Leave the Kingdom?</h2>
        <p class="app-modal-copy">
            Are you sure you wish to abandon your current quest and log out?
        </p>
        <div class="app-modal-actions">
            <button type="button" class="btn-pixel app-btn-muted" data-logout-close>STAY</button>
            <a href="/logout" class="btn-pixel btn-red app-modal-link">LEAVE</a>
        </div>
    </div>
</div>
<?php endif; ?>
