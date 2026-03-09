<?php
$pageTitle='Docente - Calendario';
require_once __DIR__ . '/../includes/header.php';
require_role('docente');
?>
<?php include __DIR__.'/../includes/topbar.php'; ?><div class="layout"><?php include __DIR__.'/../includes/sidebar-docente.php'; ?><main class="content"><?php include __DIR__.'/../includes/alerts.php'; ?><?php include __DIR__.'/../includes/page-header.php'; ?>
<div class="card border-0 shadow-sm"><div class="card-body"><div id="calendar"></div></div></div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css"><script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script><script>document.addEventListener('DOMContentLoaded',()=>{const el=document.getElementById('calendar');if(!el)return;new FullCalendar.Calendar(el,{initialView:'timeGridWeek',height:'auto',locale:'it',events:'<?= APP_URL ?>/ajax/get-calendar-events.php'}).render();});</script>
</main></div><?php require_once __DIR__ . '/../includes/footer.php'; ?>
