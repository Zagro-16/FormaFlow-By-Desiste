$desktop = [Environment]::GetFolderPath("Desktop")
$projectRoot = Join-Path $desktop "FormaFlow-By-Desiste"

$directories = @(
    "config", "includes", "assets/css", "assets/js", "assets/img",
    "admin", "docente", "corsista", "actions", "ajax", "cron",
    "uploads/materiali", "uploads/attestati", "uploads/loghi", "uploads/temp",
    "database", "scripts"
)

$files = @(
    ".htaccess", "index.php", "dashboard.php", "logout.php", "README.md",
    "config/config.php", "config/db.php", "config/auth.php", "config/functions.php",
    "includes/header.php", "includes/footer.php", "includes/topbar.php", "includes/alerts.php", "includes/page-header.php",
    "includes/sidebar-admin.php", "includes/sidebar-docente.php", "includes/sidebar-corsista.php",
    "assets/css/style.css", "assets/css/dashboard.css", "assets/css/forms.css", "assets/css/tables.css", "assets/css/responsive.css",
    "assets/js/app.js", "assets/js/calendar.js", "assets/js/qrscan.js", "assets/js/quiz.js", "assets/js/charts.js", "assets/js/validations.js", "assets/js/reminders.js",
    "database/schema.sql", "database/seed.sql",
    "actions/login-action.php", "actions/corso-save.php", "actions/lezione-save.php", "actions/docente-save.php", "actions/corsista-save.php",
    "actions/iscrizione-save.php", "actions/presenza-save.php", "actions/quiz-save.php", "actions/quiz-submit.php", "actions/attestato-genera.php",
    "actions/materiale-upload.php", "actions/settings-save.php", "actions/invia-reminder-manuale.php",
    "ajax/get-calendar-events.php", "ajax/get-dashboard-stats.php", "ajax/qr-checkin.php", "ajax/search-users.php", "ajax/get-course-lessons.php", "ajax/get-teacher-hours.php", "ajax/get-reports-data.php",
    "cron/reminder-cron.php"
)

foreach ($dir in $directories) {
    New-Item -Path (Join-Path $projectRoot $dir) -ItemType Directory -Force | Out-Null
}

foreach ($file in $files) {
    $path = Join-Path $projectRoot $file
    $parent = Split-Path $path -Parent
    if (-not (Test-Path $parent)) {
        New-Item -Path $parent -ItemType Directory -Force | Out-Null
    }
    if (-not (Test-Path $path)) {
        New-Item -Path $path -ItemType File -Force | Out-Null
    }
}

Write-Host "Progetto creato in: $projectRoot" -ForegroundColor Green
