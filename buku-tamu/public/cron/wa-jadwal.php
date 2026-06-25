<?php
/**
 * Cron: pengingat WhatsApp jadwal kunjungan mendatang.
 * Windows Task Scheduler / Linux crontab: setiap 5-15 menit.
 */
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/SettingsModel.php';
require_once __DIR__ . '/../../src/VisitorModel.php';
require_once __DIR__ . '/../../src/WhatsAppService.php';
require_once __DIR__ . '/../../src/helpers.php';

$app = require __DIR__ . '/../../config/app.php';
date_default_timezone_set($app['timezone']);

$whatsapp = new WhatsAppService();
$sent = $whatsapp->processScheduledReminders();

header('Content-Type: text/plain');
echo date('Y-m-d H:i:s') . " — Pengingat jadwal terkirim: {$sent}\n";
