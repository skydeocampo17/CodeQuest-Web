<?php

require_once __DIR__ . '/../bootstrap/app.php';

function app_render_document_start(string $title, array $styles = [], string $bodyClass = ''): void
{
    $baseStyles = [
        '/assets/css/style.css',
        '/assets/css/layout-navbar.css',
    ];

    $allStyles = array_values(array_unique(array_merge($baseStyles, $styles)));
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="icon" type="image/png" href="/assets/img/logo/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chelsea+Market&family=VT323&display=swap" rel="stylesheet">
<?php foreach ($allStyles as $stylePath): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($stylePath, ENT_QUOTES, 'UTF-8'); ?>">
<?php endforeach; ?>
</head>
<body<?php echo $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
    <?php
}

function app_render_document_end(array $scripts = []): void
{
    $baseScripts = ['/assets/js/ui/navbar.js'];
    $allScripts = array_values(array_unique(array_merge($baseScripts, $scripts)));

    foreach ($allScripts as $scriptPath) {
        echo '    <script src="' . htmlspecialchars($scriptPath, ENT_QUOTES, 'UTF-8') . '"></script>' . PHP_EOL;
    }
    ?>
</body>
</html>
    <?php
}

function app_include(string $relativePath): void
{
    $normalized = ltrim($relativePath, '/');
    $normalized = preg_replace('#^layout/#', '', $normalized);
    require view_path('layouts/partials/' . $normalized);
}
