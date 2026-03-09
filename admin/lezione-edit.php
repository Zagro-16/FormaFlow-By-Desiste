<?php
$pageTitle = 'Modifica Lezione';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM lessons WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$lesson = $stmt->fetch();
if (!$lesson) {
    set_flash('danger', 'Lezione non trovata.');
    redirect('admin/lezioni.php');
}
$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="card"><div class="card-body">
            <form method="post" action="<?= APP_URL ?>/actions/lezione-save.php" class="row g-3">
                <input type="hidden" name="id" value="<?= (int)$lesson['id'] ?>">
                <div class="col-md-6"><label class="form-label">Corso</label><select name="course_id" class="form-select" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$lesson['course_id']===(int)$c['id']?'selected':'' ?>><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Titolo lezione</label><input name="title" class="form-control" value="<?= e($lesson['title']) ?>" required></div>
                <div class="col-md-3"><label class="form-label">Data</label><input type="date" name="lesson_date" class="form-control" value="<?= e($lesson['lesson_date']) ?>" required></div>
                <div class="col-md-3"><label class="form-label">Ora inizio</label><input type="time" name="start_time" class="form-control" value="<?= e(substr($lesson['start_time'],0,5)) ?>" required></div>
                <div class="col-md-3"><label class="form-label">Ora fine</label><input type="time" name="end_time" class="form-control" value="<?= e(substr($lesson['end_time'],0,5)) ?>" required></div>
                <div class="col-md-3"><label class="form-label">Stato</label><select name="status" class="form-select"><?php foreach(['scheduled','completed','cancelled'] as $s): ?><option value="<?= $s ?>" <?= $lesson['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
                <div class="col-12"><label class="form-label">Google Meet link</label><input type="url" name="google_meet_link" class="form-control" value="<?= e((string)$lesson['google_meet_link']) ?>"></div>
                <div class="col-12"><button class="btn btn-primary">Aggiorna Lezione</button></div>
            </form>
        </div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
