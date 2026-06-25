<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$event = getEventById($id);
if (!$event) {
    flash('error', 'Undangan tidak ditemukan.');
    header('Location: ' . app_url('admin/events/index.php'));
    exit;
}

$stmt = getDB()->prepare('DELETE FROM events WHERE id = ?');
$stmt->execute([$id]);

flash('success', 'Undangan dan seluruh data RSVP terkait telah dihapus.');
header('Location: ' . app_url('admin/events/index.php'));
exit;
