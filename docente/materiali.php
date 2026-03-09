<?php
$pageTitle='Docente - Materiali';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=(int)$_SESSION['user']['id'];
$courses=$pdo->prepare('SELECT id,title FROM courses WHERE teacher_id=? ORDER BY title');$courses->execute([$uid]);$courses=$courses->fetchAll();
$materials=$pdo->prepare("SELECT m.*, c.title course_title FROM materials m JOIN courses c ON c.id=m.course_id WHERE c.teacher_id=? ORDER BY m.created_at DESC");$materials->execute([$uid]);$materials=$materials->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm mb-3"><div class="card-body"><form method="post" action="<?= APP_URL ?>/actions/materiale-upload.php" enctype="multipart/form-data" class="row g-2"><div class="col-md-4"><select name="course_id" class="form-select"><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option><?php endforeach; ?></select></div><div class="col-md-4"><input name="title" class="form-control" placeholder="Titolo"></div><div class="col-md-4"><input type="file" name="materiale" class="form-control" required></div><div class="col-12"><button class="btn btn-primary">Carica</button></div></form></div></div>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Titolo</th><th>Corso</th><th>Data</th><th>File</th></tr></thead><tbody><?php foreach($materials as $m): ?><tr><td><?= e($m['title']) ?></td><td><?= e($m['course_title']) ?></td><td><?= e($m['created_at']) ?></td><td><a href="<?= APP_URL.'/'.e($m['file_path']) ?>" target="_blank">Apri</a></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
