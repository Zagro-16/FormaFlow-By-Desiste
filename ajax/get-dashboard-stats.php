<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$stmt=$pdo->query("SELECT (SELECT COUNT(*) FROM courses) courses, (SELECT COUNT(*) FROM users WHERE role='docente') teachers, (SELECT COUNT(*) FROM users WHERE role='corsista') students"); echo json_encode($stmt->fetchAll());
