<?php
$pageTitle = 'Presenze';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$lessonId = (int)($_GET['lesson_id'] ?? 0);
$lessons = $pdo->query("SELECT l.id, CONCAT(c.title, ' - ', l.title, ' (', l.lesson_date, ')') AS full_label
    FROM lessons l JOIN courses c ON c.id = l.course_id ORDER BY l.lesson_date DESC")->fetchAll();

$students = [];
$attendance = [];
if ($lessonId > 0) {
    $studentsStmt = $pdo->prepare("SELECT u.id, u.full_name
        FROM enrollments e
        JOIN users u ON u.id = e.student_id
        JOIN lessons l ON l.course_id = e.course_id
        WHERE l.id = ? AND e.status IN ('active', 'completed')
        ORDER BY u.full_name");
    $studentsStmt->execute([$lessonId]);
    $students = $studentsStmt->fetchAll();

    $attStmt = $pdo->prepare('SELECT * FROM attendance WHERE lesson_id = ?');
    $attStmt->execute([$lessonId]);
    foreach ($attStmt->fetchAll() as $row) {
        $attendance[(int)$row['student_id']] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-admin.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-6">
        <select name="lesson_id" class="form-select" onchange="this.form.submit()">
            <option value="0">Seleziona lezione</option>
            <?php foreach($lessons as $l): ?><option value="<?= (int)$l['id'] ?>" <?= $lessonId===(int)$l['id']?'selected':'' ?>><?= e($l['full_label']) ?></option><?php endforeach; ?>
        </select>
    </div>
</form>

<?php if ($lessonId > 0): ?>
<div class="card"><div class="card-body table-responsive">
    <table class="table">
        <thead><tr><th>Corsista</th><th>Stato</th><th>Azioni</th></tr></thead>
        <tbody>
            <?php foreach($students as $student): $current = $attendance[(int)$student['id']]['status'] ?? 'present'; ?>
            <tr>
                <td><?= e($student['full_name']) ?></td>
                <td><?= e($current) ?></td>
                <td>
                    <form method="post" action="<?= APP_URL ?>/actions/presenza-save.php" class="d-flex gap-2">
                        <input type="hidden" name="lesson_id" value="<?= $lessonId ?>">
                        <input type="hidden" name="student_id" value="<?= (int)$student['id'] ?>">
                        <select name="status" class="form-select form-select-sm" style="max-width:130px;">
                            <?php foreach(['present','late','absent'] as $st): ?><option value="<?= $st ?>" <?= $current===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-primary">Salva</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div></div>
<?php endif; ?>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
