<?php
$pageTitle = 'Dashboard Docente';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');

$teacherId = (int)$_SESSION['user']['id'];

$statsStmt = $pdo->prepare("SELECT
    COUNT(*) AS assigned_courses,
    COALESCE(SUM(total_hours), 0) AS assigned_hours,
    COUNT(CASE WHEN status = 'active' THEN 1 END) AS active_courses
    FROM courses WHERE teacher_id = ?");
$statsStmt->execute([$teacherId]);
$stats = $statsStmt->fetch();

$doneHoursStmt = $pdo->prepare("SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE, l.start_time, l.end_time))/60,0) AS done_hours
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    WHERE c.teacher_id = ? AND l.status = 'completed'");
$doneHoursStmt->execute([$teacherId]);
$done = $doneHoursStmt->fetch();

$upcomingStmt = $pdo->prepare("SELECT l.*, c.title AS course_title
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    WHERE c.teacher_id = ? AND l.lesson_date >= CURDATE()
    ORDER BY l.lesson_date, l.start_time
    LIMIT 8");
$upcomingStmt->execute([$teacherId]);
$upcoming = $upcomingStmt->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-docente.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Corsi assegnati</div><div class="stat-number"><?= (int)$stats['assigned_courses'] ?></div></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Corsi attivi</div><div class="stat-number"><?= (int)$stats['active_courses'] ?></div></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Ore assegnate</div><div class="stat-number"><?= number_format((float)$stats['assigned_hours'],1) ?></div></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Ore svolte</div><div class="stat-number"><?= number_format((float)$done['done_hours'],1) ?></div></div></div></div>
</div>

<div class="card"><div class="card-header">Prossime lezioni</div><div class="card-body table-responsive">
    <table class="table"><thead><tr><th>Corso</th><th>Lezione</th><th>Data</th><th>Orario</th><th>Meet</th></tr></thead><tbody>
    <?php foreach($upcoming as $l): ?><tr><td><?= e($l['course_title']) ?></td><td><?= e($l['title']) ?></td><td><?= e($l['lesson_date']) ?></td><td><?= e(substr($l['start_time'],0,5)) ?> - <?= e(substr($l['end_time'],0,5)) ?></td><td><?php if($l['google_meet_link']): ?><a href="<?= e($l['google_meet_link']) ?>" target="_blank">Apri</a><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?>
    </tbody></table>
</div></div>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
