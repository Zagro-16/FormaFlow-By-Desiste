<?php
$pageTitle = 'Check-in QR';
require_once __DIR__ . '/../includes/header.php';
require_role('admin');

$lessons = $pdo->query("SELECT l.id, CONCAT(c.title, ' - ', l.title, ' (', l.lesson_date, ')') AS label
    FROM lessons l
    JOIN courses c ON c.id = l.course_id
    WHERE l.lesson_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ORDER BY l.lesson_date DESC, l.start_time DESC")->fetchAll();

$students = $pdo->query("SELECT id, full_name FROM users WHERE role='corsista' AND is_active=1 ORDER BY full_name")->fetchAll();
?>
<?php include __DIR__ . '/../includes/topbar.php'; ?>
<div class="layout"><?php include __DIR__ . '/../includes/sidebar-admin.php'; ?><main class="content">
<?php include __DIR__ . '/../includes/alerts.php'; ?>
<?php include __DIR__ . '/../includes/page-header.php'; ?>

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><h6 class="mb-0">Simulatore check-in QR</h6></div>
            <div class="card-body">
                <form id="qrCheckinForm" class="row g-2">
                    <div class="col-12">
                        <label class="form-label">Lezione *</label>
                        <select name="lesson_id" class="form-select" required>
                            <option value="">Seleziona lezione</option>
                            <?php foreach($lessons as $l): ?><option value="<?= (int)$l['id'] ?>"><?= e($l['label']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Corsista *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Seleziona corsista</option>
                            <?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['full_name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><button class="btn btn-primary">Registra check-in</button></div>
                </form>
                <div id="qrFeedback" class="mt-3"></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><h6 class="mb-0">Come usare il modulo QR</h6></div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Seleziona la lezione in corso.</li>
                    <li>Seleziona il corsista che ha scansionato il QR.</li>
                    <li>Conferma check-in per registrare presenza immediata.</li>
                </ol>
                <hr>
                <p class="small text-muted mb-0">Nota: endpoint collegato a <code>ajax/qr-checkin.php</code> con salvataggio su tabella <code>attendance</code>.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('qrCheckinForm')?.addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const feedback = document.getElementById('qrFeedback');
    feedback.innerHTML = '<div class="alert alert-info">Invio in corso...</div>';

    try {
        const response = await fetch('<?= APP_URL ?>/ajax/qr-checkin.php', { method: 'POST', body: formData });
        const json = await response.json();
        if (response.ok && json.ok) {
            feedback.innerHTML = '<div class="alert alert-success">Check-in registrato con successo.</div>';
            this.reset();
        } else {
            feedback.innerHTML = '<div class="alert alert-danger">Errore durante il check-in.</div>';
        }
    } catch (err) {
        feedback.innerHTML = '<div class="alert alert-danger">Errore di connessione.</div>';
    }
});
</script>

</main></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
