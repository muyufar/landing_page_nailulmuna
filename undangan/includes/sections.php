<?php

require_once __DIR__ . '/themes.php';

function getBuiltinPageMeta(): array
{
    return [
        'header'    => ['label' => 'Cover / Pembuka', 'hint' => 'Judul, pesantren, logo — tab Konten Teks'],
        'quote'     => ['label' => 'Kutipan Al-Qur\'an', 'hint' => 'Kutipan Al-Qur\'an — tab Konten Teks'],
        'mukaddimah'=> ['label' => 'Mukaddimah', 'hint' => 'Mukaddimah — tab Konten Teks'],
        'countdown' => ['label' => 'Hitung Mundur', 'hint' => 'Target hitung mundur — tab Konten Teks'],
        'detail'    => ['label' => 'Waktu & Tempat', 'hint' => 'Tanggal, waktu, lokasi — tab Konten Teks'],
        'schedule'  => ['label' => 'Susunan Acara', 'hint' => 'Susunan acara — tab Konten Teks'],
        'speaker'   => ['label' => 'Penceramah', 'hint' => 'Nama penceramah — tab Konten Teks'],
        'rules'     => ['label' => 'Etika & Tata Tertib', 'hint' => 'Dress code & tata tertib — tab Konten Teks'],
        'rsvp'      => ['label' => 'Konfirmasi RSVP', 'hint' => 'Form konfirmasi kehadiran'],
        'guestbook' => ['label' => 'Doa & Ucapan', 'hint' => 'Ucapan tamu yang sudah mengisi RSVP'],
        'footer'    => ['label' => 'Penutup', 'hint' => 'Ornamen bawah & nama pesantren'],
    ];
}

function getInvitationSections(): array
{
    $meta = getBuiltinPageMeta();
    unset($meta['footer']);
    return array_map(fn ($m) => $m['label'], $meta);
}

function renderOrnament(?string $url, string $type, string $alt = 'Ornamen', bool $animated = true): void
{
    $animClass = $animated ? ' ornament-moving' : '';
    if ($url) {
        echo '<img src="' . e($url) . '" alt="' . e($alt) . '" class="ornament-img ornament-' . e($type) . $animClass . '" loading="lazy">';
    } else {
        echo '<div class="ornament-default ornament-' . e($type) . '-default' . $animClass . '" aria-hidden="true"></div>';
    }
}

function renderDivider(?string $url, bool $animated = true): void
{
    $animClass = $animated ? ' ornament-moving' : '';
    echo '<div class="section-divider">';
    if ($url) {
        echo '<img src="' . e($url) . '" alt="" class="ornament-img ornament-divider' . $animClass . '" loading="lazy">';
    } else {
        echo '<div class="ornament-default ornament-divider-default' . $animClass . '" aria-hidden="true"></div>';
    }
    echo '</div>';
}

function parseSectionAnimations(?string $json): array
{
    if (!$json) {
        return [];
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function buildSectionAnimationsFromPost(array $post, string $defaultMode): string
{
    $input  = $post['section_anim'] ?? [];
    $result = [];
    $keys   = [];

    if (!empty($post['pages']) && is_array($post['pages'])) {
        foreach ($post['pages'] as $row) {
            $key = trim($row['key'] ?? '');
            if ($key !== '') {
                $keys[] = $key;
            }
        }
    }
    if (empty($keys)) {
        $keys = array_keys(getInvitationSections());
    }

    foreach ($keys as $key) {
        $row = $input[$key] ?? [];
        $result[$key] = [
            'on'   => !empty($row['on']),
            'mode' => getValidAnimationMode($row['mode'] ?? $defaultMode),
        ];
    }

    return json_encode($result, JSON_UNESCAPED_UNICODE);
}

function resolveSectionAnim(string $sectionKey, array $configs, string $defaultMode, bool $globalOn): string
{
    if (!$globalOn) {
        return 'none';
    }
    $cfg = $configs[$sectionKey] ?? null;
    if ($cfg !== null && empty($cfg['on'])) {
        return 'none';
    }
    $mode = $cfg['mode'] ?? $defaultMode;
    return $mode === 'none' ? 'none' : getValidAnimationMode($mode);
}

function invScreenAttrs(string $sectionKey, array $configs, string $defaultMode, bool $globalOn): string
{
    $mode = resolveSectionAnim($sectionKey, $configs, $defaultMode, $globalOn);
    return 'data-section="' . e($sectionKey) . '" data-anim="' . e($mode) . '"';
}

function renderScreenHint(bool $isLast = false): void
{
    if ($isLast) {
        return;
    }
    echo '<div class="screen-hint" aria-hidden="true"><span>Geser ke bawah</span><span class="hint-arrow">↓</span></div>';
}
