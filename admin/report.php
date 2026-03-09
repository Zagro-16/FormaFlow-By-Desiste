<?php
$pageTitle = 'Report';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$global = $pdo->query("SELECT
    (SELECT COUNT(*) FROM courses) courses_total,
    (SELECT COUNT(*) FROM lessons) lessons_total,
    (SELECT COUNT(*) FROM enrollments) enrollments_total,
    (SELECT COUNT(*) FROM attendance WHERE status='present') attendance_present,
    (SELECT COUNT(*) FROM attendance) attendance_total,
    (SELECT COUNT(*) FROM certificates) certificates_total,
    (SELECT COUNT(*) FROM quiz_attempts) quiz_attempts_total
")->fetch();

$attendanceRate = ((int)$global['attendance_total'] > 0)
    ? round(((int)$global['attendance_present'] / (int)$global['attendance_total']) * 100, 1)
    : 0;

$coursesReport = $pdo->query("SELECT c.id, c.title, c.status,
    COUNT(DISTINCT e.id) AS enrollments,
    COUNT(DISTINCT l.id) AS lessons,
    COUNT(DISTINCT cert.id) AS certificates
    FROM courses c
    LEFT JOIN enrollments e ON e.course_id = c.id
    LEFT JOIN lessons l ON l.course_id = c.id
    LEFT JOIN certificates cert ON cert.course_id = c.id
    GROUP BY c.id, c.title, c.status
    ORDER BY c.title")->fetchAll();

$monthlyEnrollments = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_label, COUNT(*) AS total
    FROM enrollments
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month_label")->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-admin.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Corsi</div><div class="stat-number"><?= (int)$global['courses_total'] ?></div></div></div></div>
    <div class="col-6 col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Lezioni</div><div class="stat-number"><?= (int)$global['lessons_total'] ?></div></div></div></div>
    <div class="col-6 col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Iscrizioni</div><div class="stat-number"><?= (int)$global['enrollments_total'] ?></div></div></div></div>
    <div class="col-6 col-md-3"><div class="card card-stat"><div class="card-body"><div class="text-muted small">Frequenza media</div><div class="stat-number"><?= $attendanceRate ?>%</div></div></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100"><div class="card-header bg-white"><h6 class="mb-0">Riepilogo corsi</h6></div><div class="card-body table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Corso</th><th>Stato</th><th>Iscrizioni</th><th>Lezioni</th><th>Attestati</th></tr></thead>
                <tbody>
                    <?php foreach($coursesReport as $r): ?>
                        <tr>
                            <td><?= e($r['title']) ?></td>
                            <td><?= e($r['status']) ?></td>
                            <td><?= (int)$r['enrollments'] ?></td>
                            <td><?= (int)$r['lessons'] ?></td>
                            <td><?= (int)$r['certificates'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100"><div class="card-header bg-white"><h6 class="mb-0">Trend iscrizioni (12 mesi)</h6></div><div class="card-body">
            <canvas id="enrollmentReportChart" height="220"></canvas>
        </div></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    const chart = document.getElementById('enrollmentReportChart');
    if (!chart) return;
    const labels = <?= json_encode(array_column($monthlyEnrollments, 'month_label')) ?>;
    const values = <?= json_encode(array_map('intval', array_column($monthlyEnrollments, 'total'))) ?>;
    new Chart(chart, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Iscrizioni', data: values, backgroundColor: 'rgba(37,99,235,0.55)', borderColor: '#2563eb', borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
})();
</script>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
