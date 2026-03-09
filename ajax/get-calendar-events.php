<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$role = $_SESSION['user']['role'];
$userId = (int)$_SESSION['user']['id'];

try {
    if ($role === 'admin') {
        $stmt = $pdo->query("SELECT l.id, l.title, CONCAT(l.lesson_date,' ',l.start_time) AS start, CONCAT(l.lesson_date,' ',l.end_time) AS end, c.title AS course_title
            FROM lessons l JOIN courses c ON c.id = l.course_id");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($role === 'docente') {
        $stmt = $pdo->prepare("SELECT l.id, l.title, CONCAT(l.lesson_date,' ',l.start_time) AS start, CONCAT(l.lesson_date,' ',l.end_time) AS end, c.title AS course_title
            FROM lessons l JOIN courses c ON c.id = l.course_id WHERE c.teacher_id = ?");
        $stmt->execute([$userId]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    $stmt = $pdo->prepare("SELECT l.id, l.title, CONCAT(l.lesson_date,' ',l.start_time) AS start, CONCAT(l.lesson_date,' ',l.end_time) AS end, c.title AS course_title
        FROM lessons l
        JOIN courses c ON c.id = l.course_id
        JOIN enrollments e ON e.course_id = c.id
        WHERE e.student_id = ?");
    $stmt->execute([$userId]);
    echo json_encode($stmt->fetchAll());
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore caricamento eventi calendario']);
}
