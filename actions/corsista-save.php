<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);

if (!is_post()) {
    redirect('admin/corsisti.php');
}

$id = (int)($_POST['id'] ?? 0);
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$phone = trim($_POST['phone'] ?? '');
$birthDate = $_POST['birth_date'] ?? null;
$isActive = isset($_POST['is_active']) ? 1 : 0;

if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Nome ed email validi sono obbligatori.');
    redirect($id > 0 ? 'admin/corsista-edit.php?id=' . $id : 'admin/corsista-new.php');
}

if (!empty($birthDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthDate)) {
    set_flash('danger', 'Data di nascita non valida.');
    redirect($id > 0 ? 'admin/corsista-edit.php?id=' . $id : 'admin/corsista-new.php');
}

try {
    if ($id > 0) {
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, password_hash=?, is_active=? WHERE id=? AND role='corsista'");
            $stmt->execute([$fullName, $email, $passwordHash, $isActive, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, is_active=? WHERE id=? AND role='corsista'");
            $stmt->execute([$fullName, $email, $isActive, $id]);
        }

        $profileStmt = $pdo->prepare('INSERT INTO student_profiles (user_id, phone, birth_date) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE phone=VALUES(phone), birth_date=VALUES(birth_date)');
        $profileStmt->execute([$id, $phone ?: null, $birthDate ?: null]);

        set_flash('success', 'Corsista aggiornato con successo.');
    } else {
        if ($password === '') {
            set_flash('danger', 'Password obbligatoria per creare il corsista.');
            redirect('admin/corsista-new.php');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'corsista', ?)");
        $stmt->execute([$fullName, $email, $passwordHash, $isActive]);
        $newId = (int)$pdo->lastInsertId();

        $profileStmt = $pdo->prepare('INSERT INTO student_profiles (user_id, phone, birth_date) VALUES (?, ?, ?)');
        $profileStmt->execute([$newId, $phone ?: null, $birthDate ?: null]);

        set_flash('success', 'Corsista creato con successo.');
    }
} catch (Throwable $e) {
    set_flash('danger', 'Errore durante il salvataggio del corsista: ' . $e->getMessage());
}

redirect('admin/corsisti.php');
