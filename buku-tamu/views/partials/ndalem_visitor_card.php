<?php
/**
 * Kartu ringkas tamu sowan untuk rekap ndalem.
 * @var array $visitor
 * @var string $variant 'upcoming'|'entered'
 */
$variant = $variant ?? 'upcoming';
$timeLabel = match ($visitor['status']) {
    'pending' => 'Daftar ' . date('H:i', strtotime($visitor['created_at'])),
    'checked_in', 'in_queue' => 'Check-in ' . ($visitor['checked_in_at'] ? date('H:i', strtotime($visitor['checked_in_at'])) : '-'),
    'approved' => 'Disetujui ' . ($visitor['approved_at'] ? date('H:i', strtotime($visitor['approved_at'])) : '-'),
    'called' => 'Masuk ' . ($visitor['approved_at'] ? date('H:i', strtotime($visitor['approved_at'])) : '-'),
    'completed' => 'Selesai',
    default => date('H:i', strtotime($visitor['created_at'])),
};
?>

<div class="flex items-start gap-3 p-4 rounded-xl border <?= $variant === 'entered' ? 'bg-emerald-50/50 border-emerald-100' : 'bg-amber-50/30 border-amber-100' ?>">
    <div class="queue-number !w-12 !h-12 !text-base flex-shrink-0"><?= str_pad($visitor['queue_number'], 3, '0', STR_PAD_LEFT) ?></div>
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2">
            <p class="font-bold text-gray-800"><?= e($visitor['nama_lengkap']) ?></p>
            <?= status_badge($visitor['status']) ?>
            <?= kedatangan_badge($visitor) ?>
        </div>
        <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-1 text-xs text-gray-500">
            <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3 h-3"></i><?= e(area_masuk_label($visitor['area_masuk'] ?? 'pesantren')) ?></span>
            <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3 h-3"></i><?= e($visitor['asal']) ?></span>
            <span class="flex items-center gap-1"><i data-lucide="users" class="w-3 h-3"></i><?= e($app['rombongan_options'][$visitor['jumlah_rombongan']] ?? $visitor['jumlah_rombongan']) ?></span>
            <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i><?= e($timeLabel) ?></span>
        </div>
        <?php if (!empty($visitor['detail_keperluan'])): ?>
        <p class="text-xs text-gray-600 mt-2 line-clamp-2"><?= e($visitor['detail_keperluan']) ?></p>
        <?php endif; ?>
    </div>
</div>
