<?php
$pageTitle = 'Lezioni';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$courseFilter = (int)($_GET['course_id'] ?? 0);
$statusFilter = $_GET['status'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

$sql = "SELECT l.*, c.title AS course_title, u.full_name AS teacher_name
        FROM lessons l
        JOIN courses c ON c.id = l.course_id
        LEFT JOIN users u ON u.id = c.teacher_id
        WHERE 1=1";
$params = [];

if ($courseFilter > 0) { $sql .= ' AND l.course_id = ?'; $params[] = $courseFilter; }
if ($statusFilter !== '') { $sql .= ' AND l.status = ?'; $params[] = $statusFilter; }
if ($dateFrom !== '') { $sql .= ' AND l.lesson_date >= ?'; $params[] = $dateFrom; }
if ($dateTo !== '') { $sql .= ' AND l.lesson_date <= ?'; $params[] = $dateTo; }

$sql .= ' ORDER BY l.lesson_date DESC, l.start_time DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lessons = $stmt->fetchAll();

$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();

$summary = $pdo->query("SELECT
    COUNT(*) AS total_lessons,
    COUNT(CASE WHEN status='scheduled' THEN 1 END) AS scheduled_lessons,
    COUNT(CASE WHEN status='completed' THEN 1 END) AS completed_lessons
    FROM lessons")->fetch();

$pageAction = ['url' => APP_URL . '/admin/lezione-new.php', 'label' => 'Nuova Lezione'];
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="row g-3 mb-3">
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Totale lezioni</div><div class="stat-number"><?= (int)$summary['total_lessons'] ?></div></div></div></div>
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Programmate</div><div class="stat-number"><?= (int)$summary['scheduled_lessons'] ?></div></div></div></div>
            <div class="col-12 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Completate</div><div class="stat-number"><?= (int)$summary['completed_lessons'] ?></div></div></div></div>
        </div>

        <form class="row g-2 mb-3" method="get">
            <div class="col-12 col-md-3">
                <select class="form-select" name="course_id">
                    <option value="0">Tutti i corsi</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= (int)$course['id'] ?>" <?= $courseFilter === (int)$course['id'] ? 'selected' : '' ?>><?= e($course['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select class="form-select" name="status">
                    <option value="">Stato</option>
                    <?php foreach (['scheduled','completed','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2"><input type="date" name="date_from" class="form-control" value="<?= e($dateFrom) ?>"></div>
            <div class="col-6 col-md-2"><input type="date" name="date_to" class="form-control" value="<?= e($dateTo) ?>"></div>
            <div class="col-6 col-md-2"><button class="btn btn-primary w-100">Filtra</button></div>
        </form>

        <div class="card"><div class="card-body table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Lezione</th><th>Corso</th><th>Docente</th><th>Data/Ora</th><th>Meet</th><th>Stato</th><th class="text-end">Azioni</th></tr></thead>
                <tbody>
                <?php foreach ($lessons as $lesson): ?>
                    <tr>
                        <td><?= e($lesson['title']) ?></td>
                        <td><?= e($lesson['course_title']) ?></td>
                        <td><?= e($lesson['teacher_name'] ?? '-') ?></td>
                        <td><?= e($lesson['lesson_date']) ?> <span class="text-muted"><?= e(substr($lesson['start_time'],0,5)) ?>-<?= e(substr($lesson['end_time'],0,5)) ?></span></td>
                        <td><?php if (!empty($lesson['google_meet_link'])): ?><a href="<?= e($lesson['google_meet_link']) ?>" target="_blank" rel="noopener">Apri link</a><?php else: ?>-<?php endif; ?></td>
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
