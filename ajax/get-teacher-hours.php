<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$teacherId = (int)($_GET['teacher_id'] ?? 0);
if ($teacherId <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'teacher_id non valido']);
    exit;
}

try {
    if ($_SESSION['user']['role'] === 'docente' && (int)$_SESSION['user']['id'] !== $teacherId) {
        http_response_code(403);
        echo json_encode(['error' => 'Accesso negato']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT tp.total_assigned_hours, tp.total_completed_hours,
        (SELECT COUNT(*) FROM courses c WHERE c.teacher_id = ?) AS courses_count
        FROM teacher_profiles tp
        WHERE tp.user_id = ?");
    $stmt->execute([$teacherId, $teacherId]);
    $data = $stmt->fetch();

    echo json_encode($data ?: ['total_assigned_hours' => 0, 'total_completed_hours' => 0, 'courses_count' => 0]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore recupero monte ore docente']);
}
