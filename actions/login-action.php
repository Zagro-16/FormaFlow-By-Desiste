<?php
require_once __DIR__ . '/../config/config.php';
if (!is_post()) redirect('index.php');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password_hash'])) { set_flash('danger','Credenziali non valide'); redirect('index.php'); }
$_SESSION['user'] = ['id'=>$user['id'],'full_name'=>$user['full_name'],'email'=>$user['email'],'role'=>$user['role']];
set_flash('success','Benvenuto '.$user['full_name']);
redirect(role_dashboard($user['role']));
