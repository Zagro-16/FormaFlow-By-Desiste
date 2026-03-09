<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin', 'docente']);
header('Content-Type: application/json; charset=utf-8');

$lesson = (int)($_POST['lesson_id'] ?? 0);
$student = (int)($_POST['student_id'] ?? 0);

if ($lesson <= 0 || $student <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Parametri non validi']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO attendance (lesson_id,student_id,status,via_qr,checkin_at) VALUES (?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE status=VALUES(status),via_qr=1,checkin_at=NOW()');
    $stmt->execute([$lesson, $student, 'present', 1]);
    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Errore salvataggio check-in']);
}
