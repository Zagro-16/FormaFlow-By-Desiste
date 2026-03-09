<?php
$pageTitle = 'Docenti';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$search = trim($_GET['q'] ?? '');

$sql = "SELECT u.id, u.full_name, u.email, u.created_at,
        COALESCE(tp.total_assigned_hours, 0) AS total_assigned_hours,
        COALESCE(tp.total_completed_hours, 0) AS total_completed_hours,
        (SELECT COUNT(*) FROM courses c WHERE c.teacher_id = u.id) AS courses_count,
        (SELECT COUNT(*) FROM lessons l JOIN courses c2 ON c2.id = l.course_id WHERE c2.teacher_id = u.id AND l.lesson_date >= CURDATE()) AS upcoming_lessons
        FROM users u
        LEFT JOIN teacher_profiles tp ON tp.user_id = u.id
        WHERE u.role = 'docente'";
$params = [];

if ($search !== '') {
    $sql .= ' AND (u.full_name LIKE ? OR u.email LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= ' ORDER BY u.full_name ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$teachers = $stmt->fetchAll();

$summary = $pdo->query("SELECT
    COUNT(*) AS total_teachers,
    COALESCE(SUM(tp.total_assigned_hours), 0) AS assigned_hours,
    COALESCE(SUM(tp.total_completed_hours), 0) AS completed_hours
    FROM users u
    LEFT JOIN teacher_profiles tp ON tp.user_id = u.id
    WHERE u.role = 'docente'")->fetch();

$pageAction = ['url' => APP_URL . '/admin/docente-new.php', 'label' => 'Nuovo Docente'];
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="row g-3 mb-3">
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Docenti totali</div><div class="stat-number"><?= (int)$summary['total_teachers'] ?></div></div></div></div>
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Ore assegnate</div><div class="stat-number"><?= number_format((float)$summary['assigned_hours'],1) ?></div></div></div></div>
            <div class="col-12 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Ore svolte</div><div class="stat-number"><?= number_format((float)$summary['completed_hours'],1) ?></div></div></div></div>
        </div>

        <form class="row g-2 mb-3" method="get">
            <div class="col-12 col-md-5">
                <input class="form-control" name="q" placeholder="Cerca per nome o email" value="<?= e($search) ?>">
            </div>
            <div class="col-6 col-md-2"><button class="btn btn-primary w-100">Cerca</button></div>
        </form>

        <div class="card"><div class="card-body table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Docente</th><th>Email</th><th>Corsi</th><th>Lezioni imminenti</th><th>Ore assegnate</th><th>Ore svolte</th><th class="text-end">Azioni</th></tr></thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($teacher['full_name']) ?></div>
                                <div class="small text-muted">Creato il <?= e(substr((string)$teacher['created_at'], 0, 10)) ?></div>
                            </td>
                            <td><?= e($teacher['email']) ?></td>
                            <td><?= (int)$teacher['courses_count'] ?></td>
                            <td><?= (int)$teacher['upcoming_lessons'] ?></td>
                            <td><?= number_format((float)$teacher['total_assigned_hours'],1) ?></td>
                            <td><?= number_format((float)$teacher['total_completed_hours'],1) ?></td>
                            <td class="text-end"><a href="<?= APP_URL ?>/admin/docente-edit.php?id=<?= (int)$teacher['id'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
