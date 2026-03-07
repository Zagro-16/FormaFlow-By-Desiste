<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin','docente']);
if (!is_post()) redirect('dashboard.php');
$data = $_POST;
try {
    $stmt=$pdo->prepare('INSERT INTO lessons (course_id,title,lesson_date,start_time,end_time,status,google_meet_link) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$data['course_id'] ?? null,$data['title'] ?? null,$data['lesson_date'] ?? null,$data['start_time'] ?? null,$data['end_time'] ?? null,$data['status'] ?? null,$data['google_meet_link'] ?? null]);
    set_flash('success','Salvataggio completato');
} catch (Throwable $e) { set_flash('danger','Errore: '.$e->getMessage()); }
redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard.php');
