<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$id=(int)($_GET['teacher_id'] ?? 0); $stmt=$pdo->prepare('SELECT total_assigned_hours,total_completed_hours FROM teacher_profiles WHERE user_id=?'); $stmt->execute([$id]); echo json_encode($stmt->fetchAll());
