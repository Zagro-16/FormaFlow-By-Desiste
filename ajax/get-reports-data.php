<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);
header('Content-Type: application/json; charset=utf-8');

$range = $_GET['range'] ?? '12m';
$interval = match ($range) {
    '3m' => '3 MONTH',
    '6m' => '6 MONTH',
    default => '12 MONTH',
};

try {
    $enrollStmt = $pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') AS label, COUNT(*) AS total
        FROM enrollments
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL {$interval})
        GROUP BY DATE_FORMAT(created_at,'%Y-%m')
        ORDER BY label");

    $courseStmt = $pdo->query("SELECT status AS label, COUNT(*) AS total
        FROM courses
        GROUP BY status");

    $attendanceStmt = $pdo->query("SELECT status AS label, COUNT(*) AS total
        FROM attendance
        GROUP BY status");

    echo json_encode([
        'enrollments_trend' => $enrollStmt->fetchAll(),
        'courses_status' => $courseStmt->fetchAll(),
        'attendance_status' => $attendanceStmt->fetchAll(),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore generazione report']);
}
