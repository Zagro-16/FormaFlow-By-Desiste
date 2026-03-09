<?php
$pageTitle='Docente - Quiz';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=(int)$_SESSION['user']['id'];
$courses=$pdo->prepare('SELECT id,title FROM courses WHERE teacher_id=? ORDER BY title');$courses->execute([$uid]);$courses=$courses->fetchAll();
$quizzes=$pdo->prepare("SELECT q.*, c.title course_title,(SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id=q.id) attempts FROM quizzes q JOIN courses c ON c.id=q.course_id WHERE c.teacher_id=? ORDER BY q.created_at DESC");$quizzes->execute([$uid]);$quizzes=$quizzes->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm mb-3"><div class="card-body"><form method="post" action="<?= APP_URL ?>/actions/quiz-save.php" class="row g-2"><div class="col-md-4"><select name="course_id" class="form-select"><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option><?php endforeach; ?></select></div><div class="col-md-4"><input name="title" class="form-control" placeholder="Titolo quiz" required></div><div class="col-md-2"><select name="published" class="form-select"><option value="0">Bozza</option><option value="1">Pubblico</option></select></div><div class="col-md-2"><button class="btn btn-primary w-100">Salva</button></div></form></div></div>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Quiz</th><th>Corso</th><th>Pubblicato</th><th>Tentativi</th></tr></thead><tbody><?php foreach($quizzes as $q): ?><tr><td><?= e($q['title']) ?></td><td><?= e($q['course_title']) ?></td><td><?= (int)$q['published']===1?'Sì':'No' ?></td><td><?= (int)$q['attempts'] ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
