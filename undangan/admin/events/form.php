<?php

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/themes.php';
require_once __DIR__ . '/../../includes/sections.php';
require_once __DIR__ . '/../../includes/invitation_pages.php';

function eventFormFields(): array
{
    return [
        'title', 'slug', 'pesantren_name', 'theme_statement', 'mukaddimah', 'quran_quote',
        'date_masehi', 'date_hijriah', 'event_time', 'countdown_target',
        'location_name', 'location_address', 'maps_url',
        'speaker_name', 'speaker_origin',
        'dresscode_pria', 'dresscode_wanita', 'special_rules',
        'color_primary', 'color_accent', 'theme_preset', 'audio_mode', 'audio_url', 'seat_capacity', 'status',
    ];
}

function collectEventData(array $post): array
{
    return [
        'title'            => trim($post['title'] ?? ''),
        'slug'             => slugify($post['slug'] ?? $post['title'] ?? ''),
        'pesantren_name'   => trim($post['pesantren_name'] ?? ''),
        'theme_statement'  => trim($post['theme_statement'] ?? ''),
        'invitation_greeting' => trim($post['invitation_greeting'] ?? 'Kepada Yth. Bapak/Ibu/Saudara/i'),
        'mukaddimah'       => trim($post['mukaddimah'] ?? ''),
        'quran_quote'      => trim($post['quran_quote'] ?? ''),
        'date_masehi'      => trim($post['date_masehi'] ?? ''),
        'date_hijriah'     => trim($post['date_hijriah'] ?? ''),
        'event_time'       => trim($post['event_time'] ?? ''),
        'countdown_target' => !empty($post['countdown_target']) ? $post['countdown_target'] : null,
        'location_name'    => trim($post['location_name'] ?? ''),
        'location_address' => trim($post['location_address'] ?? ''),
        'maps_url'         => trim($post['maps_url'] ?? ''),
        'speaker_name'     => trim($post['speaker_name'] ?? ''),
        'speaker_origin'   => trim($post['speaker_origin'] ?? ''),
        'dresscode_pria'   => trim($post['dresscode_pria'] ?? ''),
        'dresscode_wanita' => trim($post['dresscode_wanita'] ?? ''),
        'special_rules'    => trim($post['special_rules'] ?? ''),
        'event_schedule'   => buildEventScheduleFromPost($post),
        'color_primary'    => trim($post['color_primary'] ?? '#064E3B'),
        'color_accent'     => trim($post['color_accent'] ?? '#D4AF37'),
        'theme_preset'     => getValidThemePreset($post['theme_preset'] ?? 'hijau_emas'),
        'font_preset'      => getValidFontPreset($post['font_preset'] ?? 'klasik'),
        'ornament_animation' => getValidAnimationMode($post['ornament_animation'] ?? 'melayang'),
        'audio_mode'       => in_array($post['audio_mode'] ?? '', ['synth', 'url']) ? $post['audio_mode'] : 'synth',
        'audio_url'        => trim($post['audio_url'] ?? ''),
        'seat_capacity'    => max(1, (int) ($post['seat_capacity'] ?? 500)),
        'status'           => in_array($post['status'] ?? '', ['aktif', 'arsip']) ? $post['status'] : 'aktif',
        'auto_scroll'      => !empty($post['auto_scroll']) ? 1 : 0,
        'animated_ornaments' => !empty($post['animated_ornaments']) ? 1 : 0,
        'scroll_interval'  => max(2, (int) ($post['scroll_interval'] ?? 5)),
        'scroll_speed'     => max(300, min(3000, (int) ($post['scroll_speed'] ?? 800))),
        'scroll_snap'      => !empty($post['scroll_snap']) ? 1 : 0,
        'invitation_pages' => buildInvitationPagesFromPost($post),
        'section_animations' => buildSectionAnimationsFromPost(
            $post,
            getValidAnimationMode($post['ornament_animation'] ?? 'melayang')
        ),
    ];
}

function validateEventData(array $data): array
{
    $errors = [];
    if ($data['title'] === '') {
        $errors[] = 'Judul acara wajib diisi.';
    }
    if ($data['slug'] === '') {
        $errors[] = 'Slug URL wajib diisi.';
    }
    if (!isValidHexColor($data['color_primary'])) {
        $errors[] = 'Warna primer tidak valid.';
    }
    if (!isValidHexColor($data['color_accent'])) {
        $errors[] = 'Warna aksen tidak valid.';
    }
    if ($data['audio_mode'] === 'url' && $data['audio_url'] !== '' && !isValidMp3Url($data['audio_url'])) {
        $errors[] = 'URL audio harus berupa tautan .mp3 yang valid.';
    }
    return $errors;
}

function renderEventForm(array $event = [], string $formAction = '', string $submitLabel = 'Simpan'): void
{
    $event = array_merge([
        'title' => '', 'slug' => '', 'pesantren_name' => '', 'theme_statement' => '',
        'mukaddimah' => '', 'quran_quote' => '', 'date_masehi' => '', 'date_hijriah' => '',
        'event_time' => '', 'countdown_target' => '', 'location_name' => '', 'location_address' => '',
        'maps_url' => '', 'speaker_name' => '', 'speaker_origin' => '',
        'dresscode_pria' => '', 'dresscode_wanita' => '', 'special_rules' => '',
        'color_primary'    => '#064E3B', 'color_accent' => '#D4AF37', 'theme_preset' => 'hijau_emas',
        'font_preset' => 'klasik', 'font_custom_title' => '', 'font_custom_body' => '',
        'ornament_animation' => 'melayang',
        'logo_pesantren' => '', 'logo_haflah' => '',
        'ornament_top' => '', 'ornament_divider' => '', 'ornament_bottom' => '', 'bg_image' => '',
        'invitation_greeting' => 'Kepada Yth. Bapak/Ibu/Saudara/i', 'event_schedule' => '',
        'auto_scroll' => 1, 'animated_ornaments' => 1,
        'scroll_interval' => 5, 'scroll_speed' => 800, 'scroll_snap' => 1,
        'section_animations' => '', 'invitation_pages' => '',
        'audio_mode' => 'synth', 'audio_url' => '', 'seat_capacity' => 500, 'status' => 'aktif',
    ], $event);

    $presets = getThemePresets();
    $fontPresets = getFontPresets();
    $animModes = getAnimationModes();
    $currentPreset = $event['theme_preset'] ?? 'hijau_emas';
    $currentFont = $event['font_preset'] ?? 'klasik';
    $currentAnim = $event['ornament_animation'] ?? 'melayang';
    $sectionAnimCfg = parseSectionAnimations($event['section_animations'] ?? '');
    $invPages       = resolveInvitationPages($event['invitation_pages'] ?? '');
    $builtinMeta    = getBuiltinPageMeta();
    $scheduleItems = parseEventSchedule($event['event_schedule'] ?? '');
    if (empty($scheduleItems)) {
        $scheduleItems = [['time' => '', 'title' => '', 'desc' => '']];
    }

    $activeTab = $_GET['tab'] ?? 'konten';
    ?>
    <div class="editor-tabs" role="tablist">
        <button type="button" class="tab <?= $activeTab === 'konten' ? 'active' : '' ?>" data-tab="konten">Konten Teks</button>
        <button type="button" class="tab <?= $activeTab === 'halaman' ? 'active' : '' ?>" data-tab="halaman">Halaman</button>
        <button type="button" class="tab <?= $activeTab === 'visual' ? 'active' : '' ?>" data-tab="visual">Tema & Logo</button>
        <button type="button" class="tab <?= $activeTab === 'audio' ? 'active' : '' ?>" data-tab="audio">Audio</button>
        <button type="button" class="tab <?= $activeTab === 'pengaturan' ? 'active' : '' ?>" data-tab="pengaturan">Pengaturan</button>
    </div>

    <form method="POST" enctype="multipart/form-data" action="<?= e($formAction) ?>" class="event-form">
        <input type="hidden" name="tab" value="<?= e($activeTab) ?>">

        <div class="tab-panel <?= $activeTab === 'konten' ? 'active' : '' ?>" data-tab="konten">
            <div class="form-grid">
                <label class="full">
                    Judul Acara *
                    <input type="text" name="title" value="<?= e($event['title']) ?>" required>
                </label>
                <label>
                    Nama Pesantren
                    <input type="text" name="pesantren_name" value="<?= e($event['pesantren_name']) ?>">
                </label>
                <label>
                    Slug URL *
                    <input type="text" name="slug" value="<?= e($event['slug']) ?>" pattern="[a-z0-9-]+" placeholder="haflah-2026">
                </label>
                <label class="full">
                    Tema Haflah
                    <input type="text" name="theme_statement" value="<?= e($event['theme_statement']) ?>">
                </label>
                <label class="full">
                    Salam Pembuka Undangan (label nama tamu)
                    <input type="text" name="invitation_greeting" value="<?= e($event['invitation_greeting']) ?>" placeholder="Kepada Yth. Bapak/Ibu/Saudara/i">
                    <small>Ditampilkan di layar awal bersama nama tamu dari link personal</small>
                </label>
                <label class="full">
                    Mukaddimah (Pembuka)
                    <textarea name="mukaddimah" rows="4"><?= e($event['mukaddimah']) ?></textarea>
                </label>
                <label class="full">
                    Kutipan Al-Qur'an / Hadits
                    <textarea name="quran_quote" rows="2"><?= e($event['quran_quote']) ?></textarea>
                </label>
                <label>
                    Tanggal Masehi
                    <input type="text" name="date_masehi" value="<?= e($event['date_masehi']) ?>" placeholder="Ahad, 15 Juni 2026">
                </label>
                <label>
                    Tanggal Hijriah
                    <input type="text" name="date_hijriah" value="<?= e($event['date_hijriah']) ?>">
                </label>
                <label>
                    Waktu Acara
                    <input type="text" name="event_time" value="<?= e($event['event_time']) ?>" placeholder="08.00 WIB – selesai">
                </label>
                <label>
                    Target Hitung Mundur
                    <input type="datetime-local" name="countdown_target" value="<?= $event['countdown_target'] ? date('Y-m-d\TH:i', strtotime($event['countdown_target'])) : '' ?>">
                </label>
                <label>
                    Nama Lokasi
                    <input type="text" name="location_name" value="<?= e($event['location_name']) ?>">
                </label>
                <label class="full">
                    Alamat Lengkap
                    <textarea name="location_address" rows="2"><?= e($event['location_address']) ?></textarea>
                </label>
                <label class="full">
                    URL Google Maps
                    <input type="url" name="maps_url" value="<?= e($event['maps_url']) ?>">
                </label>
                <label>
                    Nama Penceramah
                    <input type="text" name="speaker_name" value="<?= e($event['speaker_name']) ?>">
                </label>
                <label>
                    Asal Penceramah
                    <input type="text" name="speaker_origin" value="<?= e($event['speaker_origin']) ?>">
                </label>
                <label class="full">
                    Dress Code Pria
                    <input type="text" name="dresscode_pria" value="<?= e($event['dresscode_pria']) ?>">
                </label>
                <label class="full">
                    Dress Code Wanita
                    <input type="text" name="dresscode_wanita" value="<?= e($event['dresscode_wanita']) ?>">
                </label>
                <label class="full">
                    Tata Tertib Khusus
                    <textarea name="special_rules" rows="3"><?= e($event['special_rules']) ?></textarea>
                </label>
            </div>

            <h3 class="subsection-title">Susunan Acara</h3>
            <p class="field-hint">Isi rangkaian acara haflah yang akan ditampilkan di undangan.</p>
            <div id="schedule-editor" class="schedule-editor">
                <?php foreach ($scheduleItems as $idx => $item): ?>
                <div class="schedule-row">
                    <input type="text" name="schedule_time[]" value="<?= e($item['time']) ?>" placeholder="08.00" class="schedule-time">
                    <input type="text" name="schedule_title[]" value="<?= e($item['title']) ?>" placeholder="Nama acara" class="schedule-title">
                    <input type="text" name="schedule_desc[]" value="<?= e($item['desc']) ?>" placeholder="Keterangan (opsional)" class="schedule-desc">
                    <button type="button" class="btn btn-sm btn-danger schedule-remove" title="Hapus baris">×</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-outline btn-sm" id="schedule-add">+ Tambah Acara</button>

            <?php if (!empty($event['slug'])): ?>
            <div class="info-box full" style="margin-top:1.25rem">
                <strong>Link Undangan Personal:</strong><br>
                <code id="personal-link-template"><?= e(getPersonalizedInvitationUrl($event['slug'], 'Nama Tamu')) ?></code>
                <p style="margin-top:.5rem;font-size:.85rem">Ganti <em>Nama Tamu</em> saat menyebarkan ke setiap orang. Contoh: <code>?kepada=Budi Santoso</code></p>
            </div>
            <?php endif; ?>
            </div>

        <div class="tab-panel <?= $activeTab === 'halaman' ? 'active' : '' ?>" data-tab="halaman">
            <h3 class="subsection-title">Pengaturan Gulir</h3>
            <div class="form-grid">
                <label class="full checkbox-label">
                    <input type="checkbox" name="auto_scroll" value="1" <?= !empty($event['auto_scroll']) ? 'checked' : '' ?>>
                    Tampilkan tombol gulir otomatis (tamu tekan sendiri untuk mulai/berhenti)
                </label>
                <label>
                    Lama Tiap Halaman (detik)
                    <input type="number" name="scroll_interval" value="<?= (int) ($event['scroll_interval'] ?? 5) ?>" min="2" max="60">
                    <small>Berapa lama undangan berhenti di setiap halaman saat gulir otomatis</small>
                </label>
                <label>
                    Kecepatan Transisi Gulir (ms)
                    <input type="number" name="scroll_speed" value="<?= (int) ($event['scroll_speed'] ?? 800) ?>" min="300" max="3000" step="100">
                    <small>Angka kecil = gulir cepat, angka besar = gulir halus/lambat</small>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="scroll_snap" value="1" <?= !isset($event['scroll_snap']) || !empty($event['scroll_snap']) ? 'checked' : '' ?>>
                    Snap per layar (geser manual berhenti di setiap bagian)
                </label>
            </div>

            <h3 class="subsection-title">Susunan & Isi Halaman</h3>
            <p class="field-hint full">Atur urutan halaman, aktif/nonaktif, judul kustom, dan tambah halaman bebas. Isi halaman bawaan diisi di tab <strong>Konten Teks</strong>.</p>
            <div id="pages-editor" class="pages-editor">
                <?php foreach ($invPages as $idx => $page):
                    $isCustom = ($page['type'] ?? '') === 'custom';
                    $meta     = $builtinMeta[$page['key']] ?? null;
                    $label    = $isCustom ? 'Halaman Kustom' : ($meta['label'] ?? $page['key']);
                    $hint     = $isCustom ? 'Isi judul & teks di bawah' : ($meta['hint'] ?? '');
                ?>
                <div class="page-row <?= $isCustom ? 'page-row-custom' : 'page-row-builtin' ?>" data-type="<?= $isCustom ? 'custom' : 'builtin' ?>">
                    <div class="page-row-tools">
                        <span class="page-order-num"><?= $idx + 1 ?></span>
                        <button type="button" class="btn btn-sm btn-outline page-up" title="Naik">↑</button>
                        <button type="button" class="btn btn-sm btn-outline page-down" title="Turun">↓</button>
                    </div>
                    <label class="page-enabled checkbox-label">
                        <input type="checkbox" name="pages[<?= $idx ?>][enabled]" value="1" <?= !empty($page['enabled']) ? 'checked' : '' ?>>
                        Aktif
                    </label>
                    <div class="page-fields">
                        <input type="hidden" name="pages[<?= $idx ?>][key]" value="<?= e($page['key']) ?>" class="page-key-input">
                        <input type="hidden" name="pages[<?= $idx ?>][type]" value="<?= e($page['type']) ?>" class="page-type-input">
                        <span class="page-type-badge"><?= e($label) ?></span>
                        <?php if ($hint): ?><small class="page-hint"><?= e($hint) ?></small><?php endif; ?>
                        <input type="text" name="pages[<?= $idx ?>][title]" value="<?= e($page['title']) ?>" placeholder="Judul halaman (opsional)" class="page-title-input">
                        <textarea name="pages[<?= $idx ?>][body]" rows="<?= $isCustom ? 4 : 2 ?>" placeholder="<?= $isCustom ? 'Isi teks halaman tambahan...' : 'Catatan khusus (opsional, halaman bawaan pakai isi tab Konten)' ?>" class="page-body-input <?= $isCustom ? '' : 'page-body-builtin' ?>"><?= e($page['body']) ?></textarea>
                    </div>
                    <?php if ($isCustom): ?>
                    <button type="button" class="btn btn-sm btn-danger page-remove" title="Hapus halaman">×</button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-outline btn-sm" id="pages-add">+ Tambah Halaman Kustom</button>
        </div>

        <div class="tab-panel <?= $activeTab === 'visual' ? 'active' : '' ?>" data-tab="visual">
            <div class="form-grid">
                <div class="full">
                    <span class="field-label">Pilih Tema Undangan</span>
                    <div class="theme-picker" id="theme-picker">
                        <?php foreach ($presets as $key => $preset): ?>
                        <label class="theme-card <?= $currentPreset === $key ? 'selected' : '' ?>"
                               data-primary="<?= e($preset['primary']) ?>"
                               data-accent="<?= e($preset['accent']) ?>">
                            <input type="radio" name="theme_preset" value="<?= e($key) ?>"
                                   <?= $currentPreset === $key ? 'checked' : '' ?>>
                            <span class="theme-swatch" style="background: linear-gradient(135deg, <?= e($preset['primary']) ?> 50%, <?= e($preset['accent']) ?> 50%);"></span>
                            <span class="theme-name"><?= e($preset['name']) ?></span>
                            <small><?= e($preset['description']) ?></small>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div id="custom-colors" class="form-grid full" style="<?= $currentPreset !== 'custom' ? 'display:none' : '' ?>">
                    <label>
                        Warna Primer (Latar)
                        <div class="color-input">
                            <input type="color" name="color_primary" id="color_primary" value="<?= e($event['color_primary']) ?>">
                            <input type="text" value="<?= e($event['color_primary']) ?>" readonly class="color-hex">
                        </div>
                    </label>
                    <label>
                        Warna Aksen
                        <div class="color-input">
                            <input type="color" name="color_accent" id="color_accent" value="<?= e($event['color_accent']) ?>">
                            <input type="text" value="<?= e($event['color_accent']) ?>" readonly class="color-hex">
                        </div>
                    </label>
                </div>
                <label class="full">
                    Logo Pesantren (.png/.jpg, max 1MB)
                    <?php if ($event['logo_pesantren']): ?>
                        <div class="logo-preview"><img src="<?= e($event['logo_pesantren']) ?>" alt="Logo Pesantren"></div>
                    <?php endif; ?>
                    <input type="file" name="logo_pesantren" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                </label>
                <label class="full">
                    Logo Haflah (.png/.jpg, max 1MB)
                    <?php if ($event['logo_haflah']): ?>
                        <div class="logo-preview"><img src="<?= e($event['logo_haflah']) ?>" alt="Logo Haflah"></div>
                    <?php endif; ?>
                    <input type="file" name="logo_haflah" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                </label>
            </div>

            <h3 class="subsection-title">Gambar Ornamen (opsional, max 2MB)</h3>
            <p class="field-hint">Unggah ornamen PNG transparan. Kosongkan untuk ornamen bawaan tema.</p>
            <div class="form-grid">
                <label>
                    Ornamen Atas (header / splash)
                    <?php if (!empty($event['ornament_top'])): ?>
                        <div class="logo-preview ornament-preview"><img src="<?= e($event['ornament_top']) ?>" alt="Ornamen Atas"></div>
                    <?php endif; ?>
                    <input type="file" name="ornament_top" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                </label>
                <label>
                    Ornamen Pembatas (antar bagian)
                    <?php if (!empty($event['ornament_divider'])): ?>
                        <div class="logo-preview ornament-preview"><img src="<?= e($event['ornament_divider']) ?>" alt="Ornamen Pembatas"></div>
                    <?php endif; ?>
                    <input type="file" name="ornament_divider" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                </label>
                <label>
                    Ornamen Bawah (footer)
                    <?php if (!empty($event['ornament_bottom'])): ?>
                        <div class="logo-preview ornament-preview"><img src="<?= e($event['ornament_bottom']) ?>" alt="Ornamen Bawah"></div>
                    <?php endif; ?>
                    <input type="file" name="ornament_bottom" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                </label>
                <label class="full">
                    Gambar Latar Belakang (opsional)
                    <?php if (!empty($event['bg_image'])): ?>
                        <div class="logo-preview bg-preview"><img src="<?= e($event['bg_image']) ?>" alt="Background"></div>
                    <?php endif; ?>
                    <input type="file" name="bg_image" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                    <small>Disarankan gambar portrait untuk tampilan ponsel</small>
                </label>
            </div>

            <h3 class="subsection-title">Font Undangan</h3>
            <div class="form-grid">
                <label class="full">
                    Pilihan Font
                    <select name="font_preset" id="font_preset">
                        <?php foreach ($fontPresets as $fkey => $fp): ?>
                        <option value="<?= e($fkey) ?>" <?= $currentFont === $fkey ? 'selected' : '' ?>>
                            <?= e($fp['name']) ?> — <?= e($fp['description']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div id="custom-font-fields" class="form-grid" style="<?= $currentFont !== 'custom' ? 'display:none' : '' ?>">
                <label>
                    Font Judul / Arab (.woff2, .woff, .ttf — max 3MB)
                    <?php if (!empty($event['font_custom_title'])): ?>
                        <small class="file-current">Aktif: <?= e(basename($event['font_custom_title'])) ?></small>
                    <?php endif; ?>
                    <input type="file" name="font_custom_title" accept=".woff2,.woff,.ttf">
                </label>
                <label>
                    Font Isi / Latin (.woff2, .woff, .ttf — max 3MB)
                    <?php if (!empty($event['font_custom_body'])): ?>
                        <small class="file-current">Aktif: <?= e(basename($event['font_custom_body'])) ?></small>
                    <?php endif; ?>
                    <input type="file" name="font_custom_body" accept=".woff2,.woff,.ttf">
                </label>
            </div>

            <h3 class="subsection-title">Animasi Ornamen</h3>
            <div class="form-grid">
                <label class="full checkbox-label">
                    <input type="checkbox" name="animated_ornaments" value="1" <?= !empty($event['animated_ornaments']) ? 'checked' : '' ?>>
                    Aktifkan ornamen bergerak (teks & halaman tetap diam)
                </label>
                <label class="full">
                    Model Gerak
                    <select name="ornament_animation" id="ornament_animation">
                        <?php foreach ($animModes as $akey => $am): ?>
                        <option value="<?= e($akey) ?>" <?= $currentAnim === $akey ? 'selected' : '' ?>>
                            <?= e($am['name']) ?> — <?= e($am['description']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <h3 class="subsection-title">Animasi Per Tampilan</h3>
            <p class="field-hint full">Hanya gambar ornamen (atas, pembatas, bawah) yang bergerak. Konten teks dan halaman undangan tetap diam saat gulir otomatis.</p>
            <div class="section-anim-grid full">
                <div class="section-anim-head">
                    <span>Bagian</span>
                    <span>Aktif</span>
                    <span>Model Gerak</span>
                </div>
                <?php foreach ($invPages as $skey => $page):
                    if (($page['type'] ?? '') === 'custom' && empty($page['enabled'])) {
                        continue;
                    }
                    $cfg = $sectionAnimCfg[$page['key']] ?? null;
                    $on = $cfg === null || !empty($cfg['on']);
                    $mode = $cfg['mode'] ?? $currentAnim;
                    $slabel = ($page['type'] ?? '') === 'custom'
                        ? ($page['title'] ?: 'Halaman Kustom')
                        : ($builtinMeta[$page['key']]['label'] ?? $page['key']);
                ?>
                <div class="section-anim-row">
                    <span class="section-anim-label"><?= e($slabel) ?></span>
                    <label class="section-anim-check">
                        <input type="checkbox" name="section_anim[<?= e($page['key']) ?>][on]" value="1" <?= $on ? 'checked' : '' ?>>
                    </label>
                    <select name="section_anim[<?= e($page['key']) ?>][mode]">
                        <?php foreach ($animModes as $akey => $am): ?>
                        <option value="<?= e($akey) ?>" <?= $mode === $akey ? 'selected' : '' ?>><?= e($am['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-panel <?= $activeTab === 'audio' ? 'active' : '' ?>" data-tab="audio">
            <div class="form-grid">
                <label class="full">
                    Mode Audio
                    <select name="audio_mode" id="audio_mode">
                        <option value="synth" <?= $event['audio_mode'] === 'synth' ? 'selected' : '' ?>>Generator Sholawat (Web Audio API)</option>
                        <option value="url" <?= $event['audio_mode'] === 'url' ? 'selected' : '' ?>>Tautan MP3 Eksternal</option>
                    </select>
                </label>
                <label class="full" id="audio_url_field" style="<?= $event['audio_mode'] !== 'url' ? 'display:none' : '' ?>">
                    URL File MP3
                    <input type="url" name="audio_url" value="<?= e($event['audio_url']) ?>" placeholder="https://example.com/lagu.mp3">
                    <small>URL harus berakhiran .mp3 dan server mengizinkan CORS</small>
                </label>
                <div class="info-box full">
                    <strong>Kebijakan Autoplay Browser:</strong> Tamu wajib menekan tombol "Buka Undangan" di layar awal sebelum musik diputar.
                </div>
            </div>
        </div>

        <div class="tab-panel <?= $activeTab === 'pengaturan' ? 'active' : '' ?>" data-tab="pengaturan">
            <div class="form-grid">
                <label>
                    Kapasitas Kursi Aula
                    <input type="number" name="seat_capacity" value="<?= (int) $event['seat_capacity'] ?>" min="1">
                </label>
                <label>
                    Status Undangan
                    <select name="status">
                        <option value="aktif" <?= $event['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="arsip" <?= $event['status'] === 'arsip' ? 'selected' : '' ?>>Arsip</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
            <a href="<?= app_url('admin/events/index.php') ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
    <?php
}

function saveEvent(array $data, ?int $id = null, array $files = []): array
{
    $db = getDB();

    $presets = getThemePresets();
    if ($data['theme_preset'] !== 'custom' && isset($presets[$data['theme_preset']])) {
        $data['color_primary'] = $presets[$data['theme_preset']]['primary'];
        $data['color_accent']  = $presets[$data['theme_preset']]['accent'];
    }

    if ($id) {
        $existing = getEventById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['Undangan tidak ditemukan.']];
        }
        $logoPesantren     = $existing['logo_pesantren'];
        $logoHaflah        = $existing['logo_haflah'];
        $ornamentTop       = $existing['ornament_top'] ?? null;
        $ornamentDivider   = $existing['ornament_divider'] ?? null;
        $ornamentBottom    = $existing['ornament_bottom'] ?? null;
        $bgImage           = $existing['bg_image'] ?? null;
        $fontCustomTitle   = $existing['font_custom_title'] ?? null;
        $fontCustomBody    = $existing['font_custom_body'] ?? null;
    } else {
        $logoPesantren = $logoHaflah = $ornamentTop = $ornamentDivider = $ornamentBottom = $bgImage = null;
        $fontCustomTitle = $fontCustomBody = null;
    }

    $uploadMap = [
        'logo_pesantren'    => ['var' => 'logoPesantren',   'prefix' => 'pesantren',   'fn' => 'saveLogoUpload'],
        'logo_haflah'       => ['var' => 'logoHaflah',      'prefix' => 'haflah',      'fn' => 'saveLogoUpload'],
        'ornament_top'      => ['var' => 'ornamentTop',     'prefix' => 'orn_top',     'fn' => 'saveOrnamentUpload'],
        'ornament_divider'  => ['var' => 'ornamentDivider', 'prefix' => 'orn_div',     'fn' => 'saveOrnamentUpload'],
        'ornament_bottom'   => ['var' => 'ornamentBottom',  'prefix' => 'orn_bot',     'fn' => 'saveOrnamentUpload'],
        'bg_image'          => ['var' => 'bgImage',         'prefix' => 'bg',          'fn' => 'saveOrnamentUpload'],
    ];

    foreach ($uploadMap as $field => $cfg) {
        if (empty($files[$field]['name'])) {
            continue;
        }
        $validateFn = str_starts_with($field, 'logo') ? 'validateLogoUpload' : 'validateImageUpload';
        $maxSize    = str_starts_with($field, 'logo') ? MAX_LOGO_SIZE : MAX_ORNAMENT_SIZE;
        $err = str_starts_with($field, 'logo')
            ? validateLogoUpload($files[$field])
            : validateImageUpload($files[$field], $maxSize);
        if ($err) {
            return ['success' => false, 'errors' => [$err]];
        }
        $saved = $cfg['fn']($files[$field], $cfg['prefix']);
        if ($saved) {
            ${$cfg['var']} = $saved;
        }
    }

    foreach (['font_custom_title' => 'fontCustomTitle', 'font_custom_body' => 'fontCustomBody'] as $field => $var) {
        if (empty($files[$field]['name'])) {
            continue;
        }
        $err = validateFontUpload($files[$field]);
        if ($err) {
            return ['success' => false, 'errors' => [$err]];
        }
        $saved = saveFontUpload($files[$field], str_replace('font_custom_', 'font_', $field));
        if (!$saved) {
            return ['success' => false, 'errors' => ['Gagal menyimpan file font.']];
        }
        ${$var} = $saved;
    }

    $slugCheck = $db->prepare('SELECT id FROM events WHERE slug = ? AND id != ?');
    $slugCheck->execute([$data['slug'], $id ?? 0]);
    if ($slugCheck->fetch()) {
        return ['success' => false, 'errors' => ['Slug URL sudah digunakan.']];
    }

    $fields = 'slug=?, title=?, pesantren_name=?, theme_statement=?, invitation_greeting=?, mukaddimah=?, quran_quote=?,
            date_masehi=?, date_hijriah=?, event_time=?, countdown_target=?,
            location_name=?, location_address=?, maps_url=?,
            speaker_name=?, speaker_origin=?,
            dresscode_pria=?, dresscode_wanita=?, special_rules=?, event_schedule=?,
            color_primary=?, color_accent=?, theme_preset=?, font_preset=?,
            font_custom_title=?, font_custom_body=?,
            logo_pesantren=?, logo_haflah=?,
            ornament_top=?, ornament_divider=?, ornament_bottom=?, bg_image=?,
            auto_scroll=?, animated_ornaments=?, ornament_animation=?,
            section_animations=?, scroll_interval=?, scroll_speed=?, scroll_snap=?, invitation_pages=?,
            audio_mode=?, audio_url=?, seat_capacity=?, status=?';

    $params = [
        $data['slug'], $data['title'], $data['pesantren_name'], $data['theme_statement'],
        $data['invitation_greeting'], $data['mukaddimah'], $data['quran_quote'], $data['date_masehi'], $data['date_hijriah'],
        $data['event_time'], $data['countdown_target'], $data['location_name'],
        $data['location_address'], $data['maps_url'], $data['speaker_name'], $data['speaker_origin'],
        $data['dresscode_pria'], $data['dresscode_wanita'], $data['special_rules'], $data['event_schedule'] ?: null,
        $data['color_primary'], $data['color_accent'], $data['theme_preset'], $data['font_preset'],
        $fontCustomTitle, $fontCustomBody,
        $logoPesantren, $logoHaflah,
        $ornamentTop, $ornamentDivider, $ornamentBottom, $bgImage,
        $data['auto_scroll'], $data['animated_ornaments'], $data['ornament_animation'],
        $data['section_animations'] ?: null, $data['scroll_interval'], $data['scroll_speed'], $data['scroll_snap'],
        $data['invitation_pages'] ?: null,
        $data['audio_mode'], $data['audio_url'] ?: null, $data['seat_capacity'], $data['status'],
    ];

    if ($id) {
        $db->prepare("UPDATE events SET {$fields} WHERE id=?")->execute([...$params, $id]);
        return ['success' => true, 'id' => $id];
    }

    $placeholders = implode(',', array_fill(0, count($params), '?'));
    $sql = 'INSERT INTO events (
        slug, title, pesantren_name, theme_statement, invitation_greeting, mukaddimah, quran_quote,
        date_masehi, date_hijriah, event_time, countdown_target,
        location_name, location_address, maps_url,
        speaker_name, speaker_origin,
        dresscode_pria, dresscode_wanita, special_rules, event_schedule,
        color_primary, color_accent, theme_preset, font_preset,
        font_custom_title, font_custom_body,
        logo_pesantren, logo_haflah,
        ornament_top, ornament_divider, ornament_bottom, bg_image,
        auto_scroll, animated_ornaments, ornament_animation,
        section_animations, scroll_interval, scroll_speed, scroll_snap, invitation_pages,
        audio_mode, audio_url, seat_capacity, status
    ) VALUES (' . $placeholders . ')';
    $db->prepare($sql)->execute($params);
    return ['success' => true, 'id' => (int) $db->lastInsertId()];
}
