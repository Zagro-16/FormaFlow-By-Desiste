<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin','docente']);
if (!is_post()) redirect('dashboard.php');
$data = $_POST;
try {
    $stmt=$pdo->prepare('INSERT INTO users (full_name,email) VALUES (?,?)');
    $stmt->execute([$data['full_name'] ?? null,$data['email'] ?? null]);
    set_flash('success','Salvataggio completato');
} catch (Throwable $e) { set_flash('danger','Errore: '.$e->getMessage()); }
redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard.php');
