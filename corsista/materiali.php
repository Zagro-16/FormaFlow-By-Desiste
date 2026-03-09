<?php
$pageTitle='Corsista - Materiali';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');
$uid=(int)$_SESSION['user']['id'];
$st=$pdo->prepare("SELECT m.*, c.title course_title FROM materials m JOIN courses c ON c.id=m.course_id JOIN enrollments e ON e.course_id=c.id WHERE e.student_id=? ORDER BY m.created_at DESC");$st->execute([$uid]);$materials=$st->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-corsista.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Titolo</th><th>Corso</th><th>Data</th><th>File</th></tr></thead><tbody><?php foreach($materials as $m): ?><tr><td><?= e($m['title']) ?></td><td><?= e($m['course_title']) ?></td><td><?= e((string)$m['created_at']) ?></td><td><a href="<?= APP_URL.'/'.e($m['file_path']) ?>" target="_blank">Scarica</a></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
