<?php
$pageTitle='Docente - Lezioni';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=(int)$_SESSION['user']['id'];
$st=$pdo->prepare("SELECT l.*, c.title course_title FROM lessons l JOIN courses c ON c.id=l.course_id WHERE c.teacher_id=? ORDER BY l.lesson_date DESC,l.start_time DESC");
$st->execute([$uid]);$lessons=$st->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table align-middle"><thead><tr><th>Lezione</th><th>Corso</th><th>Data</th><th>Orario</th><th>Meet</th><th>Stato</th></tr></thead><tbody><?php foreach($lessons as $l): ?><tr><td><?= e($l['title']) ?></td><td><?= e($l['course_title']) ?></td><td><?= e($l['lesson_date']) ?></td><td><?= e(substr($l['start_time'],0,5)) ?>-<?= e(substr($l['end_time'],0,5)) ?></td><td><?php if($l['google_meet_link']): ?><a href="<?= e($l['google_meet_link']) ?>" target="_blank">Apri</a><?php else: ?>-<?php endif; ?></td><td><?= e($l['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
