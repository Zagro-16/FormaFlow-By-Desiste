<?php
$pageTitle='Corsista - Corsi';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');
$uid=(int)$_SESSION['user']['id'];
$st=$pdo->prepare("SELECT c.*, e.status enroll_status FROM enrollments e JOIN courses c ON c.id=e.course_id WHERE e.student_id=? ORDER BY c.start_date DESC");$st->execute([$uid]);$courses=$st->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-corsista.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Corso</th><th>Periodo</th><th>Ore</th><th>Stato corso</th><th>Iscrizione</th></tr></thead><tbody><?php foreach($courses as $c): ?><tr><td><?= e($c['title']) ?></td><td><?= e((string)$c['start_date']) ?> → <?= e((string)$c['end_date']) ?></td><td><?= e((string)$c['total_hours']) ?></td><td><?= e($c['status']) ?></td><td><?= e($c['enroll_status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
