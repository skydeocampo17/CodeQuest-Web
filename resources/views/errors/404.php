<?php
require_once __DIR__ . '/../../../includes/app.php';

app_render_document_start('404 | CodeQuest', [
    '/assets/css/layout-dashboard.css',
], 'dashboard-layout');
app_include('layout/navbar.php');
app_include('layout/background-effects.php');
?>
<div class="dashboard-body content-stack">
    <main class="fb-feed-center">
        <div class="feed-card" style="display: block; text-align: center;">
            <h1 class="text-outline-black" style="font-family: 'Chelsea Market'; color: var(--ts-red);">404</h1>
            <p style="font-family: 'VT323'; font-size: 1.6rem;">Thou art lost, traveler. This scroll does not exist.</p>
            <p><a href="/" class="btn-pixel">Return Home</a></p>
        </div>
    </main>
</div>
<?php app_render_document_end(); ?>
