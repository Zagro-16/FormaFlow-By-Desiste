<?php
$pageTitle = 'Materiali Didattici';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();
$courseFilter = (int)($_GET['course_id'] ?? 0);

$sql = "SELECT m.*, c.title AS course_title, u.full_name AS uploader_name
        FROM materials m
        JOIN courses c ON c.id = m.course_id
        JOIN users u ON u.id = m.uploader_id
        WHERE 1=1";
$params = [];
if ($courseFilter > 0) { $sql .= ' AND m.course_id = ?'; $params[] = $courseFilter; }
$sql .= ' ORDER BY m.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materials = $stmt->fetchAll();

$summary = $pdo->query('SELECT COUNT(*) total_files FROM materials')->fetch();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-admin.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<div class="row g-3 mb-3">
    <div class="col-12 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">File caricati</div><div class="stat-number"><?= (int)$summary['total_files'] ?></div></div></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white"><h6 class="mb-0">Carica nuovo materiale</h6></div>
    <div class="card-body">
        <form method="post" action="<?= APP_URL ?>/actions/materiale-upload.php" enctype="multipart/form-data" class="row g-2">
            <div class="col-12 col-md-4">
                <label class="form-label">Corso *</label>
                <select name="course_id" class="form-select" required>
                    <?php foreach ($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Titolo materiale</label>
                <input type="text" name="title" class="form-control" placeholder="Es. Dispensa Lezione 1">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">File *</label>
                <input type="file" name="materiale" class="form-control" required>
            </div>
            <div class="col-12"><button class="btn btn-primary">Carica file</button></div>
        </form>
    </div>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-8 col-md-4">
        <select class="form-select" name="course_id">
            <option value="0">Tutti i corsi</option>
            <?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $courseFilter===(int)$c['id']?'selected':'' ?>><?= e($c['title']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-4 col-md-2"><button class="btn btn-outline-primary w-100">Filtra</button></div>
</form>

<div class="card border-0 shadow-sm"><div class="card-body table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Titolo</th><th>Corso</th><th>Uploader</th><th>Data</th><th>File</th></tr></thead>
        <tbody>
            <?php foreach($materials as $m): ?>
                <tr>
                    <td><?= e($m['title']) ?></td>
                    <td><?= e($m['course_title']) ?></td>
                    <td><?= e($m['uploader_name']) ?></td>
                    <td><?= e(substr((string)$m['created_at'],0,16)) ?></td>
                    <td><a class="btn btn-sm btn-outline-secondary" href="<?= APP_URL . '/' . e($m['file_path']) ?>" target="_blank">Apri</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div></div>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
