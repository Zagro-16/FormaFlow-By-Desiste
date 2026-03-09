<?php
$pageTitle = 'Modifica Docente';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT u.*, tp.bio, tp.total_assigned_hours, tp.total_completed_hours
    FROM users u
    LEFT JOIN teacher_profiles tp ON tp.user_id = u.id
    WHERE u.id = ? AND u.role = 'docente' LIMIT 1");
$stmt->execute([$id]);
$teacher = $stmt->fetch();

if (!$teacher) {
    set_flash('danger', 'Docente non trovato.');
    redirect('admin/docenti.php');
}
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post" action="<?= APP_URL ?>/actions/docente-save.php" class="row g-3">
                    <input type="hidden" name="id" value="<?= (int)$teacher['id'] ?>">

                    <div class="col-12 col-md-6">
                        <label class="form-label">Nome e cognome *</label>
                        <input type="text" name="full_name" class="form-control" value="<?= e($teacher['full_name']) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?= e($teacher['email']) ?>" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nuova password (opzionale)</label>
                        <input type="password" name="password" class="form-control" placeholder="Lascia vuoto per non modificarla">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Ore assegnate</label>
                        <input type="number" step="0.5" min="0" name="total_assigned_hours" class="form-control" value="<?= e((string)($teacher['total_assigned_hours'] ?? 0)) ?>">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Ore svolte</label>
                        <input type="number" step="0.5" min="0" name="total_completed_hours" class="form-control" value="<?= e((string)($teacher['total_completed_hours'] ?? 0)) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio professionale</label>
                        <textarea name="bio" class="form-control" rows="4"><?= e((string)($teacher['bio'] ?? '')) ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= (int)$teacher['is_active'] === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Docente attivo</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check2-circle me-2"></i>Aggiorna docente</button>
                        <a href="<?= APP_URL ?>/admin/docenti.php" class="btn btn-outline-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
