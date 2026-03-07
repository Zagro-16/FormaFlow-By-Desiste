<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$id=(int)($_GET['course_id'] ?? 0); $stmt=$pdo->prepare('SELECT id,title,lesson_date,start_time,end_time FROM lessons WHERE course_id=?'); $stmt->execute([$id]); echo json_encode($stmt->fetchAll());
