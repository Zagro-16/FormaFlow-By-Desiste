<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin', 'docente']);

if (!is_post()) {
    redirect('admin/presenze.php');
}

$lessonId = (int)($_POST['lesson_id'] ?? 0);
$studentId = (int)($_POST['student_id'] ?? 0);
$status = $_POST['status'] ?? 'present';
$viaQr = !empty($_POST['via_qr']) ? 1 : 0;

if ($lessonId <= 0 || $studentId <= 0 || !in_array($status, ['present', 'absent', 'late'], true)) {
    set_flash('danger', 'Dati presenza non validi.');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/presenze.php');
}

try {
    $stmt = $pdo->prepare('INSERT INTO attendance (lesson_id, student_id, status, via_qr, checkin_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE status = VALUES(status), via_qr = VALUES(via_qr), checkin_at = NOW()');
    $stmt->execute([$lessonId, $studentId, $status, $viaQr]);
    set_flash('success', 'Presenza salvata.');
} catch (Throwable $e) {
    set_flash('danger', 'Errore salvataggio presenza.');
}

redirect($_SERVER['HTTP_REFERER'] ?? 'admin/presenze.php');
