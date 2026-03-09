<?php
$pageTitle = 'Dashboard Corsista';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');

$studentId = (int)$_SESSION['user']['id'];

$statsStmt = $pdo->prepare("SELECT
    COUNT(*) AS enrolled_courses,
    COUNT(CASE WHEN e.status='completed' THEN 1 END) AS completed_courses
    FROM enrollments e
    WHERE e.student_id = ?");
$statsStmt->execute([$studentId]);
$stats = $statsStmt->fetch();

$quizStmt = $pdo->prepare('SELECT COUNT(*) AS completed_quiz FROM quiz_attempts WHERE student_id = ?');
$quizStmt->execute([$studentId]);
$quizStats = $quizStmt->fetch();

$certStmt = $pdo->prepare('SELECT COUNT(*) AS certificates_total FROM certificates WHERE student_id = ?');
$certStmt->execute([$studentId]);
$certStats = $certStmt->fetch();

$upcomingStmt = $pdo->prepare("SELECT l.*, c.title AS course_title
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    JOIN enrollments e ON e.course_id = c.id
    WHERE e.student_id = ? AND e.status IN ('active', 'completed') AND l.lesson_date >= CURDATE()
    ORDER BY l.lesson_date, l.start_time
    LIMIT 8");
$upcomingStmt->execute([$studentId]);
$upcoming = $upcomingStmt->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-corsista.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Corsi iscritti</div><div class="stat-number"><?= (int)$stats['enrolled_courses'] ?></div></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Corsi completati</div><div class="stat-number"><?= (int)$stats['completed_courses'] ?></div></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Quiz completati</div><div class="stat-number"><?= (int)$quizStats['completed_quiz'] ?></div></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Attestati</div><div class="stat-number"><?= (int)$certStats['certificates_total'] ?></div></div></div></div>
</div>

<div class="card"><div class="card-header">Prossime lezioni</div><div class="card-body table-responsive">
    <table class="table"><thead><tr><th>Corso</th><th>Lezione</th><th>Data</th><th>Orario</th><th>Meet</th></tr></thead><tbody>
    <?php foreach($upcoming as $l): ?><tr><td><?= e($l['course_title']) ?></td><td><?= e($l['title']) ?></td><td><?= e($l['lesson_date']) ?></td><td><?= e(substr($l['start_time'],0,5)) ?> - <?= e(substr($l['end_time'],0,5)) ?></td><td><?php if($l['google_meet_link']): ?><a href="<?= e($l['google_meet_link']) ?>" target="_blank">Apri</a><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?>
    </tbody></table>
</div></div>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
