<?php
$pageTitle = 'Dashboard Admin';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$stats = $pdo->query("SELECT
    (SELECT COUNT(*) FROM courses) AS total_courses,
    (SELECT COUNT(*) FROM courses WHERE status='active') AS active_courses,
    (SELECT COUNT(*) FROM lessons WHERE lesson_date >= CURDATE()) AS upcoming_lessons,
    (SELECT COUNT(*) FROM users WHERE role='docente') AS total_teachers,
    (SELECT COUNT(*) FROM users WHERE role='corsista') AS total_students,
    (SELECT COUNT(*) FROM certificates) AS total_certificates,
    (SELECT COUNT(*) FROM quiz_attempts) AS completed_quizzes
")->fetch();

$nextLessons = $pdo->query("SELECT l.id, l.title, l.lesson_date, l.start_time, l.end_time, l.google_meet_link,
        c.title AS course_title, u.full_name AS teacher_name
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    LEFT JOIN users u ON u.id = c.teacher_id
    WHERE l.lesson_date >= CURDATE()
    ORDER BY l.lesson_date ASC, l.start_time ASC
    LIMIT 8")->fetchAll();

$latestEnrollments = $pdo->query("SELECT e.created_at, c.title AS course_title, u.full_name AS student_name, e.status
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    JOIN users u ON u.id = e.student_id
    ORDER BY e.created_at DESC
    LIMIT 8")->fetchAll();

$trendRows = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_label, COUNT(*) AS total
    FROM enrollments
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month_label ASC")->fetchAll();

$trendLabels = array_column($trendRows, 'month_label');
$trendValues = array_map('intval', array_column($trendRows, 'total'));
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="row g-3 mb-3">
            <?php foreach ([
                ['Totale corsi', (int)$stats['total_courses']],
                ['Corsi attivi', (int)$stats['active_courses']],
                ['Lezioni imminenti', (int)$stats['upcoming_lessons']],
                ['Docenti', (int)$stats['total_teachers']],
                ['Corsisti', (int)$stats['total_students']],
                ['Attestati emessi', (int)$stats['total_certificates']],
                ['Quiz completati', (int)$stats['completed_quizzes']],
            ] as $card): ?>
                <div class="col-6 col-md-4 col-xl-3">
                    <div class="card card-stat h-100">
                        <div class="card-body">
                            <div class="text-muted small"><?= e($card[0]) ?></div>
                            <div class="stat-number"><?= e((string)$card[1]) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header">Lezioni in programma</div>
                    <div class="card-body table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Corso</th><th>Lezione</th><th>Data</th><th>Docente</th><th>Meet</th></tr></thead>
                            <tbody>
                                <?php foreach ($nextLessons as $lesson): ?>
                                    <tr>
                                        <td><?= e($lesson['course_title']) ?></td>
                                        <td><?= e($lesson['title']) ?></td>
                                        <td><?= e($lesson['lesson_date']) ?> <span class="text-muted">(<?= e(substr($lesson['start_time'], 0, 5)) ?>-<?= e(substr($lesson['end_time'], 0, 5)) ?>)</span></td>
                                        <td><?= e($lesson['teacher_name'] ?? '-') ?></td>
                                        <td>
                                            <?php if (!empty($lesson['google_meet_link'])): ?>
                                                <a href="<?= e($lesson['google_meet_link']) ?>" target="_blank" rel="noopener">Apri</a>
                                            <?php else: ?>-
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">Andamento iscrizioni (ultimi 6 mesi)</div>
                    <div class="card-body">
                        <canvas id="enrollmentTrendChart" height="190"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Attività recenti (nuove iscrizioni)</div>
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Data</th><th>Corsista</th><th>Corso</th><th>Stato</th></tr></thead>
                    <tbody>
                        <?php foreach ($latestEnrollments as $enroll): ?>
                            <tr>
                                <td><?= e($enroll['created_at']) ?></td>
                                <td><?= e($enroll['student_name']) ?></td>
                                <td><?= e($enroll['course_title']) ?></td>
                                <td><span class="badge bg-secondary"><?= e($enroll['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    const ctx = document.getElementById('enrollmentTrendChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($trendLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Iscrizioni',
                data: <?= json_encode($trendValues, JSON_UNESCAPED_UNICODE) ?>,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.15)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
})();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
