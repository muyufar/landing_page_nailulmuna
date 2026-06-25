<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$eventId = (int) ($_GET['event_id'] ?? 0);
$event = getEventById($eventId);
if (!$event) {
    flash('error', 'Acara tidak ditemukan.');
    header('Location: ' . app_url('admin/rsvp/index.php'));
    exit;
}

$stmt = getDB()->prepare('SELECT * FROM guestbook_rsvp WHERE event_id = ? ORDER BY submitted_at DESC');
$stmt->execute([$eventId]);
$rows = $stmt->fetchAll();

$filename = 'rsvp-' . $event['slug'] . '-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
fputcsv($out, ['No', 'Nama Tamu', 'Status', 'Jumlah Pax', 'Ucapan/Doa', 'Waktu Kirim']);

foreach ($rows as $i => $r) {
    fputcsv($out, [
        $i + 1,
        $r['guest_name'],
        $r['status'] === 'hadir' ? 'Hadir' : 'Tidak Hadir',
        $r['pax_count'],
        $r['greeting_note'],
        $r['submitted_at'],
    ]);
}
fclose($out);
exit;
