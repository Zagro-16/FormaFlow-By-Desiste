<?php
$pageTitle = 'Corsisti';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$search = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "SELECT u.id, u.full_name, u.email, u.is_active, u.created_at,
        sp.phone, sp.birth_date,
        (SELECT COUNT(*) FROM enrollments e WHERE e.student_id = u.id) AS courses_count,
        (SELECT COUNT(*) FROM certificates c WHERE c.student_id = u.id) AS certificates_count
        FROM users u
        LEFT JOIN student_profiles sp ON sp.user_id = u.id
        WHERE u.role='corsista'";
$params = [];

if ($search !== '') {
    $sql .= ' AND (u.full_name LIKE ? OR u.email LIKE ? OR sp.phone LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($status !== '') {
    $sql .= ' AND u.is_active = ?';
    $params[] = $status === 'active' ? 1 : 0;
}
$sql .= ' ORDER BY u.full_name';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$summary = $pdo->query("SELECT
    COUNT(*) AS total,
    COUNT(CASE WHEN is_active=1 THEN 1 END) AS active_total,
    COUNT(CASE WHEN is_active=0 THEN 1 END) AS inactive_total
    FROM users WHERE role='corsista'")->fetch();

$pageAction = ['url' => APP_URL . '/admin/corsista-new.php', 'label' => 'Nuovo Corsista'];
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="row g-3 mb-3">
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Totale corsisti</div><div class="stat-number"><?= (int)$summary['total'] ?></div></div></div></div>
            <div class="col-6 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Attivi</div><div class="stat-number"><?= (int)$summary['active_total'] ?></div></div></div></div>
            <div class="col-12 col-md-4"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Non attivi</div><div class="stat-number"><?= (int)$summary['inactive_total'] ?></div></div></div></div>
        </div>

        <form class="row g-2 mb-3" method="get">
            <div class="col-12 col-md-5"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Cerca per nome, email, telefono"></div>
            <div class="col-8 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tutti</option>
                    <option value="active" <?= $status==='active'?'selected':'' ?>>Attivi</option>
                    <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Non attivi</option>
                </select>
            </div>
            <div class="col-4 col-md-2"><button class="btn btn-primary w-100">Filtra</button></div>
        </form>

        <div class="card"><div class="card-body table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Corsista</th><th>Contatti</th><th>Corsi</th><th>Attestati</th><th>Stato</th><th class="text-end">Azioni</th></tr></thead>
                <tbody>
                <?php foreach ($students as $st): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($st['full_name']) ?></div>
                            <div class="small text-muted">Creato: <?= e(substr((string)$st['created_at'],0,10)) ?></div>
                        </td>
                        <td>
                            <div><?= e($st['email']) ?></div>
                            <div class="small text-muted">Tel: <?= e($st['phone'] ?? '-') ?></div>
                        </td>
                        <td><?= (int)$st['courses_count'] ?></td>
                        <td><?= (int)$st['certificates_count'] ?></td>
                        <td>
                            <span class="badge rounded-pill <?= (int)$st['is_active']===1 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' ?>">
                                <?= (int)$st['is_active']===1 ? 'Attivo' : 'Non attivo' ?>
                            </span>
                        </td>
                        <td class="text-end"><a href="<?= APP_URL ?>/admin/corsista-edit.php?id=<?= (int)$st['id'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
