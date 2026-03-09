<?php
$pageTitle='Nuovo Quiz';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');
$courses=$pdo->query('SELECT id,title FROM courses ORDER BY title')->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-admin.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="post" action="<?= APP_URL ?>/actions/quiz-save.php" class="row g-3">
<div class="col-md-6"><label class="form-label">Titolo quiz</label><input name="title" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Corso</label><select name="course_id" class="form-select" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label">Pubblica</label><select name="published" class="form-select"><option value="0">No</option><option value="1">Sì</option></select></div>
<div class="col-12"><button class="btn btn-primary">Salva Quiz</button></div>
</form></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
