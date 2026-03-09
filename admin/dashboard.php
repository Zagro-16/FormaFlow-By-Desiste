<?php
$pageTitle = 'Dashboard Admin';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

if (!function_exists('admin_badge_class')) {
    function admin_badge_class(string $status): string {
        return match ($status) {
            'active', 'completed' => 'bg-success-subtle text-success border border-success-subtle',
            'draft', 'scheduled' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
            default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        };
    }
}

if (!function_exists('format_admin_date')) {
    function format_admin_date(?string $date, bool $withTime = false): string {
        if (empty($date)) return '-';
        $ts = strtotime($date);
        if (!$ts) return e($date);
        return $withTime ? date('d/m/Y H:i', $ts) : date('d/m/Y', $ts);
    }
}

$stats = $pdo->query("SELECT
    (SELECT COUNT(*) FROM courses) AS total_courses,
    (SELECT COUNT(*) FROM courses WHERE status='active') AS active_courses,
    (SELECT COUNT(*) FROM users WHERE role='docente') AS total_teachers,
    (SELECT COUNT(*) FROM users WHERE role='corsista') AS total_students,
    (SELECT COUNT(*) FROM certificates) AS certificates,
    (SELECT COUNT(*) FROM quiz_attempts) AS quiz_attempts,
    (SELECT COUNT(*) FROM lessons WHERE lesson_date >= CURDATE()) AS upcoming_lessons,
    (SELECT COUNT(*) FROM courses WHERE status='draft') AS draft_courses
")->fetch();

$upcomingLessons = $pdo->query("SELECT l.id, l.title, l.lesson_date, l.start_time, l.end_time, l.status, c.title AS course_title
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    ORDER BY l.lesson_date ASC, l.start_time ASC
    LIMIT 8")->fetchAll();

$recentCourses = $pdo->query("SELECT c.id, c.title, c.status, c.start_date, c.end_date, c.total_hours, u.full_name AS teacher_name
    FROM courses c
    LEFT JOIN users u ON u.id = c.teacher_id
    ORDER BY c.created_at DESC
    LIMIT 6")->fetchAll();

$teachersLoad = $pdo->query("SELECT u.full_name, COUNT(c.id) AS total_courses
    FROM users u
    LEFT JOIN courses c ON c.teacher_id = u.id
    WHERE u.role = 'docente'
    GROUP BY u.id, u.full_name
    ORDER BY total_courses DESC, u.full_name ASC
    LIMIT 5")->fetchAll();

$quickStats = [
    ['label' => 'Totale corsi', 'value' => (int)($stats['total_courses'] ?? 0), 'icon' => 'bi-collection', 'note' => 'Tutti i corsi presenti in piattaforma'],
    ['label' => 'Corsi attivi', 'value' => (int)($stats['active_courses'] ?? 0), 'icon' => 'bi-play-circle', 'note' => 'Attualmente disponibili per gli utenti'],
    ['label' => 'Docenti', 'value' => (int)($stats['total_teachers'] ?? 0), 'icon' => 'bi-person-badge', 'note' => 'Profili con ruolo docente'],
    ['label' => 'Corsisti', 'value' => (int)($stats['total_students'] ?? 0), 'icon' => 'bi-people', 'note' => 'Studenti registrati in piattaforma'],
    ['label' => 'Attestati', 'value' => (int)($stats['certificates'] ?? 0), 'icon' => 'bi-patch-check', 'note' => 'Certificati generati'],
    ['label' => 'Quiz completati', 'value' => (int)($stats['quiz_attempts'] ?? 0), 'icon' => 'bi-ui-checks-grid', 'note' => 'Tentativi registrati'],
    ['label' => 'Lezioni in programma', 'value' => (int)($stats['upcoming_lessons'] ?? 0), 'icon' => 'bi-calendar-event', 'note' => 'Lezioni future pianificate'],
    ['label' => 'Corsi in bozza', 'value' => (int)($stats['draft_courses'] ?? 0), 'icon' => 'bi-pencil-square', 'note' => 'Contenuti ancora non pubblicati'],
];
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>

    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="container-fluid px-0">
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle mb-3">Pannello amministratore</span>
                            <h2 class="fw-bold mb-2">Controllo completo della piattaforma formativa</h2>
                            <p class="text-muted mb-0">Monitora corsi, docenti, lezioni, corsisti e attività recenti da una dashboard chiara, moderna e ottimizzata per ogni dispositivo.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="row g-3">
                                <div class="col-6"><a href="<?= APP_URL ?>/admin/corsi.php" class="btn btn-primary w-100 py-3"><i class="bi bi-collection me-2"></i>Corsi</a></div>
                                <div class="col-6"><a href="<?= APP_URL ?>/admin/lezioni.php" class="btn btn-outline-primary w-100 py-3"><i class="bi bi-journal-text me-2"></i>Lezioni</a></div>
                                <div class="col-6"><a href="<?= APP_URL ?>/admin/calendario.php" class="btn btn-outline-dark w-100 py-3"><i class="bi bi-calendar3 me-2"></i>Calendario</a></div>
                                <div class="col-6"><a href="<?= APP_URL ?>/admin/docenti.php" class="btn btn-outline-secondary w-100 py-3"><i class="bi bi-person-badge me-2"></i>Docenti</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <?php foreach ($quickStats as $item): ?>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold mb-2"><?= e($item['label']) ?></div>
                                        <div class="display-6 fw-bold lh-1 mb-2"><?= number_format((int)$item['value'], 0, ',', '.') ?></div>
                                        <div class="small text-muted"><?= e($item['note']) ?></div>
                                    </div>
                                    <div class="rounded-4 p-3 bg-light"><i class="bi <?= e($item['icon']) ?> fs-4"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Prossime lezioni</h5>
                                <p class="text-muted small mb-0">Panoramica rapida delle attività in calendario</p>
                            </div>
                            <a href="<?= APP_URL ?>/admin/lezioni.php" class="btn btn-sm btn-outline-primary">Vai a lezioni</a>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($upcomingLessons)): ?>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead><tr><th>Lezione</th><th>Corso</th><th>Data</th><th>Orario</th><th>Stato</th></tr></thead>
                                        <tbody>
                                        <?php foreach ($upcomingLessons as $lesson): ?>
                                            <tr>
                                                <td class="fw-semibold"><?= e($lesson['title']) ?></td>
                                                <td><?= e($lesson['course_title']) ?></td>
                                                <td><?= format_admin_date($lesson['lesson_date']) ?></td>
                                                <td><?= !empty($lesson['start_time']) ? e(substr($lesson['start_time'], 0, 5)) : '-' ?><?php if (!empty($lesson['end_time'])): ?> - <?= e(substr($lesson['end_time'], 0, 5)) ?><?php endif; ?></td>
                                                <td><span class="badge rounded-pill <?= admin_badge_class((string)$lesson['status']) ?>"><?= e(ucfirst((string)$lesson['status'])) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5"><i class="bi bi-calendar-x fs-1 text-muted"></i><h6 class="mt-3 mb-1">Nessuna lezione trovata</h6><p class="text-muted mb-0">Quando verranno pianificate nuove lezioni, compariranno qui.</p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="mb-1">Docenti più impegnati</h5>
                            <p class="text-muted small mb-0">Numero corsi assegnati</p>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($teachersLoad)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($teachersLoad as $teacher): ?>
                                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 border-bottom">
                                            <div class="fw-medium"><?= e($teacher['full_name']) ?></div>
                                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle"><?= (int)$teacher['total_courses'] ?> corsi</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">Nessun docente disponibile al momento.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="mb-1">Ultimi corsi inseriti</h5>
                            <p class="text-muted small mb-0">Contenuti creati più di recente</p>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($recentCourses)): ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($recentCourses as $course): ?>
                                        <div class="border rounded-4 p-3">
                                            <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                                <div class="fw-semibold"><?= e($course['title']) ?></div>
                                                <span class="badge rounded-pill <?= admin_badge_class((string)$course['status']) ?>"><?= e(ucfirst((string)$course['status'])) ?></span>
                                            </div>
                                            <div class="small text-muted mb-1"><i class="bi bi-person me-1"></i><?= e($course['teacher_name'] ?? 'Docente non assegnato') ?></div>
                                            <div class="small text-muted mb-2"><i class="bi bi-clock me-1"></i><?= (int)$course['total_hours'] ?> ore</div>
                                            <div class="small text-muted"><?= format_admin_date($course['start_date']) ?> → <?= format_admin_date($course['end_date']) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">Nessun corso disponibile.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
