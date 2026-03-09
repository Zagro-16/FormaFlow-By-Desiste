<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin', 'docente']);
header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
$role = trim($_GET['role'] ?? '');
if ($q === '' || mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $sql = 'SELECT id, full_name, email, role FROM users WHERE (full_name LIKE ? OR email LIKE ?)';
    $params = ["%{$q}%", "%{$q}%"];

    if (in_array($role, ['admin', 'docente', 'corsista'], true)) {
        $sql .= ' AND role = ?';
        $params[] = $role;
    }

    if ($_SESSION['user']['role'] === 'docente') {
        $sql .= " AND role = 'corsista'";
    }

    $sql .= ' ORDER BY full_name LIMIT 20';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll());
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore ricerca utenti']);
}
