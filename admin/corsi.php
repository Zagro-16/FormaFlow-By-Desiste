<?php
$pageTitle = 'Gestione Corsi';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');

$sql = "SELECT c.*, u.full_name AS teacher_name
        FROM courses c
        LEFT JOIN users u ON u.id = c.teacher_id
        WHERE 1=1";
$params = [];

if ($statusFilter !== '') {
    $sql .= ' AND c.status = ?';
    $params[] = $statusFilter;
}
if ($search !== '') {
    $sql .= ' AND (c.title LIKE ? OR c.description LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= ' ORDER BY c.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

$pageAction = ['url' => APP_URL . '/admin/corso-new.php', 'label' => 'Nuovo Corso'];
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <form class="row g-2 mb-3" method="get">
            <div class="col-md-4">
                <input type="text" class="form-control" name="q" placeholder="Cerca corso" value="<?= e($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <?php foreach (['draft','active','completed','cancelled'] as $status): ?>
                        <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filtra</button>
            </div>
        </form>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Titolo</th>
                            <th>Docente</th>
                            <th>Periodo</th>
                            <th>Ore</th>
                            <th>Stato</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?= e($course['title']) ?></td>
                                <td><?= e($course['teacher_name'] ?? '-') ?></td>
                                <td><?= e($course['start_date'] ?? '-') ?> → <?= e($course['end_date'] ?? '-') ?></td>
                                <td><?= e((string)$course['total_hours']) ?></td>
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
