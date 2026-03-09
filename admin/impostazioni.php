<?php
$pageTitle='Impostazioni';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$rows=$pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
$settings=[]; foreach($rows as $r){$settings[$r['setting_key']]=$r['setting_value'];}
?>
<?php include __DIR__.'/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__.'/../includes/sidebar-admin.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="post" action="<?= APP_URL ?>/actions/settings-save.php" class="row g-3">
<div class="col-md-6"><label class="form-label">Nome ente</label><input class="form-control" name="ente_nome" value="<?= e($settings['ente_nome'] ?? '') ?>"></div>
<div class="col-md-6"><label class="form-label">Email mittente</label><input class="form-control" name="email_from" value="<?= e($settings['email_from'] ?? '') ?>"></div>
<div class="col-md-6"><label class="form-label">Firma responsabile</label><input class="form-control" name="firma_responsabile" value="<?= e($settings['firma_responsabile'] ?? '') ?>"></div>
<div class="col-md-6"><label class="form-label">SMTP host</label><input class="form-control" name="smtp_host" value="<?= e($settings['smtp_host'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">SMTP port</label><input class="form-control" name="smtp_port" value="<?= e($settings['smtp_port'] ?? '587') ?>"></div>
<div class="col-md-3"><label class="form-label">SMTP user</label><input class="form-control" name="smtp_user" value="<?= e($settings['smtp_user'] ?? '') ?>"></div>
<div class="col-md-6"><label class="form-label">SMTP password</label><input type="password" class="form-control" name="smtp_pass" value="<?= e($settings['smtp_pass'] ?? '') ?>"></div>
<div class="col-12"><button class="btn btn-primary">Salva impostazioni</button></div>
</form></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
