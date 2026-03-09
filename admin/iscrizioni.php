<?php
$pageTitle = 'Iscrizioni';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$courseFilter = (int)($_GET['course_id'] ?? 0);
$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');

$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();
$students = $pdo->query("SELECT id, full_name FROM users WHERE role='corsista' ORDER BY full_name")->fetchAll();

$sql = "SELECT e.id, e.course_id, e.student_id, e.status, e.created_at,
        c.title AS course_title,
        u.full_name AS student_name,
        u.email AS student_email
        FROM enrollments e
        JOIN courses c ON c.id = e.course_id
        JOIN users u ON u.id = e.student_id
        WHERE 1=1";
$params = [];
if ($courseFilter > 0) { $sql .= ' AND e.course_id = ?'; $params[] = $courseFilter; }
if ($statusFilter !== '') { $sql .= ' AND e.status = ?'; $params[] = $statusFilter; }
if ($search !== '') {
    $sql .= ' AND (u.full_name LIKE ? OR u.email LIKE ? OR c.title LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
$sql .= ' ORDER BY e.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$enrollments = $stmt->fetchAll();

$summary = $pdo->query("SELECT
    COUNT(*) AS total,
    COUNT(CASE WHEN status='active' THEN 1 END) AS active_total,
    COUNT(CASE WHEN status='completed' THEN 1 END) AS completed_total
    FROM enrollments")->fetch();

$editId = (int)($_GET['edit'] ?? 0);
$editing = null;
if ($editId > 0) {
    foreach ($enrollments as $row) {
        if ((int)$row['id'] === $editId) { $editing = $row; break; }
    }
}
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-admin.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Totale iscrizioni</div><div class="stat-number"><?= (int)$summary['total'] ?></div></div></div></div>
    <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Iscrizioni attive</div><div class="stat-number"><?= (int)$summary['active_total'] ?></div></div></div></div>
    <div class="col-12 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Completate</div><div class="stat-number"><?= (int)$summary['completed_total'] ?></div></div></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white"><h6 class="mb-0"><?= $editing ? 'Modifica iscrizione' : 'Nuova iscrizione' ?></h6></div>
    <div class="card-body">
        <form method="post" action="<?= APP_URL ?>/actions/iscrizione-save.php" class="row g-2 align-items-end">
            <?php if ($editing): ?><input type="hidden" name="enrollment_id" value="<?= (int)$editing['id'] ?>"><?php endif; ?>
            <div class="col-12 col-md-4"><label class="form-label">Corso</label><select name="course_id" class="form-select" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $editing && (int)$editing['course_id']===(int)$c['id']?'selected':'' ?>><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
            <div class="col-12 col-md-4"><label class="form-label">Corsista</label><select name="student_id" class="form-select" required><?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>" <?= $editing && (int)$editing['student_id']===(int)$s['id']?'selected':'' ?>><?= e($s['full_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-8 col-md-2"><label class="form-label">Stato</label><select name="status" class="form-select"><?php foreach(['active','completed','withdrawn'] as $st): ?><option value="<?= $st ?>" <?= $editing && $editing['status']===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?></select></div>
            <div class="col-4 col-md-2"><button class="btn btn-primary w-100"><?= $editing ? 'Aggiorna' : 'Salva' ?></button></div>
        </form>
    </div>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-12 col-md-4"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Cerca corsista/corso/email"></div>
    <div class="col-6 col-md-3"><select name="course_id" class="form-select"><option value="0">Tutti i corsi</option><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $courseFilter===(int)$c['id']?'selected':'' ?>><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
    <div class="col-4 col-md-2"><select name="status" class="form-select"><option value="">Tutti gli stati</option><?php foreach(['active','completed','withdrawn'] as $st): ?><option value="<?= $st ?>" <?= $statusFilter===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?></select></div>
    <div class="col-2 col-md-1"><button class="btn btn-outline-primary w-100">OK</button></div>
</form>

<div class="card border-0 shadow-sm"><div class="card-body table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Corsista</th><th>Corso</th><th>Stato</th><th>Data</th><th class="text-end">Azioni</th></tr></thead>
        <tbody>
            <?php foreach($enrollments as $e): ?>
                <tr>
                    <td><div class="fw-semibold"><?= e($e['student_name']) ?></div><div class="small text-muted"><?= e($e['student_email']) ?></div></td>
                    <td><?= e($e['course_title']) ?></td>
                    <td><span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle"><?= e($e['status']) ?></span></td>
                    <td><?= e(substr((string)$e['created_at'],0,16)) ?></td>
                    <td class="text-end"><a href="<?= APP_URL ?>/admin/iscrizioni.php?edit=<?= (int)$e['id'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div></div>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
