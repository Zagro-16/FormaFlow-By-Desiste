<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$stmt=$pdo->query("SELECT l.id, l.title, CONCAT(l.lesson_date,' ',l.start_time) start, CONCAT(l.lesson_date,' ',l.end_time) end FROM lessons l"); echo json_encode($stmt->fetchAll());
