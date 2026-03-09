<?php
$pageTitle='Corsista - Quiz';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');
$uid=(int)$_SESSION['user']['id'];
$st=$pdo->prepare("SELECT q.id,q.title,c.title course_title,q.published,
(SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id=q.id AND qa.student_id=?) attempts
FROM quizzes q JOIN courses c ON c.id=q.course_id JOIN enrollments e ON e.course_id=c.id WHERE e.student_id=? ORDER BY q.created_at DESC");$st->execute([$uid,$uid]);$quizzes=$st->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-corsista.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Quiz</th><th>Corso</th><th>Pubblicato</th><th>Tentativi</th><th></th></tr></thead><tbody><?php foreach($quizzes as $q): ?><tr><td><?= e($q['title']) ?></td><td><?= e($q['course_title']) ?></td><td><?= (int)$q['published']===1?'Sì':'No' ?></td><td><?= (int)$q['attempts'] ?></td><td><?php if((int)$q['published']===1): ?><form method="post" action="<?= APP_URL ?>/actions/quiz-submit.php"><input type="hidden" name="quiz_id" value="<?= (int)$q['id'] ?>"><button class="btn btn-sm btn-primary">Invia tentativo</button></form><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
