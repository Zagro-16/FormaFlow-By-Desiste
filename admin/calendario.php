<?php
$pageTitle = 'Calendario Lezioni';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$upcoming = $pdo->query("SELECT l.id, l.title, l.lesson_date, l.start_time, c.title AS course_title
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    WHERE l.lesson_date >= CURDATE()
    ORDER BY l.lesson_date ASC, l.start_time ASC
    LIMIT 10")->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout">
    <?php include __DIR__ . '/../includes/sidebar-admin.php'; ?>
    <main class="content">
        <?php include __DIR__ . '/../includes/alerts.php'; ?>
        <?php include __DIR__ . '/../includes/page-header.php'; ?>

        <div class="row g-3">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Calendario didattico</span>
                        <small class="text-muted">Drag & drop pronto per estensioni future</small>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card h-100">
                    <div class="card-header">Prossime lezioni</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($upcoming as $lesson): ?>
                                <li class="list-group-item px-0">
                                    <div class="fw-semibold"><?= e($lesson['title']) ?></div>
                                    <div class="small text-muted"><?= e($lesson['course_title']) ?></div>
                                    <div class="small"><?= e($lesson['lesson_date']) ?> - <?= e(substr($lesson['start_time'],0,5)) ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        locale: 'it',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: '<?= APP_URL ?>/ajax/get-calendar-events.php',
        eventClick(info) {
            const title = info.event.title;
            const start = info.event.start ? info.event.start.toLocaleString('it-IT') : '-';
            const course = info.event.extendedProps.course_title || '-';
            alert(`Lezione: ${title}\nCorso: ${course}\nInizio: ${start}`);
        }
    });

    calendar.render();
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
