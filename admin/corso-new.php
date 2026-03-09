<?php
$pageTitle = 'Nuovo Corso';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');
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
                <div class="col-md-6"><label class="form-label">Titolo</label><input name="title" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">Data inizio</label><input type="date" name="start_date" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Data fine</label><input type="date" name="end_date" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Docente</label><select name="teacher_id" class="form-select"><option value="">--</option><?php foreach($teachers as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['full_name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4"><label class="form-label">Ore totali</label><input type="number" step="0.5" min="0" name="total_hours" class="form-control" value="0"></div>
                <div class="col-md-4"><label class="form-label">Stato</label><select name="status" class="form-select"><?php foreach(['draft','active','completed','cancelled'] as $s): ?><option value="<?= $s ?>"><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
                <div class="col-12"><label class="form-label">Descrizione</label><textarea name="description" class="form-control" rows="4"></textarea></div>
                <div class="col-12"><button class="btn btn-primary">Salva Corso</button></div>
            </form>
        </div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
