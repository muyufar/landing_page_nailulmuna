<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$event = getEventById($id);
if (!$event) {
    flash('error', 'Undangan tidak ditemukan.');
    header('Location: ' . app_url('admin/events/index.php'));
    exit;
}

$db = getDB();
$newSlug = $event['slug'] . '-copy-' . date('Ymd');
$baseSlug = $newSlug;
$i = 1;
while (true) {
    $check = $db->prepare('SELECT id FROM events WHERE slug = ?');
    $check->execute([$newSlug]);
    if (!$check->fetch()) break;
    $newSlug = $baseSlug . '-' . $i++;
}

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
) SELECT
    ?, CONCAT(title, " (Salinan)"), pesantren_name, theme_statement, invitation_greeting, mukaddimah, quran_quote,
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
    audio_mode, audio_url, seat_capacity, "arsip"
FROM events WHERE id = ?';

$db->prepare($sql)->execute([$newSlug, $id]);
$newId = (int) $db->lastInsertId();

flash('success', 'Draf undangan berhasil diduplikasi (status: arsip).');
header('Location: ' . app_url('admin/events/edit.php?id=' . $newId));
exit;
