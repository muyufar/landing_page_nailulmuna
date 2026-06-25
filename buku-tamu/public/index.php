<?php

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/HijriDate.php';
require_once __DIR__ . '/../src/WhatsAppService.php';
require_once __DIR__ . '/../src/VisitorModel.php';
require_once __DIR__ . '/../src/UserModel.php';
require_once __DIR__ . '/../src/SettingsModel.php';
require_once __DIR__ . '/../src/JadwalTerimaTamuModel.php';
require_once __DIR__ . '/../src/helpers.php';

Auth::startSession();

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

$scriptDir = rawurldecode(dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir !== '/' && $scriptDir !== '\\' && str_starts_with($uri, $scriptDir)) {
    $uri = substr($uri, strlen($scriptDir));
}
$uri = '/' . trim($uri, '/');
if ($uri === '/') {
    $uri = '/';
}

$visitorModel = new VisitorModel();
$userModel = new UserModel();
$settingsModel = new SettingsModel();
$jadwalModel = new JadwalTerimaTamuModel();
$pengasuhModel = new PengasuhStatusModel();
$whatsapp = new WhatsAppService($settingsModel);

// Pengingat jadwal otomatis (skip request API polling agar ringan)
$isApiRoute = str_starts_with($uri, '/admin/api') || str_starts_with($uri, '/ndalem/api') || str_starts_with($uri, '/api/');
if (!$isApiRoute) {
    $waLockFile = __DIR__ . '/../storage/wa_cron.lock';
    if (!is_dir(dirname($waLockFile))) {
        mkdir(dirname($waLockFile), 0755, true);
    }
    if (!file_exists($waLockFile) || (time() - filemtime($waLockFile)) >= 300) {
        touch($waLockFile);
        $whatsapp->processScheduledReminders();
    }
}

// --- GUEST ROUTES ---

if ($uri === '/' && $method === 'GET') {
    $pengasuh = $pengasuhModel->getCurrent();
    view('guest/form', array_merge([
        'pengasuh' => $pengasuh,
        'pengasuhModel' => $pengasuhModel,
    ], guest_form_jadwal_data()));
    exit;
}

if ($uri === '/api/jadwal-terima' && $method === 'GET') {
    $area = $_GET['area'] ?? 'pesantren';
    if (!in_array($area, ['pesantren', 'ndalem'], true)) {
        $area = 'pesantren';
    }
    json_response([
        'area' => $area,
        'label' => area_masuk_label($area),
        'jadwal' => jadwal_terima_json_for_guest($area),
        'hari' => jadwal_terima_hari_options(),
    ]);
}

if ($uri === '/submit' && $method === 'POST') {
    $tujuan = $_POST['tujuan_kunjungan'] ?? '';

    if ($tujuan === 'sowan' && !$pengasuhModel->isSowanAvailable()) {
        $pengasuh = $pengasuhModel->getCurrent();
        view('guest/form', array_merge([
            'error' => 'Maaf, Pengasuh sedang tidak dapat menerima tamu sowan. ' . ($pengasuh['message'] ?? ''),
            'pengasuh' => $pengasuh,
            'pengasuhModel' => $pengasuhModel,
            'old' => $_POST,
        ], guest_form_jadwal_data()));
        exit;
    }

    $errors = [];
    if (empty(trim($_POST['nama_lengkap'] ?? ''))) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty(trim($_POST['asal'] ?? ''))) $errors[] = 'Asal daerah/instansi wajib diisi.';
    if (empty($tujuan)) $errors[] = 'Tujuan kunjungan wajib dipilih.';
    if ($tujuan === 'jenguk' && empty(trim($_POST['nama_santri'] ?? ''))) {
        $errors[] = 'Nama santri wajib diisi untuk jenguk santri.';
    }

    $jenisKedatangan = $_POST['jenis_kedatangan'] ?? 'sekarang';
    $jadwalKunjungan = null;
    if ($jenisKedatangan === 'jadwal') {
        $jadwalRaw = trim($_POST['jadwal_kunjungan'] ?? '');
        if ($jadwalRaw === '') {
            $errors[] = 'Tanggal & waktu jadwal wajib diisi.';
        } else {
            $jadwalKunjungan = str_replace('T', ' ', $jadwalRaw);
            if (strlen($jadwalKunjungan) === 16) {
                $jadwalKunjungan .= ':00';
            }
            if (strtotime($jadwalKunjungan) < time() - 60) {
                $errors[] = 'Jadwal kunjungan harus di waktu mendatang.';
            }
        }
    }

    if ($errors) {
        view('guest/form', array_merge([
            'errors' => $errors,
            'pengasuh' => $pengasuhModel->getCurrent(),
            'pengasuhModel' => $pengasuhModel,
            'old' => $_POST,
        ], guest_form_jadwal_data()));
        exit;
    }

    $fotoPath = null;
    if (!empty($_FILES['foto']['name'])) {
        $uploadDir = $appConfig['upload_path'];
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $appConfig['allowed_extensions']) && $_FILES['foto']['size'] <= $appConfig['upload_max_size']) {
            $filename = uniqid('foto_') . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $filename)) {
                $fotoPath = 'uploads/' . $filename;
            }
        }
    }

    $visitor = $visitorModel->create([
        'nama_lengkap' => trim($_POST['nama_lengkap']),
        'no_hp' => trim($_POST['no_hp'] ?? ''),
        'asal' => trim($_POST['asal']),
        'jumlah_rombongan' => $_POST['jumlah_rombongan'] ?? '1',
        'tujuan_kunjungan' => $tujuan,
        'detail_keperluan' => trim($_POST['detail_keperluan'] ?? ''),
        'nama_santri' => $tujuan === 'jenguk' ? trim($_POST['nama_santri']) : null,
        'foto_path' => $fotoPath,
        'jenis_kedatangan' => $jenisKedatangan,
        'jadwal_kunjungan' => $jadwalKunjungan,
        'area_masuk' => area_masuk_from_tujuan($tujuan),
    ]);

    // WA segera: staff + tamu + pengingat jadwal
    $whatsapp->dispatchOnRegister($visitor);

    redirect('/ticket/' . $visitor['ticket_code']);
}

if (preg_match('#^/ticket/([A-Z0-9]+)$#', $uri, $m) && $method === 'GET') {
    $visitor = $visitorModel->findByTicket($m[1]);
    if (!$visitor) {
        http_response_code(404);
        view('guest/notfound');
        exit;
    }
    view('guest/ticket', ['visitor' => $visitor]);
    exit;
}

// --- ADMIN ROUTES ---

if ($uri === '/admin/login' && $method === 'GET') {
    view('admin/login');
    exit;
}

if ($uri === '/admin/login' && $method === 'POST') {
    $user = Auth::login($_POST['username'] ?? '', $_POST['password'] ?? '', 'admin');
    if ($user) redirect('/admin');
    view('admin/login', ['error' => 'Username atau password salah.']);
    exit;
}

if ($uri === '/admin/logout') {
    Auth::logout();
    redirect('/admin/login');
}

if ($uri === '/admin' && $method === 'GET') {
    Auth::requireRole('admin');
    $stats = $visitorModel->getStatistics('week');
    view('admin/dashboard', [
        'visitors' => $visitorModel->getTodayVisitors(),
        'active' => $visitorModel->getActiveInside(),
        'stats' => $stats,
        'error' => $_GET['error'] ?? null,
    ]);
    exit;
}

if ($uri === '/admin/api/visitors' && $method === 'GET') {
    Auth::requireRole('admin');
    json_response([
        'active' => $visitorModel->getActiveInside(),
        'today' => $visitorModel->getTodayVisitors(),
        'stats' => $visitorModel->getStatistics('week'),
    ]);
}

if ($uri === '/admin/statistik' && $method === 'GET') {
    Auth::requireRole('admin');
    $period = $_GET['period'] ?? 'week';
    view('admin/statistik', [
        'stats' => $visitorModel->getStatistics($period),
        'period' => $period,
    ]);
    exit;
}

if ($uri === '/admin/qr' && $method === 'GET') {
    Auth::requireRole('admin');
    view('admin/qr');
    exit;
}

if (preg_match('#^/admin/checkin/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('admin');
    $id = (int) $m[1];
    $visitor = $visitorModel->findById($id);
    if ($visitor && $visitor['status'] === 'pending') {
        $waktuTemui = parse_waktu_temu_input($_POST['waktu_temu'] ?? '');
        $hasHp = trim($visitor['no_hp'] ?? '') !== '';
        $isSowan = $visitor['tujuan_kunjungan'] === 'sowan';

        if ($hasHp && !$isSowan && !$waktuTemui) {
            redirect('/admin?error=' . urlencode('Isi waktu temu tamu untuk mengirim notifikasi WA.'));
        }

        $newStatus = $isSowan ? 'in_queue' : 'checked_in';
        $visitorModel->updateStatus($id, $newStatus);
        if ($waktuTemui) {
            $visitorModel->setWaktuTemui($id, $waktuTemui);
        }
        $visitor = $visitorModel->findById($id);
        if ($visitor) {
            $whatsapp->dispatchOnCheckin($visitor);
            if ($waktuTemui && $visitor['tujuan_kunjungan'] !== 'sowan') {
                $whatsapp->notifyGuestWaktuTemui($visitor);
            }
        }
    }
    redirect('/admin');
}

if (preg_match('#^/admin/checkout/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('admin');
    $visitorModel->updateStatus((int) $m[1], 'checked_out');
    redirect('/admin');
}

if (preg_match('#^/admin/print/(\d+)$#', $uri, $m) && $method === 'GET') {
    Auth::requireRole('admin');
    $visitor = $visitorModel->findById((int) $m[1]);
    if (!$visitor) redirect('/admin');
    view('admin/print', ['visitor' => $visitor]);
    exit;
}

// --- ADMIN: Pengaturan Akun Ndalem ---

if ($uri === '/admin/pengaturan/ndalem' && $method === 'GET') {
    Auth::requireRole('admin');
    view('admin/ndalem_users', [
        'users' => $userModel->getByRole('ndalem'),
        'success' => $_GET['success'] ?? null,
        'error' => $_GET['error'] ?? null,
    ]);
    exit;
}

if ($uri === '/admin/pengaturan/ndalem/baru' && $method === 'GET') {
    Auth::requireRole('admin');
    view('admin/ndalem_form', ['mode' => 'create', 'old' => []]);
    exit;
}

if ($uri === '/admin/pengaturan/ndalem/baru' && $method === 'POST') {
    Auth::requireRole('admin');
    $errors = validate_ndalem_user($_POST);
    if ($errors) {
        view('admin/ndalem_form', ['mode' => 'create', 'errors' => $errors, 'old' => $_POST]);
        exit;
    }
    $userModel->create(
        trim($_POST['username']),
        $_POST['password'],
        trim($_POST['name']),
        'ndalem'
    );
    redirect('/admin/pengaturan/ndalem?success=' . urlencode('Akun ndalem berhasil dibuat.'));
}

if (preg_match('#^/admin/pengaturan/ndalem/edit/(\d+)$#', $uri, $m) && $method === 'GET') {
    Auth::requireRole('admin');
    $ndalem = $userModel->findNdalemById((int) $m[1]);
    if (!$ndalem) redirect('/admin/pengaturan/ndalem');
    view('admin/ndalem_form', [
        'mode' => 'edit',
        'ndalem' => $ndalem,
        'old' => ['name' => $ndalem['name'], 'username' => $ndalem['username']],
    ]);
    exit;
}

if (preg_match('#^/admin/pengaturan/ndalem/edit/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('admin');
    $id = (int) $m[1];
    $ndalem = $userModel->findNdalemById($id);
    if (!$ndalem) redirect('/admin/pengaturan/ndalem');

    $errors = validate_ndalem_user($_POST, $id, false);
    if ($errors) {
        view('admin/ndalem_form', ['mode' => 'edit', 'ndalem' => $ndalem, 'errors' => $errors, 'old' => $_POST]);
        exit;
    }
    $userModel->update($id, trim($_POST['username']), trim($_POST['name']));
    redirect('/admin/pengaturan/ndalem?success=' . urlencode('Data akun berhasil diperbarui.'));
}

if (preg_match('#^/admin/pengaturan/ndalem/password/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('admin');
    $id = (int) $m[1];
    if (!$userModel->findNdalemById($id)) redirect('/admin/pengaturan/ndalem');

    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    if (strlen($password) < 6) {
        redirect('/admin/pengaturan/ndalem?error=' . urlencode('Password minimal 6 karakter.'));
    }
    if ($password !== $confirm) {
        redirect('/admin/pengaturan/ndalem?error=' . urlencode('Konfirmasi password tidak cocok.'));
    }
    $userModel->updatePassword($id, $password);
    redirect('/admin/pengaturan/ndalem?success=' . urlencode('Password berhasil diubah.'));
}

if (preg_match('#^/admin/pengaturan/ndalem/hapus/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('admin');
    if (!$userModel->deleteNdalem((int) $m[1])) {
        redirect('/admin/pengaturan/ndalem?error=' . urlencode('Tidak dapat menghapus. Minimal harus ada 1 akun ndalem.'));
    }
    redirect('/admin/pengaturan/ndalem?success=' . urlencode('Akun ndalem berhasil dihapus.'));
}

if ($uri === '/admin/pengaturan/identitas' && $method === 'GET') {
    Auth::requireRole('admin');
    view('admin/identitas', [
        'settings' => $settingsModel->getIdentitySettings(),
        'success' => $_GET['success'] ?? null,
        'error' => $_GET['error'] ?? null,
    ]);
    exit;
}

if ($uri === '/admin/pengaturan/identitas' && $method === 'POST') {
    Auth::requireRole('admin');
    $name = trim($_POST['pesantren_name'] ?? '');
    if ($name === '') {
        redirect('/admin/pengaturan/identitas?error=' . urlencode('Nama pesantren wajib diisi.'));
    }

    $data = [
        'pesantren_name' => $name,
        'pesantren_address' => trim($_POST['pesantren_address'] ?? ''),
    ];

    $currentLogo = $settingsModel->get('pesantren_logo', '');

    if (!empty($_POST['remove_logo'])) {
        remove_pesantren_logo_files();
        $data['pesantren_logo'] = '';
    } elseif (!empty($_FILES['pesantren_logo']['name'])) {
        $logoPath = save_pesantren_logo($_FILES['pesantren_logo']);
        if ($logoPath === null) {
            redirect('/admin/pengaturan/identitas?error=' . urlencode('Logo gagal diunggah. Gunakan JPG/PNG/WebP maks. 5 MB.'));
        }
        $data['pesantren_logo'] = $logoPath;
    } elseif ($currentLogo !== '') {
        $data['pesantren_logo'] = $currentLogo;
    }

    $settingsModel->setMany($data);
    redirect('/admin/pengaturan/identitas?success=' . urlencode('Identitas pesantren berhasil disimpan.'));
}

if ($uri === '/admin/pengaturan/whatsapp' && $method === 'GET') {
    Auth::requireRole('admin');
    view('admin/whatsapp_settings', [
        'settings' => $settingsModel->getAllForAdmin(),
        'success' => $_GET['success'] ?? null,
    ]);
    exit;
}

if ($uri === '/admin/pengaturan/whatsapp' && $method === 'POST') {
    Auth::requireRole('admin');
    $checkboxes = [
        'whatsapp_enabled', 'wa_enabled_pengasuh', 'wa_enabled_ndalem', 'wa_enabled_kantor',
        'wa_on_register_pengasuh', 'wa_on_register_ndalem', 'wa_on_register_kantor',
        'wa_on_checkin_pengasuh', 'wa_on_checkin_ndalem', 'wa_on_checkin_kantor',
        'wa_on_jadwal_pengasuh', 'wa_on_jadwal_ndalem', 'wa_on_jadwal_kantor',
        'wa_on_approve_guest', 'wa_on_register_guest',
    ];
    $data = [
        'whatsapp_provider' => $_POST['whatsapp_provider'] ?? 'fonnte',
        'whatsapp_token' => trim($_POST['whatsapp_token'] ?? ''),
        'wa_phone_pengasuh' => trim($_POST['wa_phone_pengasuh'] ?? ''),
        'wa_phone_ndalem' => trim($_POST['wa_phone_ndalem'] ?? ''),
        'wa_phone_kantor' => trim($_POST['wa_phone_kantor'] ?? ''),
        'wa_jadwal_reminder_minutes' => (string) max(5, (int) ($_POST['wa_jadwal_reminder_minutes'] ?? 60)),
        'ndalem_ruang' => trim($_POST['ndalem_ruang'] ?? 'Ruang Tunggu Ndalem Barat'),
    ];
    foreach ($checkboxes as $key) {
        $data[$key] = isset($_POST[$key]) ? '1' : '0';
    }
    $settingsModel->setMany($data);
    redirect('/admin/pengaturan/whatsapp?success=' . urlencode('Pengaturan WhatsApp berhasil disimpan.'));
}

if ($uri === '/admin/pengaturan/jadwal' && $method === 'GET') {
    Auth::requireRole('admin');
    $area = ($_GET['area'] ?? 'ndalem') === 'pesantren' ? 'pesantren' : 'ndalem';
    view('admin/jadwal_terima', [
        'area' => $area,
        'items' => $jadwalModel->getAll($area),
        'grouped' => $jadwalModel->getGroupedByArea($area),
        'jadwalModel' => $jadwalModel,
        'success' => $_GET['success'] ?? null,
        'error' => $_GET['error'] ?? null,
    ]);
    exit;
}

if ($uri === '/admin/pengaturan/jadwal/tambah' && $method === 'POST') {
    Auth::requireRole('admin');
    $area = $_POST['area'] ?? 'ndalem';
    $errors = validate_jadwal_terima($_POST);
    if ($errors) {
        redirect('/admin/pengaturan/jadwal?area=' . urlencode($area) . '&error=' . urlencode(implode(' ', $errors)));
    }
    $jadwalModel->create([
        'area' => $area,
        'hari' => $_POST['hari'],
        'jam_mulai' => $_POST['jam_mulai'] . ':00',
        'jam_selesai' => $_POST['jam_selesai'] . ':00',
        'keterangan' => trim($_POST['keterangan'] ?? '') ?: null,
        'is_active' => isset($_POST['is_active']),
    ]);
    redirect('/admin/pengaturan/jadwal?area=' . urlencode($area) . '&success=' . urlencode('Jadwal berhasil ditambahkan.'));
}

if (preg_match('#^/admin/pengaturan/jadwal/edit/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('admin');
    $id = (int) $m[1];
    $item = $jadwalModel->findById($id);
    if (!$item) redirect('/admin/pengaturan/jadwal');
    $errors = validate_jadwal_terima($_POST);
    $area = $_POST['area'] ?? $item['area'];
    if ($errors) {
        redirect('/admin/pengaturan/jadwal?area=' . urlencode($area) . '&error=' . urlencode(implode(' ', $errors)));
    }
    $jadwalModel->update($id, [
        'area' => $area,
        'hari' => $_POST['hari'],
        'jam_mulai' => $_POST['jam_mulai'] . (strlen($_POST['jam_mulai']) === 5 ? ':00' : ''),
        'jam_selesai' => $_POST['jam_selesai'] . (strlen($_POST['jam_selesai']) === 5 ? ':00' : ''),
        'keterangan' => trim($_POST['keterangan'] ?? '') ?: null,
        'is_active' => isset($_POST['is_active']),
    ]);
    redirect('/admin/pengaturan/jadwal?area=' . urlencode($area) . '&success=' . urlencode('Jadwal berhasil diperbarui.'));
}

if (preg_match('#^/admin/pengaturan/jadwal/hapus/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('admin');
    $item = $jadwalModel->findById((int) $m[1]);
    $area = $item['area'] ?? 'ndalem';
    $jadwalModel->delete((int) $m[1]);
    redirect('/admin/pengaturan/jadwal?area=' . urlencode($area) . '&success=' . urlencode('Jadwal dihapus.'));
}

// --- NDALEM ROUTES ---

if ($uri === '/ndalem/login' && $method === 'GET') {
    view('ndalem/login');
    exit;
}

if ($uri === '/ndalem/login' && $method === 'POST') {
    $user = Auth::login($_POST['username'] ?? '', $_POST['password'] ?? '', 'ndalem');
    if ($user) redirect('/ndalem');
    view('ndalem/login', ['error' => 'Username atau password salah.']);
    exit;
}

if ($uri === '/ndalem/logout') {
    Auth::logout();
    redirect('/ndalem/login');
}

if ($uri === '/ndalem' && $method === 'GET') {
    Auth::requireRole('ndalem');
    view('ndalem/dashboard', [
        'queue' => $visitorModel->getSowanQueue(),
        'summary' => $visitorModel->getNdalemRecapSummary(),
        'pengasuh' => $pengasuhModel->getCurrent(),
        'pengasuhModel' => $pengasuhModel,
        'success' => $_GET['success'] ?? null,
        'error' => $_GET['error'] ?? null,
    ]);
    exit;
}

if ($uri === '/ndalem/rekap' && $method === 'GET') {
    Auth::requireRole('ndalem');
    view('ndalem/rekap', [
        'upcoming' => $visitorModel->getNdalemUpcoming(),
        'entered' => $visitorModel->getNdalemEntered(),
        'summary' => $visitorModel->getNdalemRecapSummary(),
    ]);
    exit;
}

if ($uri === '/ndalem/jadwal' && $method === 'GET') {
    Auth::requireRole('ndalem');
    view('ndalem/jadwal', [
        'ndalemGrouped' => $jadwalModel->getGroupedByArea('ndalem'),
        'pesantrenGrouped' => $jadwalModel->getGroupedByArea('pesantren'),
    ]);
    exit;
}

if ($uri === '/ndalem/api/queue' && $method === 'GET') {
    Auth::requireRole('ndalem');
    json_response([
        'queue' => $visitorModel->getSowanQueue(),
        'summary' => $visitorModel->getNdalemRecapSummary(),
        'pengasuh' => $pengasuhModel->getCurrent(),
    ]);
}

if ($uri === '/ndalem/api/rekap' && $method === 'GET') {
    Auth::requireRole('ndalem');
    json_response([
        'upcoming' => $visitorModel->getNdalemUpcoming(),
        'entered' => $visitorModel->getNdalemEntered(),
        'summary' => $visitorModel->getNdalemRecapSummary(),
    ]);
}

if ($uri === '/ndalem/status' && $method === 'POST') {
    Auth::requireRole('ndalem');
    $user = Auth::user();
    $status = $_POST['status'] ?? 'available';
    $messages = [
        'available' => 'Pengasuh sedang luang dan menerima tamu sowan.',
        'busy' => 'Pengasuh sedang menerima tamu. Antrean mungkin lebih lama.',
        'closed' => 'Pengasuh sedang istirahat / tidak menerima tamu sowan.',
    ];
    $pengasuhModel->update($status, $messages[$status] ?? '', $user['id']);
    redirect('/ndalem');
}

if (preg_match('#^/ndalem/approve/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('ndalem');
    $user = Auth::user();
    $id = (int) $m[1];
    $visitor = $visitorModel->findById($id);
    if ($visitor) {
        $waktuTemui = parse_waktu_temu_input($_POST['waktu_temu'] ?? '');
        if (!$waktuTemui) {
            redirect('/ndalem?error=' . urlencode('Waktu temu wajib diisi saat menyetujui tamu.'));
        }
        $visitorModel->updateStatus($id, 'approved', $user['id']);
        $visitorModel->setWaktuTemui($id, $waktuTemui);
        $visitor = $visitorModel->findById($id);
        if ($visitor) {
            $whatsapp->dispatchOnApprove($visitor);
            $db = Database::connect();
            $db->prepare('UPDATE visitors SET whatsapp_sent = 1 WHERE id = ?')->execute([$id]);
        }
    }
    redirect('/ndalem?success=' . urlencode('Tamu disetujui. WA jadwal temu dikirim jika nomor HP tersedia.'));
}

if (preg_match('#^/ndalem/call/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('ndalem');
    $visitorModel->updateStatus((int) $m[1], 'called');
    redirect('/ndalem');
}

if (preg_match('#^/ndalem/complete/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('ndalem');
    $visitorModel->updateStatus((int) $m[1], 'completed');
    redirect('/ndalem');
}

if (preg_match('#^/ndalem/reject/(\d+)$#', $uri, $m) && $method === 'POST') {
    Auth::requireRole('ndalem');
    $visitorModel->updateStatus((int) $m[1], 'rejected');
    redirect('/ndalem');
}

// QR landing page for pesantren entrance
if ($uri === '/scan' && $method === 'GET') {
    redirect('/');
}

http_response_code(404);
view('guest/notfound');
