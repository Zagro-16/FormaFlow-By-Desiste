<?php
$pageTitle='Corsista - Profilo';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');
$uid=(int)$_SESSION['user']['id'];
$st=$pdo->prepare('SELECT u.full_name,u.email,sp.phone,sp.birth_date FROM users u LEFT JOIN student_profiles sp ON sp.user_id=u.id WHERE u.id=?');$st->execute([$uid]);$user=$st->fetch();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-corsista.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body"><div class="row g-3"><div class="col-md-6"><label class="form-label">Nome</label><input class="form-control" value="<?= e($user['full_name']??'') ?>" disabled></div><div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="<?= e($user['email']??'') ?>" disabled></div><div class="col-md-6"><label class="form-label">Telefono</label><input class="form-control" value="<?= e((string)($user['phone']??'')) ?>" disabled></div><div class="col-md-6"><label class="form-label">Data nascita</label><input class="form-control" value="<?= e((string)($user['birth_date']??'')) ?>" disabled></div></div></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
