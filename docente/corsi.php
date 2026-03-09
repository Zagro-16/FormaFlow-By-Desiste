<?php
$pageTitle='Docente - Corsi';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=(int)$_SESSION['user']['id'];
$rows=$pdo->prepare("SELECT c.*, (SELECT COUNT(*) FROM enrollments e WHERE e.course_id=c.id AND e.status IN ('active','completed')) students
FROM courses c WHERE c.teacher_id=? ORDER BY c.start_date DESC");
$rows->execute([$uid]); $courses=$rows->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table align-middle"><thead><tr><th>Corso</th><th>Periodo</th><th>Ore</th><th>Corsisti</th><th>Stato</th></tr></thead><tbody><?php foreach($courses as $c): ?><tr><td><?= e($c['title']) ?></td><td><?= e((string)$c['start_date']) ?> → <?= e((string)$c['end_date']) ?></td><td><?= e((string)$c['total_hours']) ?></td><td><?= (int)$c['students'] ?></td><td><?= e($c['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
