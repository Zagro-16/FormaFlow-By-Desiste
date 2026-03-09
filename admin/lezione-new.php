<?php
$pageTitle = 'Nuova Lezione';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');
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
                <div class="col-md-6"><label class="form-label">Corso</label><select name="course_id" class="form-select" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Titolo lezione</label><input name="title" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">Data</label><input type="date" name="lesson_date" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">Ora inizio</label><input type="time" name="start_time" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">Ora fine</label><input type="time" name="end_time" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">Stato</label><select name="status" class="form-select"><?php foreach(['scheduled','completed','cancelled'] as $s): ?><option value="<?= $s ?>"><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
                <div class="col-12"><label class="form-label">Google Meet link</label><input type="url" name="google_meet_link" class="form-control" placeholder="https://meet.google.com/..." ></div>
                <div class="col-12"><button class="btn btn-primary">Salva Lezione</button></div>
            </form>
        </div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
