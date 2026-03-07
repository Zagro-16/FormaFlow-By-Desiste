<?php
$pageTitle='Dashboard Admin';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');
$stats=$pdo->query("SELECT (SELECT COUNT(*) FROM courses) total_courses,(SELECT COUNT(*) FROM courses WHERE status='active') active_courses,(SELECT COUNT(*) FROM users WHERE role='docente') total_teachers,(SELECT COUNT(*) FROM users WHERE role='corsista') total_students,(SELECT COUNT(*) FROM certificates) certificates,(SELECT COUNT(*) FROM quiz_attempts) quiz_attempts")->fetch();
$lessons=$pdo->query("SELECT l.title,l.lesson_date,c.title course_title FROM lessons l JOIN courses c ON c.id=l.course_id ORDER BY l.lesson_date ASC LIMIT 5")->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-admin.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="row g-3 mb-3"><?php foreach([['Totale corsi',$stats['total_courses']],['Corsi attivi',$stats['active_courses']],['Docenti',$stats['total_teachers']],['Corsisti',$stats['total_students']],['Attestati',$stats['certificates']],['Quiz completati',$stats['quiz_attempts']]] as $s): ?><div class="col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small"><?= $s[0] ?></div><div class="stat-number"><?= $s[1] ?></div></div></div></div><?php endforeach; ?></div>
<div class="card"><div class="card-header">Ultime lezioni</div><div class="card-body"><table class="table"><thead><tr><th>Lezione</th><th>Corso</th><th>Data</th></tr></thead><tbody><?php foreach($lessons as $l): ?><tr><td><?= e($l['title']) ?></td><td><?= e($l['course_title']) ?></td><td><?= e($l['lesson_date']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>