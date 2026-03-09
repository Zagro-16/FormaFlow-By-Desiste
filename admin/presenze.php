<?php
$pageTitle = 'Presenze';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$lessonId = (int)($_GET['lesson_id'] ?? 0);
$statusFilter = $_GET['status'] ?? '';

$lessons = $pdo->query("SELECT l.id, CONCAT(c.title, ' - ', l.title, ' (', l.lesson_date, ')') AS full_label
    FROM lessons l JOIN courses c ON c.id = l.course_id ORDER BY l.lesson_date DESC")->fetchAll();

$students = [];
$attendance = [];
if ($lessonId > 0) {
    $studentsStmt = $pdo->prepare("SELECT DISTINCT u.id, u.full_name, u.email
        FROM enrollments e
        JOIN users u ON u.id = e.student_id
        JOIN lessons l ON l.course_id = e.course_id
        WHERE l.id = ? AND e.status IN ('active','completed')
        ORDER BY u.full_name");
    $studentsStmt->execute([$lessonId]);
    $students = $studentsStmt->fetchAll();

    $attStmt = $pdo->prepare('SELECT * FROM attendance WHERE lesson_id = ?');
    $attStmt->execute([$lessonId]);
    foreach ($attStmt->fetchAll() as $row) {
        if ($statusFilter !== '' && $row['status'] !== $statusFilter) {
            continue;
        }
        $attendance[(int)$row['student_id']] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-admin.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<form class="row g-2 mb-3" method="get">
    <div class="col-12 col-md-6">
        <select name="lesson_id" class="form-select" onchange="this.form.submit()">
            <option value="0">Seleziona lezione</option>
            <?php foreach($lessons as $l): ?><option value="<?= (int)$l['id'] ?>" <?= $lessonId===(int)$l['id']?'selected':'' ?>><?= e($l['full_label']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-8 col-md-3">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">Tutti gli stati</option>
            <?php foreach(['present','late','absent'] as $st): ?><option value="<?= $st ?>" <?= $statusFilter===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
        </select>
    </div>
</form>

<?php if ($lessonId > 0): ?>
<div class="card border-0 shadow-sm"><div class="card-body table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Corsista</th><th>Stato attuale</th><th>Check-in</th><th>Via QR</th><th>Aggiorna</th></tr></thead>
        <tbody>
            <?php foreach($students as $student):
                $row = $attendance[(int)$student['id']] ?? null;
                $current = $row['status'] ?? 'present';
            ?>
            <tr>
                <td><div class="fw-semibold"><?= e($student['full_name']) ?></div><div class="small text-muted"><?= e($student['email']) ?></div></td>
                <td><span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle"><?= e($current) ?></span></td>
                <td><?= $row ? e((string)$row['checkin_at']) : '-' ?></td>
                <td><?= $row && (int)$row['via_qr'] === 1 ? 'Sì' : 'No' ?></td>
                <td>
                    <form method="post" action="<?= APP_URL ?>/actions/presenza-save.php" class="d-flex flex-wrap gap-2">
                        <input type="hidden" name="lesson_id" value="<?= $lessonId ?>">
                        <input type="hidden" name="student_id" value="<?= (int)$student['id'] ?>">
                        <select name="status" class="form-select form-select-sm" style="max-width:130px;">
                            <?php foreach(['present','late','absent'] as $st): ?><option value="<?= $st ?>" <?= $current===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
                        </select>
                        <label class="form-check form-switch d-flex align-items-center ms-1">
                            <input class="form-check-input" type="checkbox" name="via_qr" <?= $row && (int)$row['via_qr']===1?'checked':'' ?>>
                            <span class="small ms-2">QR</span>
                        </label>
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
