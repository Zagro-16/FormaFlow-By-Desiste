<?php
$pageTitle='Corsista - Calendario';
require_once __DIR__ . '/../includes/header.php';
require_role('corsista');
$uid=(int)$_SESSION['user']['id'];
$upcoming=$pdo->prepare("SELECT l.title,l.lesson_date,l.start_time,c.title course_title,l.google_meet_link FROM lessons l JOIN courses c ON c.id=l.course_id JOIN enrollments e ON e.course_id=c.id WHERE e.student_id=? AND l.lesson_date>=CURDATE() ORDER BY l.lesson_date,l.start_time LIMIT 15");$upcoming->execute([$uid]);$upcoming=$upcoming->fetchAll();
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-corsista.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body"><div id="calendar"></div></div></div>
<div class="card border-0 shadow-sm mt-3"><div class="card-header bg-white">Prossime lezioni</div><div class="card-body table-responsive"><table class="table"><thead><tr><th>Corso</th><th>Lezione</th><th>Data</th><th>Meet</th></tr></thead><tbody><?php foreach($upcoming as $u): ?><tr><td><?= e($u['course_title']) ?></td><td><?= e($u['title']) ?></td><td><?= e($u['lesson_date']) ?> <?= e(substr($u['start_time'],0,5)) ?></td><td><?php if($u['google_meet_link']): ?><a href="<?= e($u['google_meet_link']) ?>" target="_blank">Apri</a><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css"><script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script><script>document.addEventListener('DOMContentLoaded',()=>{const el=document.getElementById('calendar');if(!el)return;new FullCalendar.Calendar(el,{initialView:'dayGridMonth',height:'auto',locale:'it',events:'<?= APP_URL ?>/ajax/get-calendar-events.php'}).render();});</script>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
