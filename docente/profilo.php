<?php
$pageTitle='Docente - Profilo';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
$uid=(int)$_SESSION['user']['id'];
$st=$pdo->prepare('SELECT u.full_name,u.email,tp.bio,tp.total_assigned_hours,tp.total_completed_hours FROM users u LEFT JOIN teacher_profiles tp ON tp.user_id=u.id WHERE u.id=?');$st->execute([$uid]);$u=$st->fetch();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body"><div class="row g-3"><div class="col-md-6"><label class="form-label">Nome</label><input class="form-control" value="<?= e($u['full_name']??'') ?>" disabled></div><div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="<?= e($u['email']??'') ?>" disabled></div><div class="col-md-6"><label class="form-label">Ore assegnate</label><input class="form-control" value="<?= e((string)($u['total_assigned_hours']??0)) ?>" disabled></div><div class="col-md-6"><label class="form-label">Ore svolte</label><input class="form-control" value="<?= e((string)($u['total_completed_hours']??0)) ?>" disabled></div><div class="col-12"><label class="form-label">Bio</label><textarea class="form-control" rows="4" disabled><?= e((string)($u['bio']??'')) ?></textarea></div></div></div></div>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
