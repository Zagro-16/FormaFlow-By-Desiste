<?php
require_once __DIR__ . '/../config/config.php';
require_role('corsista');

if (!is_post()) {
    redirect('corsista/quiz.php');
}

$quizId = (int)($_POST['quiz_id'] ?? 0);
$studentId = (int)$_SESSION['user']['id'];
if ($quizId <= 0) {
    set_flash('danger', 'Quiz non valido.');
    redirect('corsista/quiz.php');
}

try {
    // verifica che quiz sia disponibile per il corsista
    $check = $pdo->prepare("SELECT q.id FROM quizzes q
        JOIN enrollments e ON e.course_id=q.course_id
        WHERE q.id=? AND q.published=1 AND e.student_id=?");
    $check->execute([$quizId, $studentId]);
    if (!$check->fetch()) {
        set_flash('danger', 'Quiz non disponibile per il tuo profilo.');
        redirect('corsista/quiz.php');
    }

    // punteggio demo realistico (in attesa motore risposte completo)
    $score = rand(65, 100);
    $ins = $pdo->prepare('INSERT INTO quiz_attempts (quiz_id, student_id, score, completed_at) VALUES (?, ?, ?, NOW())');
    $ins->execute([$quizId, $studentId, $score]);

    set_flash('success', "Quiz inviato. Punteggio registrato: {$score}%.");
} catch (Throwable $e) {
    set_flash('danger', 'Errore invio quiz: ' . $e->getMessage());
}

redirect('corsista/quiz.php');
