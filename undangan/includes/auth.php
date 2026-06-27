<?php

require_once __DIR__ . '/functions.php';

function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool
{
    startSession();
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . app_url('admin/login.php'));
        exit;
    }
}

function currentUser(): ?array
{
    startSession();
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = getDB()->prepare('SELECT id, username, role FROM inv_users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function login(string $username, string $password): bool
{
    $stmt = getDB()->prepare('SELECT * FROM inv_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        startSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        return true;
    }
    return false;
}

function logout(): void
{
    startSession();
    session_destroy();
}

function isSuperAdmin(): bool
{
    $user = currentUser();
    return $user && $user['role'] === 'super_admin';
}
