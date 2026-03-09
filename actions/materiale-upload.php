<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin', 'docente']);

if (!is_post() || empty($_FILES['materiale'])) {
    redirect('admin/materiali.php');
}

$courseId = (int)($_POST['course_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$file = $_FILES['materiale'];

if ($courseId <= 0) {
    set_flash('danger', 'Seleziona un corso valido.');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/materiali.php');
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    set_flash('danger', 'Upload fallito.');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/materiali.php');
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'xlsx', 'xls', 'txt'];
if (!in_array($ext, $allowed, true)) {
    set_flash('danger', 'Formato file non consentito.');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/materiali.php');
}

if ($file['size'] > (25 * 1024 * 1024)) {
    set_flash('danger', 'File troppo grande (max 25MB).');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/materiali.php');
}

$targetDir = __DIR__ . '/../uploads/materiali';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0775, true);
}

$safeName = 'mat_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$relativePath = 'uploads/materiali/' . $safeName;
$fullPath = __DIR__ . '/../' . $relativePath;

if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
    set_flash('danger', 'Impossibile salvare il file sul server.');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/materiali.php');
}

try {
    $stmt = $pdo->prepare('INSERT INTO materials (course_id, uploader_id, title, file_path) VALUES (?, ?, ?, ?)');
    $stmt->execute([$courseId, (int)$_SESSION['user']['id'], $title !== '' ? $title : $file['name'], $relativePath]);
    set_flash('success', 'Materiale caricato con successo.');
} catch (Throwable $e) {
    @unlink($fullPath);
    set_flash('danger', 'Errore salvataggio materiale.');
}

redirect($_SERVER['HTTP_REFERER'] ?? 'admin/materiali.php');
