<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$stmt=$pdo->query("SELECT DATE_FORMAT(created_at,'%Y-%m') month, COUNT(*) total FROM enrollments GROUP BY month ORDER BY month"); echo json_encode($stmt->fetchAll());
