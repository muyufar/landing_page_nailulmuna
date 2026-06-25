<?php

function getThemePresets(): array
{
    return [
        'hijau_emas' => [
            'name'        => 'Hijau Emas',
            'description' => 'Klasik pesantren — hijau tua & emas',
            'primary'     => '#064E3B',
            'accent'      => '#D4AF37',
        ],
        'biru_perak' => [
            'name'        => 'Biru Perak',
            'description' => 'Elegan formal — biru navy & perak',
            'primary'     => '#1B3A5F',
            'accent'      => '#B8C5D6',
        ],
        'ungu_cream' => [
            'name'        => 'Ungu Cream',
            'description' => 'Lembut & mewah — ungu & krem',
            'primary'     => '#4A1942',
            'accent'      => '#F5E6D3',
        ],
        'maroon_emas' => [
            'name'        => 'Maroon Emas',
            'description' => 'Agung & khidmat — merah maroon & emas',
            'primary'     => '#5C1A1A',
            'accent'      => '#D4AF37',
        ],
        'teal_terracotta' => [
            'name'        => 'Teal Terracotta',
            'description' => 'Modern segar — teal & terracotta',
            'primary'     => '#0D4F4F',
            'accent'      => '#E07A5F',
        ],
        'hitam_emas' => [
            'name'        => 'Hitam Emas',
            'description' => 'Minimalis mewah — hitam & emas',
            'primary'     => '#1A1A1A',
            'accent'      => '#C9A227',
        ],
        'custom' => [
            'name'        => 'Kustom',
            'description' => 'Atur warna primer & aksen sendiri',
            'primary'     => '#064E3B',
            'accent'      => '#D4AF37',
        ],
    ];
}

function getFontPresets(): array
{
    return [
        'klasik' => [
            'name'       => 'Klasik Pesantren',
            'description'=> 'Amiri + Cormorant — elegan tradisional',
            'font_title' => 'Amiri',
            'font_body'  => 'Cormorant Garamond',
        ],
        'arabic' => [
            'name'       => 'Arabic Script',
            'description'=> 'Scheherazade + Lateef — kaligrafi kuat',
            'font_title' => 'Scheherazade New',
            'font_body'  => 'Lateef',
        ],
        'modern' => [
            'name'       => 'Modern Naskh',
            'description'=> 'Noto Naskh + Lora — bersih & terbaca',
            'font_title' => 'Noto Naskh Arabic',
            'font_body'  => 'Lora',
        ],
        'elegant' => [
            'name'       => 'Elegan Kufi',
            'description'=> 'Reem Kufi + EB Garamond',
            'font_title' => 'Reem Kufi',
            'font_body'  => 'EB Garamond',
        ],
        'khidmah' => [
            'name'       => 'Khidmah',
            'description'=> 'Lateef + Merriweather — hangat',
            'font_title' => 'Lateef',
            'font_body'  => 'Merriweather',
        ],
        'minimal' => [
            'name'       => 'Minimal',
            'description'=> 'Amiri + Source Sans — simpel modern',
            'font_title' => 'Amiri',
            'font_body'  => 'Source Sans 3',
        ],
        'custom' => [
            'name'       => 'Font Kustom (Upload)',
            'description'=> 'Unggah file font .woff2 / .woff / .ttf',
            'font_title' => 'InvFontTitle',
            'font_body'  => 'InvFontBody',
        ],
    ];
}

function getAnimationModes(): array
{
    return [
        'none'      => ['name' => 'Tidak Ada', 'description' => 'Statis tanpa animasi'],
        'melayang'  => ['name' => 'Melayang',  'description' => 'Naik-turun lembut seperti mengambang'],
        'goyang'    => ['name' => 'Goyang',    'description' => 'Ayunan kiri-kanan halus'],
        'denyut'    => ['name' => 'Denyut',    'description' => 'Membesar-mengecil berdenyut'],
        'berputar'  => ['name' => 'Berputar',  'description' => 'Putaran lambat 360°'],
        'gelombang' => ['name' => 'Gelombang', 'description' => 'Gerak melengkung seperti ombak'],
        'hujan'     => ['name' => 'Hujan Bintang', 'description' => 'Partikel jatuh dari atas'],
        'kilau'     => ['name' => 'Kilau',     'description' => 'Partikel berkelap-kelip'],
    ];
}

function getValidThemePreset(string $preset): string
{
    return isset(getThemePresets()[$preset]) ? $preset : 'hijau_emas';
}

function getValidFontPreset(string $preset): string
{
    return isset(getFontPresets()[$preset]) ? $preset : 'klasik';
}

function getValidAnimationMode(string $mode): string
{
    $modes = getAnimationModes();
    return isset($modes[$mode]) ? $mode : 'melayang';
}

function resolveEventColors(array $event): array
{
    $preset = getValidThemePreset($event['theme_preset'] ?? 'hijau_emas');
    $themes = getThemePresets();

    if ($preset === 'custom') {
        return [
            'primary' => $event['color_primary'] ?? '#064E3B',
            'accent'  => $event['color_accent'] ?? '#D4AF37',
            'preset'  => $preset,
        ];
    }

    $theme = $themes[$preset];
    return [
        'primary' => $theme['primary'],
        'accent'  => $theme['accent'],
        'preset'  => $preset,
    ];
}

function resolveEventFonts(array $event): array
{
    $fontPreset = getValidFontPreset($event['font_preset'] ?? 'klasik');
    $presets    = getFontPresets();
    $result     = [
        'preset'      => $fontPreset,
        'font_title'  => 'Amiri',
        'font_body'   => 'Cormorant Garamond',
        'google_url'  => null,
        'custom_css'  => '',
    ];

    if ($fontPreset === 'custom') {
        $titleUrl = $event['font_custom_title'] ?? '';
        $bodyUrl  = $event['font_custom_body'] ?? '';
        $css      = '';

        if ($titleUrl) {
            $fmt = fontFormatFromUrl($titleUrl);
            $css .= "@font-face{font-family:'InvFontTitle';src:url('{$titleUrl}') format('{$fmt}');font-weight:400 700;font-display:swap;}";
            $result['font_title'] = 'InvFontTitle';
        }
        if ($bodyUrl) {
            $fmt = fontFormatFromUrl($bodyUrl);
            $css .= "@font-face{font-family:'InvFontBody';src:url('{$bodyUrl}') format('{$fmt}');font-weight:400 700;font-display:swap;}";
            $result['font_body'] = 'InvFontBody';
        }
        if (!$titleUrl && !$bodyUrl) {
            $fontPreset = 'klasik';
        } else {
            $result['custom_css'] = $css;
            return $result;
        }
    }

    $fp = $presets[$fontPreset];
    $result['font_title'] = $fp['font_title'];
    $result['font_body']  = $fp['font_body'];
    $result['google_url'] = buildGoogleFontUrl($fp['font_title'], $fp['font_body']);
    $result['preset']     = $fontPreset;

    return $result;
}

function fontFormatFromUrl(string $url): string
{
    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    return match ($ext) {
        'woff2' => 'woff2',
        'woff'  => 'woff',
        'ttf'   => 'truetype',
        default => 'woff2',
    };
}

function buildGoogleFontUrl(string $title, string $body): string
{
    $t = str_replace(' ', '+', $title);
    $b = str_replace(' ', '+', $body);
    return "https://fonts.googleapis.com/css2?family={$t}:wght@400;700&family={$b}:wght@400;600;700&display=swap";
}

function getThemeFontUrl(array $fonts): string
{
    if (!empty($fonts['google_url'])) {
        return $fonts['google_url'];
    }
    $title = str_replace(' ', '+', $fonts['font_title']);
    $body  = str_replace(' ', '+', $fonts['font_body']);
    return "https://fonts.googleapis.com/css2?family={$title}:wght@400;700&family={$body}:wght@400;600&display=swap";
}
