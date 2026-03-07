<?php
$pageTitle='Admin - corsisti';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-admin.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card"><div class="card-body"><p>Pagina corsisti pronta per gestione operativa.</p></div></div>
</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>