<?php

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(string $username, string $password, string $requiredRole): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM bt_users WHERE username = ? AND role = ?');
        $stmt->execute([$username, $requiredRole]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role'],
            ];
            return $_SESSION['user'];
        }
        return null;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function requireRole(string $role): void
    {
        self::startSession();
        $user = self::user();
        if (!$user || $user['role'] !== $role) {
            header('Location: ' . base_url($role === 'admin' ? '/admin/login' : '/ndalem/login'));
            exit;
        }
    }
}

class PengasuhStatusModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getCurrent(): array
    {
        $stmt = $this->db->query('SELECT * FROM pengasuh_status ORDER BY id DESC LIMIT 1');
        $row = $stmt->fetch();
        return $row ?: ['status' => 'available', 'message' => 'Pengasuh sedang luang.'];
    }

    public function update(string $status, string $message, int $userId): void
    {
        $current = $this->getCurrent();
        if ($current) {
            $stmt = $this->db->prepare(
                'UPDATE pengasuh_status SET status = ?, message = ?, updated_by = ?, updated_at = NOW() WHERE id = ?'
            );
            $stmt->execute([$status, $message, $userId, $current['id']]);
        }
    }

    public function isSowanAvailable(): bool
    {
        $current = $this->getCurrent();
        return ($current['status'] ?? 'available') === 'available';
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'available' => 'Sedang Luang / Menerima Tamu',
            'busy' => 'Sedang Menerima Tamu (Antrean Terbatas)',
            'closed' => 'Sedang Istirahat / Menerima Tamu Ditutup',
            default => $status,
        };
    }
}

function base_url(string $path = ''): string
{
    static $base = null;
    if ($base === null) {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        if ($scriptName !== '') {
            $dir = rawurldecode(dirname($scriptName));
            $base = ($dir === '/' || $dir === '\\') ? '' : rtrim($dir, '/');
        } else {
            $config = require __DIR__ . '/../config/app.php';
            $base = rtrim(rawurldecode($config['base_url'] ?? ''), '/');
        }
    }

    $url = $base . $path;

    return str_replace(' ', '%20', $url);
}

/** Path route relatif dari REQUEST_URI (menangani rewrite tanpa /public/). */
function resolve_request_uri(): string
{
    $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
    $scriptDir = rawurldecode(dirname($_SERVER['SCRIPT_NAME'] ?? ''));

    if ($scriptDir !== '/' && $scriptDir !== '\\' && str_starts_with($uri, $scriptDir)) {
        $uri = substr($uri, strlen($scriptDir));
    } elseif (str_ends_with($scriptDir, '/public')) {
        $parentDir = substr($scriptDir, 0, -strlen('/public'));
        if ($parentDir !== '' && str_starts_with($uri, $parentDir)) {
            $uri = substr($uri, strlen($parentDir));
        }
    }

    $uri = '/' . trim($uri, '/');

    return $uri === '' ? '/' : $uri;
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function app_config(): array
{
    static $merged = null;
    if ($merged !== null) {
        return $merged;
    }

    $config = require __DIR__ . '/../config/app.php';
    try {
        $settings = new SettingsModel();
        $name = trim($settings->get('pesantren_name', ''));
        $address = trim($settings->get('pesantren_address', ''));
        $logo = trim($settings->get('pesantren_logo', ''));
        $ndalemRuang = trim($settings->get('ndalem_ruang', ''));

        if ($name !== '') {
            $config['pesantren_name'] = $name;
        }
        if ($address !== '') {
            $config['pesantren_address'] = $address;
        }
        if ($logo !== '') {
            $config['pesantren_logo'] = $logo;
        }
        if ($ndalemRuang !== '') {
            $config['ndalem_ruang'] = $ndalemRuang;
        }
    } catch (Throwable $e) {
        // Database belum siap (mis. saat install)
    }

    $merged = $config;
    return $merged;
}

function pesantren_has_logo(): bool
{
    $app = app_config();
    return !empty($app['pesantren_logo']);
}

function save_pesantren_logo(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $config = require __DIR__ . '/../config/app.php';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $config['allowed_extensions'], true)) {
        return null;
    }
    if (($file['size'] ?? 0) > $config['upload_max_size']) {
        return null;
    }

    $dir = __DIR__ . '/../public/assets/img';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return null;
    }

    foreach (glob($dir . '/pesantren-logo.*') ?: [] as $old) {
        @unlink($old);
    }

    $filename = 'pesantren-logo.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
        return null;
    }

    return '/assets/img/' . $filename;
}

function remove_pesantren_logo_files(): void
{
    $dir = __DIR__ . '/../public/assets/img';
    foreach (glob($dir . '/pesantren-logo.*') ?: [] as $old) {
        @unlink($old);
    }
}

function view(string $name, array $data = []): void
{
    extract($data);
    $app = app_config();
    require __DIR__ . '/../views/layouts/header.php';
    require __DIR__ . '/../views/' . $name . '.php';
    require __DIR__ . '/../views/layouts/footer.php';
}

function view_partial(string $name, array $data = []): void
{
    extract($data);
    require __DIR__ . '/../views/' . $name . '.php';
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function user_initials(string $name): string
{
    $parts = explode(' ', trim($name));
    $init = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $init .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $init ?: '?';
}

function validate_ndalem_user(array $data, ?int $excludeId = null, bool $requirePassword = true): array
{
    $errors = [];
    $name = trim($data['name'] ?? '');
    $username = trim($data['username'] ?? '');

    if ($name === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }
    if ($username === '') {
        $errors[] = 'Username wajib diisi.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username hanya boleh huruf, angka, dan underscore.';
    } else {
        $userModel = new UserModel();
        if ($userModel->usernameExists($username, $excludeId)) {
            $errors[] = 'Username sudah digunakan.';
        }
    }

    if ($requirePassword) {
        $password = $data['password'] ?? '';
        $confirm = $data['password_confirm'] ?? '';
        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }
    }

    return $errors;
}

function tujuan_label(string $key): string
{
    $app = require __DIR__ . '/../config/app.php';
    return $app['tujuan_options'][$key] ?? $key;
}

function status_badge(string $status): string
{
    $labels = [
        'pending' => ['Menunggu', 'bg-yellow-100 text-yellow-800'],
        'checked_in' => ['Sudah Masuk', 'bg-blue-100 text-blue-800'],
        'in_queue' => ['Antrean Ndalem', 'bg-purple-100 text-purple-800'],
        'approved' => ['Disetujui', 'bg-green-100 text-green-800'],
        'called' => ['Dipanggil', 'bg-indigo-100 text-indigo-800'],
        'completed' => ['Selesai', 'bg-gray-100 text-gray-800'],
        'checked_out' => ['Sudah Pulang', 'bg-gray-100 text-gray-600'],
        'rejected' => ['Ditolak', 'bg-red-100 text-red-800'],
    ];
    [$label, $class] = $labels[$status] ?? [$status, 'bg-gray-100 text-gray-800'];
    return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $class . '">' . e($label) . '</span>';
}

function qr_url(string $data, int $size = 200): string
{
    return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data);
}

function area_masuk_from_tujuan(string $tujuan): string
{
    return $tujuan === 'sowan' ? 'ndalem' : 'pesantren';
}

function area_masuk_label(string $area): string
{
    return $area === 'ndalem' ? 'Ndalem (Sowan)' : 'Pesantren';
}

function visitor_waktu_label(array $visitor): string
{
    if (($visitor['jenis_kedatangan'] ?? 'sekarang') === 'jadwal' && !empty($visitor['jadwal_kunjungan'])) {
        return 'Jadwal: ' . date('d/m/Y H:i', strtotime($visitor['jadwal_kunjungan']));
    }
    return 'Sekarang (harian ini)';
}

function parse_waktu_temu_input(?string $raw): ?string
{
    $raw = trim(str_replace('T', ' ', $raw ?? ''));
    if ($raw === '') {
        return null;
    }
    if (strlen($raw) === 16) {
        $raw .= ':00';
    }
    $ts = strtotime($raw);
    if ($ts === false) {
        return null;
    }
    return date('Y-m-d H:i:s', $ts);
}

function format_waktu_temu_display(?string $datetime): string
{
    if (empty($datetime)) {
        return '-';
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return '-';
    }
    return date('d F Y', $ts) . ' pukul ' . date('H:i', $ts) . ' WIB';
}

function visitor_lokasi_temu(array $visitor): string
{
    $settings = new SettingsModel();
    $app = app_config();
    if (($visitor['area_masuk'] ?? '') === 'ndalem' || ($visitor['tujuan_kunjungan'] ?? '') === 'sowan') {
        return $settings->get('ndalem_ruang', $app['ndalem_ruang'] ?? 'Ruang Tunggu Ndalem');
    }
    return 'Kantor Pesantren';
}

function kedatangan_badge(array $visitor): string
{
    if (($visitor['jenis_kedatangan'] ?? 'sekarang') === 'jadwal' && !empty($visitor['jadwal_kunjungan'])) {
        $ts = strtotime($visitor['jadwal_kunjungan']);
        $isFuture = $ts > time();
        $cls = $isFuture ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';
        return '<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold ' . $cls . '">📅 ' . date('d/m H:i', $ts) . '</span>';
    }
    return '<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Sekarang</span>';
}

function jadwal_terima_hari_options(): array
{
    return JadwalTerimaTamuModel::HARI;
}

function render_jadwal_terima_html(array $grouped, string $areaLabel): string
{
    if (empty($grouped)) {
        return '<p class="text-sm text-gray-400 italic">Belum ada jadwal terima tamu untuk ' . e($areaLabel) . '.</p>';
    }
    $model = new JadwalTerimaTamuModel();
    $html = '<div class="space-y-2">';
    foreach ($grouped as $hari => $slots) {
        $slotTexts = array_map(fn($s) => e($model->formatSlot($s)), $slots);
        $html .= '<div class="flex gap-2 text-sm">';
        $html .= '<span class="font-semibold text-gray-700 w-16 flex-shrink-0">' . e($model->hariLabel((int) $hari)) . '</span>';
        $html .= '<span class="text-gray-600">' . implode('<span class="text-gray-300 mx-1">·</span>', $slotTexts) . '</span>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

function jadwal_terima_json_for_guest(string $area): array
{
    $model = new JadwalTerimaTamuModel();
    $grouped = $model->getGroupedByArea($area);
    $out = [];
    foreach ($grouped as $hari => $slots) {
        $out[(string) $hari] = array_map(static function ($s) {
            return [
                'mulai' => substr($s['jam_mulai'], 0, 5),
                'selesai' => substr($s['jam_selesai'], 0, 5),
                'ket' => $s['keterangan'] ?? '',
            ];
        }, $slots);
    }
    return $out;
}

function validate_jadwal_terima(array $data): array
{
    $errors = [];
    if (!isset($data['hari']) || $data['hari'] === '') {
        $errors[] = 'Hari wajib dipilih.';
    }
    if (empty($data['jam_mulai']) || empty($data['jam_selesai'])) {
        $errors[] = 'Jam mulai dan selesai wajib diisi.';
    } elseif ($data['jam_mulai'] >= $data['jam_selesai']) {
        $errors[] = 'Jam selesai harus setelah jam mulai.';
    }
    if (!in_array($data['area'] ?? '', ['pesantren', 'ndalem'], true)) {
        $errors[] = 'Area tidak valid.';
    }
    return $errors;
}

function guest_form_jadwal_data(): array
{
    return [
        'jadwalPesantrenJson' => jadwal_terima_json_for_guest('pesantren'),
        'jadwalNdalemJson' => jadwal_terima_json_for_guest('ndalem'),
    ];
}
