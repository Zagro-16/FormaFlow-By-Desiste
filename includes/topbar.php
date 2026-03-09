<?php
$userName = $_SESSION['user']['full_name'] ?? 'Utente';
$logoPrimary = __DIR__ . '/../assets/img/logo.png';
$logoAlt = __DIR__ . '/../assets/img/logo1.png';
$logoUrl = null;

if (file_exists($logoPrimary)) {
    $logoUrl = APP_URL . '/assets/img/logo.png';
} elseif (file_exists($logoAlt)) {
    $logoUrl = APP_URL . '/assets/img/logo1.png';
}
?>
<header class="topbar d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-white">
    <div class="d-flex align-items-center gap-2">
        <?php if ($logoUrl): ?>
            <img src="<?= e($logoUrl) ?>" alt="Logo FormaFlow" class="topbar-logo">
        <?php endif; ?>
        <div class="fw-semibold"><?= APP_NAME ?></div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small d-none d-md-inline">Ciao,</span>
        <strong><?= e($userName) ?></strong>
        <span class="text-muted">|</span>
        <a href="<?= APP_URL ?>/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
    </div>
</header>
