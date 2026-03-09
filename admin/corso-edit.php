<?php
$pageTitle = 'Modifica Corso';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$course = $stmt->fetch();
if (!$course) {
    set_flash('danger', 'Corso non trovato.');
    redirect('admin/corsi.php');
}
$teachers = $pdo->query("SELECT id, full_name FROM users WHERE role='docente' ORDER BY full_name")->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>
        <div class="card"><div class="card-body">
            <form method="post" action="<?= APP_URL ?>/actions/corso-save.php" class="row g-3">
                <input type="hidden" name="id" value="<?= (int)$course['id'] ?>">
                <div class="col-md-6"><label class="form-label">Titolo</label><input name="title" class="form-control" value="<?= e($course['title']) ?>" required></div>
                <div class="col-md-3"><label class="form-label">Data inizio</label><input type="date" name="start_date" class="form-control" value="<?= e((string)$course['start_date']) ?>"></div>
                <div class="col-md-3"><label class="form-label">Data fine</label><input type="date" name="end_date" class="form-control" value="<?= e((string)$course['end_date']) ?>"></div>
                <div class="col-md-4"><label class="form-label">Docente</label><select name="teacher_id" class="form-select"><option value="">--</option><?php foreach($teachers as $t): ?><option value="<?= (int)$t['id'] ?>" <?= (int)$course['teacher_id']===(int)$t['id']?'selected':'' ?>><?= e($t['full_name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4"><label class="form-label">Ore totali</label><input type="number" step="0.5" min="0" name="total_hours" class="form-control" value="<?= e((string)$course['total_hours']) ?>"></div>
                <div class="col-md-4"><label class="form-label">Stato</label><select name="status" class="form-select"><?php foreach(['draft','active','completed','cancelled'] as $s): ?><option value="<?= $s ?>" <?= $course['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
                <div class="col-12"><label class="form-label">Descrizione</label><textarea name="description" class="form-control" rows="4"><?= e((string)$course['description']) ?></textarea></div>
                <div class="col-12"><button class="btn btn-primary">Aggiorna Corso</button></div>
            </form>
        </div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
