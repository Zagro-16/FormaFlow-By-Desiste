<?php
$pageTitle='Attestati';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$records=$pdo->query("SELECT cert.*, u.full_name student_name, c.title course_title
FROM certificates cert JOIN users u ON u.id=cert.student_id JOIN courses c ON c.id=cert.course_id ORDER BY cert.issued_at DESC")->fetchAll();
$eligible=$pdo->query("SELECT e.course_id, e.student_id, u.full_name, c.title course_title
FROM enrollments e JOIN users u ON u.id=e.student_id JOIN courses c ON c.id=e.course_id
WHERE e.status='completed' AND NOT EXISTS (
 SELECT 1 FROM certificates cc WHERE cc.course_id=e.course_id AND cc.student_id=e.student_id
)")->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-admin.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm mb-3"><div class="card-header bg-white"><h6 class="mb-0">Genera attestato</h6></div><div class="card-body">
<form method="post" action="<?= APP_URL ?>/actions/attestato-genera.php" class="row g-2"><div class="col-md-10"><select id="studentCourseSelect" class="form-select" required><option value="">Seleziona corsista completato</option><?php foreach($eligible as $e): ?><option value="<?= (int)$e['student_id'] ?>:<?= (int)$e['course_id'] ?>"><?= e($e['full_name'].' - '.$e['course_title']) ?></option><?php endforeach; ?></select><input type="hidden" name="student_id" id="studentIdField"><input type="hidden" name="course_id" id="courseIdField"></div><div class="col-md-2"><button class="btn btn-primary w-100">Genera</button></div></form>
<script>document.getElementById('studentCourseSelect')?.addEventListener('change',function(){const v=this.value.split(':');document.getElementById('studentIdField').value=v[0]||'';document.getElementById('courseIdField').value=v[1]||'';});</script>
</div></div>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table align-middle"><thead><tr><th>Codice</th><th>Corsista</th><th>Corso</th><th>Data</th><th>PDF</th></tr></thead><tbody><?php foreach($records as $r): ?><tr><td><?= e($r['certificate_code']) ?></td><td><?= e($r['student_name']) ?></td><td><?= e($r['course_title']) ?></td><td><?= e($r['issued_at']) ?></td><td><a class="btn btn-sm btn-outline-secondary" href="<?= APP_URL.'/'.e($r['pdf_path']) ?>" target="_blank">Apri</a></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
