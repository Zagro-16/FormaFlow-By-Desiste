<?php
$pageTitle = 'Gestione Lezioni';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$courseFilter = (int)($_GET['course_id'] ?? 0);
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT l.*, c.title AS course_title
        FROM lessons l
        JOIN courses c ON c.id = l.course_id
        WHERE 1=1";
$params = [];

if ($courseFilter > 0) {
    $sql .= ' AND l.course_id = ?';
    $params[] = $courseFilter;
}
if ($statusFilter !== '') {
    $sql .= ' AND l.status = ?';
    $params[] = $statusFilter;
}
$sql .= ' ORDER BY l.lesson_date DESC, l.start_time DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lessons = $stmt->fetchAll();

$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();
$pageAction = ['url' => APP_URL . '/admin/lezione-new.php', 'label' => 'Nuova Lezione'];
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <form class="row g-2 mb-3" method="get">
            <div class="col-md-4">
                <select class="form-select" name="course_id">
                    <option value="0">Tutti i corsi</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= (int)$course['id'] ?>" <?= $courseFilter === (int)$course['id'] ? 'selected' : '' ?>><?= e($course['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Tutti gli stati</option>
                    <?php foreach (['scheduled','completed','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Filtra</button></div>
        </form>

        <div class="card"><div class="card-body table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Lezione</th><th>Corso</th><th>Data</th><th>Orario</th><th>Meet</th><th>Stato</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($lessons as $lesson): ?>
                    <tr>
                        <td><?= e($lesson['title']) ?></td>
                        <td><?= e($lesson['course_title']) ?></td>
                        <td><?= e($lesson['lesson_date']) ?></td>
                        <td><?= e(substr($lesson['start_time'],0,5)) ?> - <?= e(substr($lesson['end_time'],0,5)) ?></td>
                        <td>
                            <?php if (!empty($lesson['google_meet_link'])): ?>
                                <a href="<?= e($lesson['google_meet_link']) ?>" target="_blank" rel="noopener">Apri</a>
                            <?php else: ?>-
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?= e($lesson['status']) ?></span></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= APP_URL ?>/admin/lezione-edit.php?id=<?= (int)$lesson['id'] ?>">Modifica</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
