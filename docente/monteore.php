<?php
$pageTitle='Docente - Monte Ore';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=(int)$_SESSION['user']['id'];
$profile=$pdo->prepare('SELECT total_assigned_hours,total_completed_hours FROM teacher_profiles WHERE user_id=?');$profile->execute([$uid]);$profile=$profile->fetch()?:['total_assigned_hours'=>0,'total_completed_hours'=>0];
$courses=$pdo->prepare('SELECT title,total_hours,status FROM courses WHERE teacher_id=? ORDER BY start_date DESC');$courses->execute([$uid]);$courses=$courses->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="row g-3 mb-3"><div class="col-md-6"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Ore assegnate</div><div class="stat-number"><?= number_format((float)$profile['total_assigned_hours'],1) ?></div></div></div></div><div class="col-md-6"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Ore svolte</div><div class="stat-number"><?= number_format((float)$profile['total_completed_hours'],1) ?></div></div></div></div></div>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Corso</th><th>Ore</th><th>Stato</th></tr></thead><tbody><?php foreach($courses as $c): ?><tr><td><?= e($c['title']) ?></td><td><?= e((string)$c['total_hours']) ?></td><td><?= e($c['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
