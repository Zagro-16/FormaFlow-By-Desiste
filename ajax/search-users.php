<?php
require_once __DIR__ . '/../config/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$term='%'.trim($_GET['q'] ?? '').'%'; $stmt=$pdo->prepare('SELECT id, full_name, email, role FROM users WHERE full_name LIKE ? OR email LIKE ? LIMIT 20'); $stmt->execute([$term,$term]); echo json_encode($stmt->fetchAll());
