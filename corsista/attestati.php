<?php
$pageTitle='Corsista - Attestati';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');
$uid=(int)$_SESSION['user']['id'];
$st=$pdo->prepare("SELECT cert.*, c.title course_title FROM certificates cert JOIN courses c ON c.id=cert.course_id WHERE cert.student_id=? ORDER BY cert.issued_at DESC");$st->execute([$uid]);$certs=$st->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-corsista.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Codice</th><th>Corso</th><th>Data</th><th>PDF</th></tr></thead><tbody><?php foreach($certs as $c): ?><tr><td><?= e($c['certificate_code']) ?></td><td><?= e($c['course_title']) ?></td><td><?= e($c['issued_at']) ?></td><td><a href="<?= APP_URL.'/'.e($c['pdf_path']) ?>" target="_blank">Scarica</a></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
