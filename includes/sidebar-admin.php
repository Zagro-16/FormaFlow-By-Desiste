<?php
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
$menu = [
    'admin/dashboard.php' => 'Dashboard',
    'admin/corsi.php' => 'Corsi',
    'admin/lezioni.php' => 'Lezioni',
    'admin/calendario.php' => 'Calendario',
    'admin/docenti.php' => 'Docenti',
    'admin/corsisti.php' => 'Corsisti',
    'admin/iscrizioni.php' => 'Iscrizioni',
    'admin/presenze.php' => 'Presenze',
    'admin/qr.php' => 'QR',
    'admin/materiali.php' => 'Materiali',
    'admin/quiz.php' => 'Quiz',
    'admin/attestati.php' => 'Attestati',
    'admin/comunicazioni.php' => 'Comunicazioni',
    'admin/report.php' => 'Report',
    'admin/impostazioni.php' => 'Impostazioni',
];

$logoPrimary = __DIR__ . '/../assets/img/logo.png';
$logoAlt = __DIR__ . '/../assets/img/logo1.png';
$logoUrl = null;

if (file_exists($logoPrimary)) {
    $logoUrl = APP_URL . '/assets/img/logo.png';
} elseif (file_exists($logoAlt)) {
    $logoUrl = APP_URL . '/assets/img/logo1.png';
}
?>
<aside class="sidebar p-3">
    <div class="sidebar-brand mb-3 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <?php if ($logoUrl): ?>
                <img src="<?= e($logoUrl) ?>" alt="Logo FormaFlow" class="sidebar-logo">
            <?php endif; ?>
            <div>
                <div class="fw-bold small text-uppercase text-muted">FormaFlow By Desiste</div>
                <div class="small">Admin Area</div>
            </div>
        </div>
    </div>

    <nav class="nav flex-column admin-nav">
        <?php foreach ($menu as $url => $label):
            $isActive = str_ends_with($currentPath, $url);
        ?>
            <a class="nav-link <?= $isActive ? 'active' : '' ?>" href="<?= APP_URL . '/' . $url ?>">
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
