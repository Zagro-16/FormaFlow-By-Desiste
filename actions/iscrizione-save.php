<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin','docente']);
if (!is_post()) redirect('dashboard.php');
$data = $_POST;
try {
    $stmt=$pdo->prepare('INSERT INTO enrollments (course_id,student_id,status) VALUES (?,?,?)');
    $stmt->execute([$data['course_id'] ?? null,$data['student_id'] ?? null,$data['status'] ?? null]);
    set_flash('success','Salvataggio completato');
} catch (Throwable $e) { set_flash('danger','Errore: '.$e->getMessage()); }
redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard.php');
