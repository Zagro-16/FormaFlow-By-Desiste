<?php
$pageTitle='Quiz';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$courseId=(int)($_GET['course_id']??0);
$courses=$pdo->query('SELECT id,title FROM courses ORDER BY title')->fetchAll();
$sql="SELECT q.*, c.title AS course_title,
 (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id=q.id) AS questions_count,
 (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id=q.id) AS attempts_count
 FROM quizzes q JOIN courses c ON c.id=q.course_id WHERE 1=1";
$params=[];
if($courseId>0){$sql.=' AND q.course_id=?';$params[]=$courseId;}
$sql.=' ORDER BY q.created_at DESC';
$st=$pdo->prepare($sql);$st->execute($params);$quizzes=$st->fetchAll();
$pageAction=['url'=>APP_URL.'/admin/quiz-new.php','label'=>'Nuovo Quiz'];
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-admin.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<form class="row g-2 mb-3" method="get"><div class="col-8 col-md-4"><select name="course_id" class="form-select"><option value="0">Tutti i corsi</option><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $courseId===(int)$c['id']?'selected':'' ?>><?= e($c['title']) ?></option><?php endforeach; ?></select></div><div class="col-4 col-md-2"><button class="btn btn-outline-primary w-100">Filtra</button></div></form>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table align-middle"><thead><tr><th>Titolo</th><th>Corso</th><th>Domande</th><th>Tentativi</th><th>Pubblicato</th></tr></thead><tbody><?php foreach($quizzes as $q): ?><tr><td><?= e($q['title']) ?></td><td><?= e($q['course_title']) ?></td><td><?= (int)$q['questions_count'] ?></td><td><?= (int)$q['attempts_count'] ?></td><td><?= (int)$q['published']===1?'Sì':'No' ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
