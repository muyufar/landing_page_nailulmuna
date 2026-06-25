<?php

require_once __DIR__ . '/sections.php';

function defaultInvitationPages(): array
{
    $meta  = getBuiltinPageMeta();
    $pages = [];
    $order = 0;
    foreach ($meta as $key => $info) {
        $pages[] = [
            'key'     => $key,
            'type'    => 'builtin',
            'enabled' => true,
            'title'   => '',
            'body'    => '',
            'order'   => $order++,
        ];
    }
    return $pages;
}

function parseInvitationPages(?string $json): array
{
    if (!$json) {
        return [];
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function resolveInvitationPages(?string $json): array
{
    $saved    = parseInvitationPages($json);
    $defaults = defaultInvitationPages();
    $builtin  = getBuiltinPageMeta();

    if (empty($saved)) {
        return $defaults;
    }

    $byKey = [];
    foreach ($saved as $row) {
        $key = $row['key'] ?? '';
        if ($key === '') {
            continue;
        }
        $byKey[$key] = [
            'key'     => $key,
            'type'    => ($row['type'] ?? '') === 'custom' ? 'custom' : 'builtin',
            'enabled' => !empty($row['enabled']),
            'title'   => trim($row['title'] ?? ''),
            'body'    => trim($row['body'] ?? ''),
            'order'   => (int) ($row['order'] ?? 0),
        ];
    }

    $merged = [];
    foreach ($defaults as $def) {
        $key = $def['key'];
        if (isset($byKey[$key])) {
            $merged[] = array_merge($def, $byKey[$key], ['type' => 'builtin']);
            unset($byKey[$key]);
        } else {
            $merged[] = $def;
        }
    }

    foreach ($byKey as $row) {
        if ($row['type'] === 'custom') {
            $merged[] = $row;
        }
    }

    usort($merged, fn ($a, $b) => ($a['order'] <=> $b['order']) ?: strcmp($a['key'], $b['key']));

    foreach ($merged as $i => &$page) {
        $page['order'] = $i;
    }
    unset($page);

    return $merged;
}

function buildInvitationPagesFromPost(array $post): string
{
    $rows   = $post['pages'] ?? [];
    $result = [];
    $order  = 0;

    if (!is_array($rows)) {
        return json_encode(defaultInvitationPages(), JSON_UNESCAPED_UNICODE);
    }

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $key  = trim($row['key'] ?? '');
        $type = ($row['type'] ?? '') === 'custom' ? 'custom' : 'builtin';
        if ($key === '') {
            if ($type === 'custom') {
                $key = 'custom_' . bin2hex(random_bytes(4));
            } else {
                continue;
            }
        }
        if ($type === 'builtin' && !isset(getBuiltinPageMeta()[$key])) {
            continue;
        }
        $result[] = [
            'key'     => $key,
            'type'    => $type,
            'enabled' => !empty($row['enabled']),
            'title'   => trim($row['title'] ?? ''),
            'body'    => trim($row['body'] ?? ''),
            'order'   => $order++,
        ];
    }

    if (empty($result)) {
        $result = defaultInvitationPages();
    }

    return json_encode($result, JSON_UNESCAPED_UNICODE);
}

function pageSectionTitle(array $page, string $fallback): string
{
    return ($page['title'] ?? '') !== '' ? $page['title'] : $fallback;
}

function shouldShowInvitationPage(array $page, array $ctx): bool
{
    if (empty($page['enabled'])) {
        return false;
    }

    $event = $ctx['event'];

    if (($page['type'] ?? '') === 'custom') {
        return ($page['title'] ?? '') !== '' || ($page['body'] ?? '') !== '';
    }

    return match ($page['key']) {
        'header', 'detail', 'rsvp', 'footer' => true,
        'quote'     => !empty($event['quran_quote']),
        'mukaddimah'=> !empty($event['mukaddimah']),
        'countdown' => !empty($event['countdown_target']),
        'schedule'  => !empty($ctx['scheduleItems']),
        'speaker'   => !empty($event['speaker_name']),
        'rules'     => !empty($event['dresscode_pria']) || !empty($event['dresscode_wanita']) || !empty($event['special_rules']),
        'guestbook' => !empty($ctx['greetingList']),
        default     => false,
    };
}

function getVisibleInvitationPages(array $pages, array $ctx): array
{
    $visible = [];
    foreach ($pages as $page) {
        if (shouldShowInvitationPage($page, $ctx)) {
            $visible[] = $page;
        }
    }
    return $visible;
}

function renderInvitationPages(array $pages, array $ctx): void
{
    $visible = getVisibleInvitationPages($pages, $ctx);
    $lastKey = '';
    if (!empty($visible)) {
        $last = end($visible);
        $lastKey = $last['key'] ?? '';
        reset($visible);
    }

    foreach ($visible as $page) {
        $isLast = ($page['key'] === $lastKey);
        renderInvitationPage($page, $ctx, $isLast);
    }
}

function renderInvitationPage(array $page, array $ctx, bool $isLast): void
{
    $event       = $ctx['event'];
    $animateOrns = $ctx['animateOrns'];
    $sectionKey  = $page['key'];

    if (($page['type'] ?? '') === 'custom') {
        renderCustomInvitationPage($page, $ctx, $isLast);
        return;
    }

    match ($sectionKey) {
        'header'    => renderPageHeader($page, $ctx, $isLast),
        'quote'     => renderPageQuote($page, $ctx, $isLast),
        'mukaddimah'=> renderPageMukaddimah($page, $ctx, $isLast),
        'countdown' => renderPageCountdown($page, $ctx, $isLast),
        'detail'    => renderPageDetail($page, $ctx, $isLast),
        'schedule'  => renderPageSchedule($page, $ctx, $isLast),
        'speaker'   => renderPageSpeaker($page, $ctx, $isLast),
        'rules'     => renderPageRules($page, $ctx, $isLast),
        'rsvp'      => renderPageRsvp($page, $ctx, $isLast),
        'guestbook' => renderPageGuestbook($page, $ctx, $isLast),
        'footer'    => renderPageFooter($page, $ctx),
        default     => null,
    };
}

function invPageAttrs(array $page, array $ctx): string
{
    return invScreenAttrs(
        $page['key'],
        $ctx['sectionAnims'],
        $ctx['defaultAnim'],
        $ctx['animateOrns']
    );
}

function renderCustomInvitationPage(array $page, array $ctx, bool $isLast): void
{
    $title = e($page['title'] ?: 'Halaman Tambahan');
    ?>
    <section class="inv-screen inv-screen-custom" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($ctx['event']['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section custom-page-section">
                <h2 class="section-title"><?= $title ?></h2>
                <?php if ($page['body']): ?>
                <div class="custom-page-body"><?= nl2br(e($page['body'])) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageHeader(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    ?>
    <section class="inv-screen inv-screen-header" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderOrnament($event['ornament_top'] ?? null, 'top', 'Ornamen atas', $ctx['animateOrns']); ?>
            <header class="inv-header">
                <?php if ($ctx['inviteeName']): ?>
                <div class="header-invitee">
                    <p class="invitee-label"><?= e($ctx['greetingLabel']) ?></p>
                    <p class="invitee-name-inline"><?= e($ctx['inviteeName']) ?></p>
                </div>
                <?php endif; ?>
                <?php if ($event['logo_pesantren']): ?>
                    <img src="<?= e($event['logo_pesantren']) ?>" alt="Logo Pesantren" class="inv-logo">
                <?php endif; ?>
                <p class="inv-pesantren"><?= e($event['pesantren_name']) ?></p>
                <?php if ($event['logo_haflah']): ?>
                    <img src="<?= e($event['logo_haflah']) ?>" alt="Logo Haflah" class="inv-logo-haflah">
                <?php endif; ?>
                <h1 class="inv-title"><?= e($event['title']) ?></h1>
                <?php if ($event['theme_statement']): ?>
                    <p class="inv-theme">"<?= e($event['theme_statement']) ?>"</p>
                <?php endif; ?>
            </header>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageQuote(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'بِسْمِ اللَّهِ');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section quote-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <p class="arabic-quote"><?= e($event['quran_quote']) ?></p>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageMukaddimah(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section">
                <?php if ($page['title']): ?><h2 class="section-title"><?= e($page['title']) ?></h2><?php endif; ?>
                <p class="mukaddimah"><?= nl2br(e($event['mukaddimah'])) ?></p>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageCountdown(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'Hitung Mundur');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section countdown-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <div id="countdown" class="countdown" data-target="<?= e($event['countdown_target']) ?>">
                    <div class="count-item"><span id="cd-days">00</span><small>Hari</small></div>
                    <div class="count-item"><span id="cd-hours">00</span><small>Jam</small></div>
                    <div class="count-item"><span id="cd-mins">00</span><small>Menit</small></div>
                    <div class="count-item"><span id="cd-secs">00</span><small>Detik</small></div>
                </div>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageDetail(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'Waktu & Tempat');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section detail-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <div class="detail-card">
                    <div class="detail-row">
                        <span class="detail-icon">📅</span>
                        <div>
                            <strong><?= e($event['date_masehi']) ?></strong>
                            <?php if ($event['date_hijriah']): ?><br><small><?= e($event['date_hijriah']) ?></small><?php endif; ?>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-icon">🕐</span>
                        <div><?= e($event['event_time']) ?></div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-icon">📍</span>
                        <div>
                            <strong><?= e($event['location_name']) ?></strong>
                            <?php if ($event['location_address']): ?><br><small><?= e($event['location_address']) ?></small><?php endif; ?>
                        </div>
                    </div>
                    <?php if ($event['maps_url']): ?>
                    <a href="<?= e($event['maps_url']) ?>" target="_blank" rel="noopener" class="btn-maps">Buka Google Maps</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageSchedule(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'Susunan Acara');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section schedule-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <div class="schedule-timeline">
                    <?php foreach ($ctx['scheduleItems'] as $si): ?>
                    <div class="schedule-item">
                        <?php if ($si['time']): ?><div class="schedule-time"><?= e($si['time']) ?></div><?php endif; ?>
                        <div class="schedule-content">
                            <strong><?= e($si['title']) ?></strong>
                            <?php if ($si['desc']): ?><p><?= e($si['desc']) ?></p><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageSpeaker(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'Penceramah');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <div class="speaker-card">
                    <strong><?= e($event['speaker_name']) ?></strong>
                    <?php if ($event['speaker_origin']): ?><br><small><?= e($event['speaker_origin']) ?></small><?php endif; ?>
                </div>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageRules(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'Etika & Tata Tertib');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <div class="rules-card">
                    <?php if ($event['dresscode_pria']): ?><p><strong>Pria:</strong> <?= e($event['dresscode_pria']) ?></p><?php endif; ?>
                    <?php if ($event['dresscode_wanita']): ?><p><strong>Wanita:</strong> <?= e($event['dresscode_wanita']) ?></p><?php endif; ?>
                    <?php if ($event['special_rules']): ?><p><?= nl2br(e($event['special_rules'])) ?></p><?php endif; ?>
                </div>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageRsvp(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'Konfirmasi Kehadiran');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section rsvp-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <form id="rsvp-form" class="rsvp-form">
                    <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                    <label>Nama Lengkap *<input type="text" name="guest_name" required maxlength="150" placeholder="Nama Bapak/Ibu/Saudara/i" autocomplete="name" value="<?= e($ctx['inviteeName']) ?>"></label>
                    <label>Konfirmasi Kehadiran *
                        <select name="status" id="rsvp-status">
                            <option value="hadir">Insya Allah Hadir</option>
                            <option value="absen">Maaf, Tidak Bisa Hadir</option>
                        </select>
                    </label>
                    <label id="pax-field">Jumlah yang Hadir (termasuk Anda)<input type="number" name="pax_count" value="1" min="1" max="20" inputmode="numeric"></label>
                    <label>Ucapan & Doa Restu<textarea name="greeting_note" rows="3" placeholder="Tuliskan doa restu atau ucapan selamat..."></textarea></label>
                    <button type="submit" class="btn-rsvp">Kirim Konfirmasi</button>
                    <div id="rsvp-message" class="rsvp-message hidden"></div>
                </form>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageGuestbook(array $page, array $ctx, bool $isLast): void
{
    $event = $ctx['event'];
    $title = pageSectionTitle($page, 'Doa & Ucapan');
    ?>
    <section class="inv-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderDivider($event['ornament_divider'] ?? null, $ctx['animateOrns']); ?>
            <div class="inv-section guestbook-section">
                <h2 class="section-title"><?= e($title) ?></h2>
                <div class="guestbook-list">
                    <?php foreach ($ctx['greetingList'] as $g): ?>
                    <div class="guestbook-item">
                        <strong><?= e($g['guest_name']) ?></strong>
                        <p><?= e($g['greeting_note']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php renderScreenHint($isLast); ?>
    </section>
    <?php
}

function renderPageFooter(array $page, array $ctx): void
{
    $event = $ctx['event'];
    ?>
    <section class="inv-screen inv-footer-screen" <?= invPageAttrs($page, $ctx) ?>>
        <div class="screen-inner">
            <?php renderOrnament($event['ornament_bottom'] ?? null, 'bottom', 'Ornamen bawah', $ctx['animateOrns']); ?>
            <footer class="inv-footer">
                <p><?= e($event['pesantren_name']) ?></p>
                <small>Undangan Digital Haflah</small>
            </footer>
        </div>
    </section>
    <?php
}
