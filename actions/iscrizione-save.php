<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);

if (!is_post()) {
    redirect('admin/iscrizioni.php');
}

$enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
$courseId = (int)($_POST['course_id'] ?? 0);
$studentId = (int)($_POST['student_id'] ?? 0);
$status = $_POST['status'] ?? 'active';

$allowedStatuses = ['active', 'completed', 'withdrawn'];
if ($courseId <= 0 || $studentId <= 0 || !in_array($status, $allowedStatuses, true)) {
    set_flash('danger', 'Dati iscrizione non validi.');
    redirect('admin/iscrizioni.php');
}

try {
    if ($enrollmentId > 0) {
        $stmt = $pdo->prepare('UPDATE enrollments SET course_id = ?, student_id = ?, status = ? WHERE id = ?');
        $stmt->execute([$courseId, $studentId, $status, $enrollmentId]);
        set_flash('success', 'Iscrizione aggiornata con successo.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO enrollments (course_id, student_id, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)');
        $stmt->execute([$courseId, $studentId, $status]);
        set_flash('success', 'Iscrizione salvata con successo.');
    }
} catch (Throwable $e) {
    set_flash('danger', 'Errore salvataggio iscrizione: ' . $e->getMessage());
}

redirect('admin/iscrizioni.php');
