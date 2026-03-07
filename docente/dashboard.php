<?php
$pageTitle='Docente - dashboard';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=$_SESSION['user']['id'];

?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card"><div class="card-body"><p>Pagina docente dashboard. Mostra dati associati all'utente loggato.</p></div></div>
</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>