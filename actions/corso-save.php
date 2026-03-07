<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin','docente']);
if (!is_post()) redirect('dashboard.php');
$data = $_POST;
try {
    $stmt=$pdo->prepare('INSERT INTO courses (title,description,start_date,end_date,status,teacher_id,total_hours) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$data['title'] ?? null,$data['description'] ?? null,$data['start_date'] ?? null,$data['end_date'] ?? null,$data['status'] ?? null,$data['teacher_id'] ?? null,$data['total_hours'] ?? null]);
    set_flash('success','Salvataggio completato');
} catch (Throwable $e) { set_flash('danger','Errore: '.$e->getMessage()); }
redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard.php');
