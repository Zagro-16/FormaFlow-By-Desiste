<?php
$pageTitle = 'Corsi';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');

$sql = "SELECT c.*, u.full_name AS teacher_name,
        (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.status IN ('active','completed')) AS enrolled_count,
        (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) AS lessons_count
        FROM courses c
        LEFT JOIN users u ON u.id = c.teacher_id
        WHERE 1=1";
$params = [];

if ($statusFilter !== '') {
    $sql .= ' AND c.status = ?';
    $params[] = $statusFilter;
}
if ($search !== '') {
    $sql .= ' AND (c.title LIKE ? OR c.description LIKE ? OR u.full_name LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= ' ORDER BY c.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

$summary = $pdo->query("SELECT
    COUNT(*) AS total_courses,
    COUNT(CASE WHEN status='active' THEN 1 END) AS active_courses,
    COALESCE(SUM(total_hours),0) AS hours_total
    FROM courses")->fetch();

$pageAction = ['url' => APP_URL . '/admin/corso-new.php', 'label' => 'Nuovo Corso'];
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="row g-3 mb-3">
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Totale corsi</div><div class="stat-number"><?= (int)$summary['total_courses'] ?></div></div></div></div>
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Corsi attivi</div><div class="stat-number"><?= (int)$summary['active_courses'] ?></div></div></div></div>
            <div class="col-12 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Ore formazione</div><div class="stat-number"><?= number_format((float)$summary['hours_total'],1) ?></div></div></div></div>
        </div>

        <form class="row g-2 mb-3" method="get">
            <div class="col-12 col-md-5">
                <input type="text" class="form-control" name="q" placeholder="Cerca per titolo, descrizione o docente" value="<?= e($search) ?>">
            </div>
            <div class="col-8 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <?php foreach (['draft','active','completed','cancelled'] as $status): ?>
                        <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-4 col-md-2">
                <button class="btn btn-primary w-100">Filtra</button>
            </div>
        </form>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Corso</th>
                            <th>Docente</th>
                            <th>Periodo</th>
                            <th>Ore</th>
                            <th>Lezioni</th>
                            <th>Iscritti</th>
                            <th>Stato</th>
                            <th class="text-end">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= e($course['title']) ?></div>
                                    <div class="text-muted small"><?= e(mb_strimwidth((string)($course['description'] ?? ''), 0, 80, '...')) ?></div>
                                </td>
                                <td><?= e($course['teacher_name'] ?? '-') ?></td>
                                <td><?= e($course['start_date'] ?? '-') ?> → <?= e($course['end_date'] ?? '-') ?></td>
                                <td><?= number_format((float)$course['total_hours'], 1) ?></td>
                                <td><?= (int)$course['lessons_count'] ?></td>
                                <td><?= (int)$course['enrolled_count'] ?></td>
                                <td><span class="badge bg-secondary"><?= e($course['status']) ?></span></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= APP_URL ?>/admin/corso-edit.php?id=<?= (int)$course['id'] ?>">Modifica</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
