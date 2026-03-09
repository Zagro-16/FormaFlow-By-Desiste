<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$courseId = (int)($_GET['course_id'] ?? 0);
if ($courseId <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'course_id non valido']);
    exit;
}

try {
    $sql = 'SELECT l.id,l.title,l.lesson_date,l.start_time,l.end_time,l.status,l.google_meet_link FROM lessons l WHERE l.course_id=?';
    $params = [$courseId];

    if ($_SESSION['user']['role'] === 'docente') {
        $sql = 'SELECT l.id,l.title,l.lesson_date,l.start_time,l.end_time,l.status,l.google_meet_link FROM lessons l JOIN courses c ON c.id=l.course_id WHERE l.course_id=? AND c.teacher_id=?';
        $params[] = (int)$_SESSION['user']['id'];
    } elseif ($_SESSION['user']['role'] === 'corsista') {
        $sql = 'SELECT l.id,l.title,l.lesson_date,l.start_time,l.end_time,l.status,l.google_meet_link FROM lessons l JOIN enrollments e ON e.course_id=l.course_id WHERE l.course_id=? AND e.student_id=?';
        $params[] = (int)$_SESSION['user']['id'];
    }

    $sql .= ' ORDER BY l.lesson_date, l.start_time';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll());
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore caricamento lezioni corso']);
}
