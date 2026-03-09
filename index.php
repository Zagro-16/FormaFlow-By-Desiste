<?php require_once __DIR__ . '/config/config.php';
if (!empty($_SESSION['user'])) redirect(role_dashboard($_SESSION['user']['role']));
$flash=get_flash(); ?>
<!doctype html><html lang="it"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css"></head><body class="bg-light">
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-5">
<div class="card shadow-sm"><div class="card-body p-4"><h1 class="h4 text-center mb-3"><?= APP_NAME ?></h1>
<?php if($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<form method="post" action="actions/login-action.php">
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<button class="btn btn-primary w-100">Accedi</button></form></div></div></div></div></div></body></html>