<?php
$pageTitle='Docente - Presenze';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=(int)$_SESSION['user']['id'];
$lessons=$pdo->prepare("SELECT l.id, CONCAT(c.title,' - ',l.title,' (',l.lesson_date,')') label FROM lessons l JOIN courses c ON c.id=l.course_id WHERE c.teacher_id=? ORDER BY l.lesson_date DESC");$lessons->execute([$uid]);$lessons=$lessons->fetchAll();
$lessonId=(int)($_GET['lesson_id']??0);
$students=[];$att=[];
if($lessonId>0){$s=$pdo->prepare("SELECT u.id,u.full_name FROM lessons l JOIN enrollments e ON e.course_id=l.course_id JOIN users u ON u.id=e.student_id WHERE l.id=? AND e.status IN ('active','completed') ORDER BY u.full_name");$s->execute([$lessonId]);$students=$s->fetchAll();$a=$pdo->prepare('SELECT * FROM attendance WHERE lesson_id=?');$a->execute([$lessonId]);foreach($a->fetchAll() as $r){$att[(int)$r['student_id']]=$r;}}
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<form class="row g-2 mb-3" method="get"><div class="col-md-6"><select name="lesson_id" class="form-select" onchange="this.form.submit()"><option value="0">Seleziona lezione</option><?php foreach($lessons as $l): ?><option value="<?= (int)$l['id'] ?>" <?= $lessonId===(int)$l['id']?'selected':'' ?>><?= e($l['label']) ?></option><?php endforeach; ?></select></div></form>
<?php if($lessonId>0): ?><div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Corsista</th><th>Stato</th><th>Aggiorna</th></tr></thead><tbody><?php foreach($students as $st): $cur=$att[(int)$st['id']]['status']??'present'; ?><tr><td><?= e($st['full_name']) ?></td><td><?= e($cur) ?></td><td><form method="post" action="<?= APP_URL ?>/actions/presenza-save.php" class="d-flex gap-2"><input type="hidden" name="lesson_id" value="<?= $lessonId ?>"><input type="hidden" name="student_id" value="<?= (int)$st['id'] ?>"><select name="status" class="form-select form-select-sm" style="max-width:130px"><?php foreach(['present','late','absent'] as $s): ?><option value="<?= $s ?>" <?= $cur===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-primary">Salva</button></form></td></tr><?php endforeach; ?></tbody></table></div></div><?php endif; ?>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
