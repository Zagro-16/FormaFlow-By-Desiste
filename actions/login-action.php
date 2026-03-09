<?php
require_once __DIR__ . '/../config/config.php';

if (!is_post()) {
    redirect('index.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    set_flash('danger', 'Inserisci email e password.');
    redirect('index.php');
}

try {
    // Compatibilità con schemi "users" diversi:
    // alcuni DB usano full_name, altri name/nome.
    $columnsStmt = $pdo->query('SHOW COLUMNS FROM users');
    $columns = array_column($columnsStmt->fetchAll(), 'Field');

    $nameExpr = 'full_name';
    if (!in_array('full_name', $columns, true)) {
        if (in_array('name', $columns, true)) {
            $nameExpr = 'name';
        } elseif (in_array('nome', $columns, true)) {
            $nameExpr = 'nome';
        } else {
            $nameExpr = "''";
        }
    }

    $passwordColumn = in_array('password_hash', $columns, true) ? 'password_hash' : 'password';
    $activeCondition = in_array('is_active', $columns, true) ? ' AND is_active = 1' : '';

    $sql = "SELECT id, {$nameExpr} AS full_name, email, {$passwordColumn} AS password_hash, role
            FROM users
            WHERE email = ?{$activeCondition}
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        set_flash('danger', 'Credenziali non valide.');
        redirect('index.php');
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'full_name' => $user['full_name'] ?: 'Utente',
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    set_flash('success', 'Benvenuto ' . $_SESSION['user']['full_name']);
    redirect(role_dashboard($user['role']));
} catch (Throwable $e) {
    set_flash('danger', 'Errore durante il login. Verifica struttura tabella users e configurazione DB.');
    redirect('index.php');
}
