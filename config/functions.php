<?php
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): void { header('Location: ' . APP_URL . '/' . ltrim($path, '/')); exit; }
function set_flash(string $type, string $message): void { $_SESSION['flash'] = ['type'=>$type,'message'=>$message]; }
function get_flash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
