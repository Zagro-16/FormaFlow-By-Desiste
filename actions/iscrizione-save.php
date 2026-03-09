<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);

if (!is_post()) {
    redirect('admin/iscrizioni.php');
}

$courseId = (int)($_POST['course_id'] ?? 0);
$studentId = (int)($_POST['student_id'] ?? 0);
$status = $_POST['status'] ?? 'active';

if ($courseId <= 0 || $studentId <= 0 || !in_array($status, ['active', 'completed', 'withdrawn'], true)) {
    set_flash('danger', 'Dati iscrizione non validi.');
    redirect('admin/iscrizioni.php');
}

try {
    $stmt = $pdo->prepare('INSERT INTO enrollments (course_id, student_id, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)');
    $stmt->execute([$courseId, $studentId, $status]);
    set_flash('success', 'Iscrizione salvata.');
} catch (Throwable $e) {
    set_flash('danger', 'Errore salvataggio iscrizione.');
}

redirect('admin/iscrizioni.php');
