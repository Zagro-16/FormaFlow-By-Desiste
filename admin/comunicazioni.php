<?php
$pageTitle = 'Comunicazioni';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();
$templates = [
    'reminder_lesson' => 'Reminder lezione',
    'new_material' => 'Nuovo materiale disponibile',
    'quiz_open' => 'Quiz pubblicato',
    'general' => 'Comunicazione generale',
];

$recentEmails = $pdo->query("SELECT email_to, subject, status, sent_at FROM email_logs ORDER BY sent_at DESC LIMIT 25")->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-admin.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white"><h6 class="mb-0">Invio comunicazione manuale</h6></div>
    <div class="card-body">
        <form method="post" action="<?= APP_URL ?>/actions/invia-reminder-manuale.php" class="row g-3">
            <div class="col-12 col-md-4"><label class="form-label">Template</label><select name="template" class="form-select"><?php foreach($templates as $k=>$v): ?><option value="<?= e($k) ?>"><?= e($v) ?></option><?php endforeach; ?></select></div>
            <div class="col-12 col-md-4"><label class="form-label">Corso (opzionale)</label><select name="course_id" class="form-select"><option value="">Tutti i corsi</option><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['title']) ?></option><?php endforeach; ?></select></div>
            <div class="col-12 col-md-4"><label class="form-label">Oggetto email</label><input name="subject" class="form-control" placeholder="Es. Promemoria lezione domani"></div>
            <div class="col-12"><label class="form-label">Messaggio</label><textarea name="message" class="form-control" rows="5" placeholder="Scrivi qui la comunicazione..."></textarea></div>
            <div class="col-12"><button class="btn btn-primary">Invia comunicazione</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm"><div class="card-header bg-white"><h6 class="mb-0">Storico invii email</h6></div><div class="card-body table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Destinatario</th><th>Oggetto</th><th>Stato</th><th>Data invio</th></tr></thead>
        <tbody>
            <?php foreach($recentEmails as $r): ?>
                <tr>
                    <td><?= e($r['email_to']) ?></td>
                    <td><?= e($r['subject']) ?></td>
                    <td><span class="badge rounded-pill <?= $r['status']==='sent' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' ?>"><?= e($r['status']) ?></span></td>
                    <td><?= e((string)$r['sent_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div></div>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
