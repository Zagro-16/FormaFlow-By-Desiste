<?php
$pageTitle='Corsista - materiali';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-corsista.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card"><div class="card-body"><p>Pagina corsista materiali. Dati filtrati per studente loggato.</p></div></div>
</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>