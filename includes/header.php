<?php require_once __DIR__ . '/../config/config.php'; require_login(); $pageTitle=$pageTitle??'Dashboard'; ?>
<!doctype html><html lang="it"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($pageTitle) ?> - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css"><link rel="stylesheet" href="<?= APP_URL ?>/assets/css/dashboard.css"></head><body>
<div class="app-shell">