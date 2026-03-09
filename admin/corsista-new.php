<?php
$pageTitle = 'Nuovo Corsista';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post" action="<?= APP_URL ?>/actions/corsista-save.php" class="row g-3">
                    <div class="col-12 col-md-6"><label class="form-label">Nome e cognome *</label><input type="text" name="full_name" class="form-control" required></div>
                    <div class="col-12 col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
                    <div class="col-12 col-md-6"><label class="form-label">Password iniziale *</label><input type="password" name="password" class="form-control" required></div>
                    <div class="col-12 col-md-3"><label class="form-label">Telefono</label><input type="text" name="phone" class="form-control"></div>
                    <div class="col-12 col-md-3"><label class="form-label">Data nascita</label><input type="date" name="birth_date" class="form-control"></div>
                    <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked><label class="form-check-label" for="is_active">Corsista attivo</label></div></div>
                    <div class="col-12 d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-check2-circle me-2"></i>Salva corsista</button><a href="<?= APP_URL ?>/admin/corsisti.php" class="btn btn-outline-secondary">Annulla</a></div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
