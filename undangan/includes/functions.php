<?php

require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function isValidHexColor(string $color): bool
{
    return (bool) preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
}

function isValidMp3Url(string $url): bool
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    $path = parse_url($url, PHP_URL_PATH);
    return $path && preg_match('/\.mp3$/i', $path);
}

function validateLogoUpload(array $file): ?string
{
    return validateImageUpload($file, MAX_LOGO_SIZE);
}

function saveLogoUpload(array $file, string $prefix): ?string
{
    return saveImageUpload($file, $prefix, UPLOAD_DIR, UPLOAD_URL, MAX_LOGO_SIZE);
}

function validateImageUpload(array $file, int $maxSize = MAX_LOGO_SIZE): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Gagal mengunggah file.';
    }
    if ($file['size'] > $maxSize) {
        return 'Ukuran file maksimal ' . round($maxSize / 1048576, 1) . ' MB.';
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_LOGO_TYPES, true)) {
        return 'Hanya file .png, .jpg, atau .jpeg yang diperbolehkan.';
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_LOGO_EXT, true)) {
        return 'Ekstensi file tidak valid.';
    }

    return null;
}

function saveImageUpload(array $file, string $prefix, string $dir, string $urlBase, int $maxSize = MAX_LOGO_SIZE): ?string
{
    $error = validateImageUpload($file, $maxSize);
    if ($error) {
        return null;
    }

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dest     = $dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    return $urlBase . $filename;
}

function saveOrnamentUpload(array $file, string $prefix): ?string
{
    return saveImageUpload($file, $prefix, ORNAMENT_UPLOAD_DIR, ORNAMENT_UPLOAD_URL, MAX_ORNAMENT_SIZE);
}

function validateFontUpload(array $file): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Gagal mengunggah file font.';
    }
    if ($file['size'] > MAX_FONT_SIZE) {
        return 'Ukuran font maksimal 3 MB.';
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_FONT_EXT, true)) {
        return 'Font harus berformat .woff2, .woff, atau .ttf';
    }
    return null;
}

function saveFontUpload(array $file, string $prefix): ?string
{
    $err = validateFontUpload($file);
    if ($err) {
        return null;
    }
    if (!is_dir(FONT_UPLOAD_DIR)) {
        mkdir(FONT_UPLOAD_DIR, 0755, true);
    }
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dest     = FONT_UPLOAD_DIR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return FONT_UPLOAD_URL . $filename;
}

function getEventById(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getEventBySlug(string $slug): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM events WHERE slug = ? AND status = ?');
    $stmt->execute([$slug, 'aktif']);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getDashboardStats(): array
{
    $db = getDB();

    $activeEvents = (int) $db->query("SELECT COUNT(*) FROM events WHERE status = 'aktif'")->fetchColumn();
    $totalRsvp    = (int) $db->query('SELECT COUNT(*) FROM guestbook_rsvp')->fetchColumn();
    $totalHadir     = (int) $db->query("SELECT COALESCE(SUM(pax_count), 0) FROM guestbook_rsvp WHERE status = 'hadir'")->fetchColumn();

    $capacityRow = $db->query("SELECT COALESCE(SUM(seat_capacity), 0) AS cap FROM events WHERE status = 'aktif'")->fetch();
    $capacity    = (int) ($capacityRow['cap'] ?? 0);

    return [
        'active_events' => $activeEvents,
        'total_rsvp'    => $totalRsvp,
        'total_hadir'   => $totalHadir,
        'seat_capacity' => $capacity,
    ];
}

function getEventRsvpStats(int $eventId): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) AS hadir_count,
            SUM(CASE WHEN status = 'absen' THEN 1 ELSE 0 END) AS absen_count,
            COALESCE(SUM(CASE WHEN status = 'hadir' THEN pax_count ELSE 0 END), 0) AS total_pax
        FROM guestbook_rsvp WHERE event_id = ?
    ");
    $stmt->execute([$eventId]);
    return $stmt->fetch() ?: ['total' => 0, 'hadir_count' => 0, 'absen_count' => 0, 'total_pax' => 0];
}

function flash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function parseInviteeName(): string
{
    foreach (['kepada', 'to', 'nama', 'tamu'] as $param) {
        if (!empty($_GET[$param])) {
            $name = trim(urldecode((string) $_GET[$param]));
            $name = strip_tags($name);
            return mb_substr($name, 0, 150);
        }
    }
    return '';
}

function parseEventSchedule(?string $json): array
{
    if (!$json) {
        return [];
    }
    $items = json_decode($json, true);
    if (!is_array($items)) {
        return [];
    }
    $result = [];
    foreach ($items as $item) {
        if (empty($item['title'])) {
            continue;
        }
        $result[] = [
            'time'  => trim($item['time'] ?? ''),
            'title' => trim($item['title'] ?? ''),
            'desc'  => trim($item['desc'] ?? ''),
        ];
    }
    return $result;
}

function buildEventScheduleFromPost(array $post): string
{
    $times  = $post['schedule_time'] ?? [];
    $titles = $post['schedule_title'] ?? [];
    $descs  = $post['schedule_desc'] ?? [];
    $items  = [];

    foreach ($titles as $i => $title) {
        $title = trim($title);
        if ($title === '') {
            continue;
        }
        $items[] = [
            'time'  => trim($times[$i] ?? ''),
            'title' => $title,
            'desc'  => trim($descs[$i] ?? ''),
        ];
    }

    return $items ? json_encode($items, JSON_UNESCAPED_UNICODE) : '';
}

function getInvitationBaseUrl(string $slug): string
{
    return app_url($slug);
}

function getPersonalizedInvitationUrl(string $slug, string $guestName): string
{
    return getInvitationBaseUrl($slug) . '?kepada=' . rawurlencode($guestName);
}
