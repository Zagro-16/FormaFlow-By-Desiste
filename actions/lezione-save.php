<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin']);

if (!is_post()) {
    redirect('admin/lezioni.php');
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$courseId = (int)($_POST['course_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$lessonDate = $_POST['lesson_date'] ?? null;
$startTime = $_POST['start_time'] ?? null;
$endTime = $_POST['end_time'] ?? null;
$status = $_POST['status'] ?? 'scheduled';
$meetLink = trim($_POST['google_meet_link'] ?? '');

$allowedStatuses = ['scheduled', 'completed', 'cancelled'];
if ($courseId <= 0 || $title === '' || !$lessonDate || !$startTime || !$endTime || !in_array($status, $allowedStatuses, true)) {
    set_flash('danger', 'Dati lezione non validi.');
    redirect($id > 0 ? 'admin/lezione-edit.php?id=' . $id : 'admin/lezione-new.php');
}

if ($endTime <= $startTime) {
    set_flash('danger', 'L\'ora di fine deve essere successiva all\'ora di inizio.');
    redirect($id > 0 ? 'admin/lezione-edit.php?id=' . $id : 'admin/lezione-new.php');
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE lessons SET course_id = ?, title = ?, lesson_date = ?, start_time = ?, end_time = ?, status = ?, google_meet_link = ? WHERE id = ?');
        $stmt->execute([$courseId, $title, $lessonDate, $startTime, $endTime, $status, $meetLink ?: null, $id]);
        set_flash('success', 'Lezione aggiornata con successo.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO lessons (course_id, title, lesson_date, start_time, end_time, status, google_meet_link) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$courseId, $title, $lessonDate, $startTime, $endTime, $status, $meetLink ?: null]);
        set_flash('success', 'Lezione creata con successo.');
    }
} catch (Throwable $e) {
    set_flash('danger', 'Errore durante il salvataggio della lezione.');
}

redirect('admin/lezioni.php');
