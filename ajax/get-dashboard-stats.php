<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$role = $_SESSION['user']['role'];
$userId = (int)$_SESSION['user']['id'];

try {
    if ($role === 'admin') {
        $stmt = $pdo->query("SELECT
            (SELECT COUNT(*) FROM courses) AS courses,
            (SELECT COUNT(*) FROM users WHERE role='docente') AS teachers,
            (SELECT COUNT(*) FROM users WHERE role='corsista') AS students,
            (SELECT COUNT(*) FROM lessons WHERE lesson_date >= CURDATE()) AS upcoming_lessons,
            (SELECT COUNT(*) FROM certificates) AS certificates,
            (SELECT COUNT(*) FROM quiz_attempts) AS quiz_attempts");
        echo json_encode($stmt->fetch());
        exit;
    }

    if ($role === 'docente') {
        $stmt = $pdo->prepare("SELECT
            (SELECT COUNT(*) FROM courses WHERE teacher_id = ?) AS courses,
            (SELECT COUNT(*) FROM lessons l JOIN courses c ON c.id=l.course_id WHERE c.teacher_id = ? AND l.lesson_date >= CURDATE()) AS upcoming_lessons,
            (SELECT COALESCE(SUM(total_hours),0) FROM courses WHERE teacher_id = ?) AS assigned_hours");
        $stmt->execute([$userId, $userId, $userId]);
        echo json_encode($stmt->fetch());
        exit;
    }

    $stmt = $pdo->prepare("SELECT
        (SELECT COUNT(*) FROM enrollments WHERE student_id = ?) AS enrolled_courses,
        (SELECT COUNT(*) FROM lessons l JOIN enrollments e ON e.course_id=l.course_id WHERE e.student_id = ? AND l.lesson_date >= CURDATE()) AS upcoming_lessons,
        (SELECT COUNT(*) FROM certificates WHERE student_id = ?) AS certificates,
        (SELECT COUNT(*) FROM quiz_attempts WHERE student_id = ?) AS quiz_attempts");
    $stmt->execute([$userId, $userId, $userId, $userId]);
    echo json_encode($stmt->fetch());
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore caricamento statistiche']);
}
