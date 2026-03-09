<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);

if (!is_post()) {
    redirect('admin/docenti.php');
}

$id = (int)($_POST['id'] ?? 0);
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$bio = trim($_POST['bio'] ?? '');
$totalAssigned = is_numeric($_POST['total_assigned_hours'] ?? null) ? (float)$_POST['total_assigned_hours'] : 0;
$totalCompleted = is_numeric($_POST['total_completed_hours'] ?? null) ? (float)$_POST['total_completed_hours'] : 0;
$isActive = isset($_POST['is_active']) ? 1 : 0;

if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Nome ed email validi sono obbligatori.');
    redirect($id > 0 ? 'admin/docente-edit.php?id=' . $id : 'admin/docente-new.php');
}

try {
    if ($id > 0) {
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, password_hash = ?, is_active = ? WHERE id = ? AND role = 'docente'");
            $stmt->execute([$fullName, $email, $passwordHash, $isActive, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, is_active = ? WHERE id = ? AND role = 'docente'");
            $stmt->execute([$fullName, $email, $isActive, $id]);
        }

        $profileStmt = $pdo->prepare('INSERT INTO teacher_profiles (user_id, bio, total_assigned_hours, total_completed_hours) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE bio = VALUES(bio), total_assigned_hours = VALUES(total_assigned_hours), total_completed_hours = VALUES(total_completed_hours)');
        $profileStmt->execute([$id, $bio ?: null, $totalAssigned, $totalCompleted]);

        set_flash('success', 'Docente aggiornato con successo.');
    } else {
        if ($password === '') {
            set_flash('danger', 'Password obbligatoria per creare un docente.');
            redirect('admin/docente-new.php');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'docente', ?)");
        $stmt->execute([$fullName, $email, $passwordHash, $isActive]);
        $newId = (int)$pdo->lastInsertId();

        $profileStmt = $pdo->prepare('INSERT INTO teacher_profiles (user_id, bio, total_assigned_hours, total_completed_hours) VALUES (?, ?, ?, ?)');
        $profileStmt->execute([$newId, $bio ?: null, $totalAssigned, $totalCompleted]);

        set_flash('success', 'Docente creato con successo.');
    }
} catch (Throwable $e) {
    set_flash('danger', 'Errore durante il salvataggio del docente: ' . $e->getMessage());
}

redirect('admin/docenti.php');
