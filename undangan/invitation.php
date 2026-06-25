<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/sections.php';
require_once __DIR__ . '/includes/invitation_pages.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    http_response_code(404);
    echo 'Undangan tidak ditemukan.';
    exit;
}

$event = getEventBySlug($slug);
if (!$event) {
    http_response_code(404);
    echo 'Undangan tidak ditemukan atau sudah diarsipkan.';
    exit;
}

$greetings = getDB()->prepare('
    SELECT guest_name, greeting_note, status, submitted_at
    FROM guestbook_rsvp
    WHERE event_id = ? AND greeting_note IS NOT NULL AND greeting_note != ""
    ORDER BY submitted_at DESC
    LIMIT 50
');
$greetings->execute([$event['id']]);
$greetingList = $greetings->fetchAll();

$colors = resolveEventColors($event);
$preset = $colors['preset'];
$fontData = resolveEventFonts($event);
$defaultAnim = getValidAnimationMode($event['ornament_animation'] ?? 'melayang');
$sectionAnims = parseSectionAnimations($event['section_animations'] ?? '');
$inviteeName   = parseInviteeName();
$scheduleItems = parseEventSchedule($event['event_schedule'] ?? '');
$animateOrns   = !isset($event['animated_ornaments']) || !empty($event['animated_ornaments']);
$autoScroll    = !isset($event['auto_scroll']) || !empty($event['auto_scroll']);
$scrollInterval = max(2, (int) ($event['scroll_interval'] ?? 5));
$scrollSpeed    = max(300, min(3000, (int) ($event['scroll_speed'] ?? 800)));
$scrollSnap    = !isset($event['scroll_snap']) || !empty($event['scroll_snap']);
$greetingLabel = $event['invitation_greeting'] ?? 'Kepada Yth. Bapak/Ibu/Saudara/i';
$invPages      = resolveInvitationPages($event['invitation_pages'] ?? '');

$renderCtx = [
    'event'         => $event,
    'inviteeName'   => $inviteeName,
    'greetingLabel' => $greetingLabel,
    'scheduleItems' => $scheduleItems,
    'greetingList'  => $greetingList,
    'animateOrns'   => $animateOrns,
    'sectionAnims'  => $sectionAnims,
    'defaultAnim'   => $defaultAnim,
];

$bodyClasses = ['theme-' . $preset];
if ($scrollSnap) {
    $bodyClasses[] = 'inv-snap-on';
}
if ($animateOrns) {
    $bodyClasses[] = 'inv-motion-on';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="theme-color" content="<?= e($colors['primary']) ?>">
    <title><?= e($event['title']) ?> — Undangan Digital</title>
    <meta name="description" content="Undangan digital <?= e($event['title']) ?> — <?= e($event['pesantren_name']) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php if (!empty($fontData['google_url'])): ?>
    <link href="<?= e($fontData['google_url']) ?>" rel="stylesheet">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= app_url('assets/css/invitation.css') ?>">
    <link rel="stylesheet" href="<?= app_url('assets/css/themes.css') ?>">
    <link rel="stylesheet" href="<?= app_url('assets/css/animations.css') ?>">
    <style>
        <?php if (!empty($fontData['custom_css'])): ?>
        <?= $fontData['custom_css'] ?>
        <?php endif; ?>
        :root {
            --color-primary: <?= e($colors['primary']) ?>;
            --color-accent: <?= e($colors['accent']) ?>;
            --font-title: '<?= e($fontData['font_title']) ?>', serif;
            --font-body: '<?= e($fontData['font_body']) ?>', serif;
        }
    </style>
</head>
<body class="<?= e(implode(' ', $bodyClasses)) ?>" data-global-anim="<?= e($animateOrns ? $defaultAnim : 'none') ?>">
    <?php /* partikel mengambang dinonaktifkan — hanya ornamen gambar yang bergerak */ ?>
    <?php if (!empty($event['bg_image'])): ?>
    <div class="inv-bg" style="background-image: url('<?= e($event['bg_image']) ?>')"></div>
  <?php endif; ?>

    <!-- Splash Screen -->
    <div id="splash" class="splash">
        <div class="splash-inner">
            <?php renderOrnament($event['ornament_top'] ?? null, 'top', 'Ornamen', $animateOrns); ?>

            <?php if ($inviteeName): ?>
            <div class="splash-invitee reveal-item">
                <p class="invitee-label"><?= e($greetingLabel) ?></p>
                <h2 class="invitee-name"><?= e($inviteeName) ?></h2>
            </div>
            <?php endif; ?>

            <?php if ($event['logo_pesantren']): ?>
                <img src="<?= e($event['logo_pesantren']) ?>" alt="Logo" class="splash-logo">
            <?php endif; ?>
            <p class="splash-pesantren"><?= e($event['pesantren_name']) ?></p>
            <h1 class="splash-title"><?= e($event['title']) ?></h1>
            <?php if ($event['theme_statement']): ?>
                <p class="splash-theme"><?= e($event['theme_statement']) ?></p>
            <?php endif; ?>
            <button id="btn-open" class="btn-open" type="button">Buka Undangan</button>
        </div>
    </div>

    <!-- Main Invitation -->
    <div id="invitation" class="invitation hidden">
        <div id="inv-scroll" class="inv-scroll">

        <?php renderInvitationPages($invPages, $renderCtx); ?>

        </div><!-- #inv-scroll -->
    </div>

    <button id="btn-mute" class="btn-mute hidden" type="button" title="Toggle Musik" aria-label="Toggle musik">🔊</button>
    <button id="btn-autoscroll" class="btn-autoscroll hidden is-off" type="button" title="Ketuk untuk memulai gulir otomatis" aria-label="Mulai gulir otomatis">
        <span class="btn-autoscroll-icon">▶</span>
        <span class="btn-autoscroll-label">Gulir</span>
    </button>

    <script>
        window.INVITATION_CONFIG = {
            audioMode: <?= json_encode($event['audio_mode']) ?>,
            audioUrl: <?= json_encode($event['audio_url'] ?? '') ?>,
            autoScroll: <?= $autoScroll ? 'true' : 'false' ?>,
            scrollInterval: <?= (int) $scrollInterval ?>,
            scrollSpeed: <?= (int) $scrollSpeed ?>,
            scrollSnap: <?= $scrollSnap ? 'true' : 'false' ?>,
            motionEnabled: <?= $animateOrns ? 'true' : 'false' ?>,
            inviteeName: <?= json_encode($inviteeName) ?>,
            appBase: <?= json_encode(APP_BASE) ?>
        };
    </script>
    <script src="<?= app_url('assets/js/audio-synth.js') ?>"></script>
    <script src="<?= app_url('assets/js/invitation-motion.js') ?>"></script>
    <script src="<?= app_url('assets/js/invitation.js') ?>"></script>
</body>
</html>
