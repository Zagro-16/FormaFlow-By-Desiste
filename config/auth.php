<?php
function require_login(): void { if (empty($_SESSION['user'])) { set_flash('danger','Effettua il login.'); redirect('index.php'); } }
function require_role($roles): void {
    require_login();
    $roles = (array)$roles;
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        set_flash('danger','Accesso negato.');
        redirect('dashboard.php');
    }
}
function role_dashboard(string $role): string {
    return match($role){'admin'=>'admin/dashboard.php','docente'=>'docente/dashboard.php','corsista'=>'corsista/dashboard.php',default=>'dashboard.php'};
}
