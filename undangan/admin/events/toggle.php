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

$newStatus = $event['status'] === 'aktif' ? 'arsip' : 'aktif';
$stmt = getDB()->prepare('UPDATE events SET status = ? WHERE id = ?');
$stmt->execute([$newStatus, $id]);

flash('success', 'Status undangan diubah menjadi ' . $newStatus . '.');
header('Location: ' . app_url('admin/events/index.php'));
exit;
