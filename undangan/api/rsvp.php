<?php

require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$eventId     = (int) ($input['event_id'] ?? 0);
$guestName   = trim($input['guest_name'] ?? '');
$status      = $input['status'] ?? 'hadir';
$paxCount    = max(1, (int) ($input['pax_count'] ?? 1));
$greetingNote = trim($input['greeting_note'] ?? '');

if ($eventId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Event tidak valid.'], 400);
}

$event = getEventById($eventId);
if (!$event || $event['status'] !== 'aktif') {
    jsonResponse(['success' => false, 'message' => 'Undangan tidak ditemukan atau tidak aktif.'], 404);
}

if ($guestName === '' || mb_strlen($guestName) > 150) {
    jsonResponse(['success' => false, 'message' => 'Nama tamu wajib diisi (maks. 150 karakter).'], 400);
}

if (!in_array($status, ['hadir', 'absen'], true)) {
    jsonResponse(['success' => false, 'message' => 'Status kehadiran tidak valid.'], 400);
}

if ($status === 'absen') {
    $paxCount = 0;
}

$stmt = getDB()->prepare('
    INSERT INTO guestbook_rsvp (event_id, guest_name, status, pax_count, greeting_note)
    VALUES (?, ?, ?, ?, ?)
');
$stmt->execute([$eventId, $guestName, $status, $paxCount, $greetingNote ?: null]);

jsonResponse([
    'success' => true,
    'message' => 'Terima kasih! Konfirmasi kehadiran Anda telah tercatat.',
    'id'      => (int) getDB()->lastInsertId(),
]);
