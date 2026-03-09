<?php
$pageTitle = 'Iscrizioni';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();
$students = $pdo->query("SELECT id, full_name FROM users WHERE role='corsista' ORDER BY full_name")->fetchAll();

$enrollments = $pdo->query("SELECT e.*, c.title AS course_title, u.full_name AS student_name
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    JOIN users u ON u.id = e.student_id
    ORDER BY e.created_at DESC")->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
<?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
<main class="content">
    <?php include __DIR__ . '/../includes/alerts.php'; ?>
    <?php include __DIR__ . '/../includes/page-header.php'; ?>

    <div class="card mb-3"><div class="card-body">
        <form method="post" action="<?= APP_URL ?>/actions/iscrizione-save.php" class="row g-2 align-items-end">
            <div class="col-md-4"><label class="form-label">Corso</label><select name="course_id" class="form-select" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Corsista</label><select name="student_id" class="form-select" required><?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['full_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label">Stato</label><select name="status" class="form-select"><?php foreach(['active','completed','withdrawn'] as $st): ?><option value="<?= $st ?>"><?= ucfirst($st) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Salva</button></div>
        </form>
    </div></div>

    <div class="card"><div class="card-body table-responsive">
        <table class="table"><thead><tr><th>Corso</th><th>Corsista</th><th>Stato</th><th>Data</th></tr></thead>
        <tbody><?php foreach($enrollments as $e): ?><tr><td><?= e($e['course_title']) ?></td><td><?= e($e['student_name']) ?></td><td><?= e($e['status']) ?></td><td><?= e($e['created_at']) ?></td></tr><?php endforeach; ?></tbody>
        </table>
    </div></div>
</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
