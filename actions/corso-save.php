<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);

if (!is_post()) {
    redirect('admin/corsi.php');
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$startDate = $_POST['start_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;
$status = $_POST['status'] ?? 'draft';
$teacherId = !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : null;
$totalHours = is_numeric($_POST['total_hours'] ?? null) ? (float)$_POST['total_hours'] : 0;

$allowedStatuses = ['draft', 'active', 'completed', 'cancelled'];
if ($title === '' || !in_array($status, $allowedStatuses, true)) {
    set_flash('danger', 'Dati corso non validi.');
    redirect($id > 0 ? 'admin/corso-edit.php?id=' . $id : 'admin/corso-new.php');
}

if ($startDate && $endDate && $startDate > $endDate) {
    set_flash('danger', 'La data fine deve essere successiva alla data inizio.');
    redirect($id > 0 ? 'admin/corso-edit.php?id=' . $id : 'admin/corso-new.php');
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE courses SET title = ?, description = ?, start_date = ?, end_date = ?, status = ?, teacher_id = ?, total_hours = ? WHERE id = ?');
        $stmt->execute([$title, $description ?: null, $startDate ?: null, $endDate ?: null, $status, $teacherId, $totalHours, $id]);
        set_flash('success', 'Corso aggiornato con successo.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO courses (title, description, start_date, end_date, status, teacher_id, total_hours) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description ?: null, $startDate ?: null, $endDate ?: null, $status, $teacherId, $totalHours]);
        set_flash('success', 'Corso creato con successo.');
    }
} catch (Throwable $e) {
    set_flash('danger', 'Errore durante il salvataggio del corso.');
}

redirect('admin/corsi.php');
