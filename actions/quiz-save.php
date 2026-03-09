<?php
require_once __DIR__ . '/../config/config.php';
require_role(['admin','docente']);

if (!is_post()) {
    redirect('dashboard.php');
}

$quizId = (int)($_POST['quiz_id'] ?? 0);
$courseId = (int)($_POST['course_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$published = (int)($_POST['published'] ?? 0) === 1 ? 1 : 0;

if ($courseId <= 0 || $title === '') {
    set_flash('danger', 'Dati quiz non validi.');
    redirect($_SERVER['HTTP_REFERER'] ?? 'admin/quiz.php');
}

try {
    // docente può operare solo sui suoi corsi
    if ($_SESSION['user']['role'] === 'docente') {
        $check = $pdo->prepare('SELECT id FROM courses WHERE id = ? AND teacher_id = ?');
        $check->execute([$courseId, (int)$_SESSION['user']['id']]);
        if (!$check->fetch()) {
            set_flash('danger', 'Non puoi creare quiz su corsi non assegnati.');
            redirect($_SERVER['HTTP_REFERER'] ?? 'docente/quiz.php');
        }
    }

    if ($quizId > 0) {
        $stmt = $pdo->prepare('UPDATE quizzes SET course_id=?, title=?, published=? WHERE id=?');
        $stmt->execute([$courseId, $title, $published, $quizId]);
        set_flash('success', 'Quiz aggiornato.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO quizzes (course_id,title,published) VALUES (?,?,?)');
        $stmt->execute([$courseId, $title, $published]);
        $quizId = (int)$pdo->lastInsertId();
        set_flash('success', 'Quiz creato con successo.');
    }

    // gestione domande opzionali via textarea JSON
    $questionsJson = trim($_POST['questions_json'] ?? '');
    if ($questionsJson !== '') {
        $items = json_decode($questionsJson, true);
        if (is_array($items)) {
            $pdo->prepare('DELETE FROM quiz_questions WHERE quiz_id=?')->execute([$quizId]);
            $ins = $pdo->prepare('INSERT INTO quiz_questions (quiz_id,question_text,option_a,option_b,option_c,option_d,correct_option) VALUES (?,?,?,?,?,?,?)');
            foreach ($items as $q) {
                if (empty($q['question_text']) || empty($q['correct_option'])) continue;
                $ins->execute([
                    $quizId,
                    $q['question_text'],
                    $q['option_a'] ?? null,
                    $q['option_b'] ?? null,
                    $q['option_c'] ?? null,
                    $q['option_d'] ?? null,
                    strtoupper((string)$q['correct_option']),
                ]);
            }
        }
    }
} catch (Throwable $e) {
    set_flash('danger', 'Errore salvataggio quiz: ' . $e->getMessage());
}

redirect($_SERVER['HTTP_REFERER'] ?? ($_SESSION['user']['role']==='admin' ? 'admin/quiz.php' : 'docente/quiz.php'));
