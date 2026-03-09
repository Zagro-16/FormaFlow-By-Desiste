<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin', 'docente']);

if (!is_post()) {
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/comunicazioni.php');
}

$template = trim($_POST['template'] ?? 'general');
$courseId = (int)($_POST['course_id'] ?? 0);
$subject = trim($_POST['subject'] ?? 'Comunicazione FormaFlow');
$message = trim($_POST['message'] ?? '');

if ($message === '') {
    set_flash('danger', 'Inserisci un messaggio da inviare.');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/comunicazioni.php');
}

try {
    if ($courseId > 0) {
        $usersStmt = $pdo->prepare("SELECT DISTINCT u.id, u.email
            FROM users u
            LEFT JOIN enrollments e ON e.student_id = u.id
            LEFT JOIN courses c ON c.id = e.course_id
            WHERE (u.role='docente' AND u.id=c.teacher_id) OR (u.role='corsista' AND e.course_id=?)");
        $usersStmt->execute([$courseId]);
    } else {
        $usersStmt = $pdo->query("SELECT id, email FROM users WHERE role IN ('docente','corsista') AND is_active=1");
    }

    $users = $usersStmt->fetchAll();
    if (!$users) {
        set_flash('warning', 'Nessun destinatario trovato.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'admin/comunicazioni.php');
    }

    $insert = $pdo->prepare('INSERT INTO email_logs (lesson_id, user_id, email_to, subject, body, status, sent_at) VALUES (NULL, ?, ?, ?, ?, ?, NOW())');
    $sent = 0;
    foreach ($users as $u) {
        if (empty($u['email'])) {
            continue;
        }
        // In questa modalità registriamo l'invio nel log; l'integrazione SMTP reale resta su cron/PHPMailer.
        $insert->execute([(int)$u['id'], $u['email'], $subject, "[{$template}] {$message}", 'sent']);
        $sent++;
    }

    set_flash('success', "Comunicazione registrata/inviata a {$sent} destinatari.");
} catch (Throwable $e) {
    set_flash('danger', 'Errore invio comunicazione: ' . $e->getMessage());
}

redirect($_SERVER['HTTP_REFERER'] ?? 'admin/comunicazioni.php');
