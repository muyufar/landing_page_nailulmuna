<?php $title = 'Cetak Stiker Tamu'; ?>

<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden" style="width: 80mm;">
        <div class="h-1 bg-gradient-to-r from-pesantren-600 via-gold-500 to-pesantren-600"></div>
        <div class="p-5 text-center">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-pesantren-700"><?= e($app['pesantren_name']) ?></p>
            <p class="text-[9px] text-gray-400 uppercase tracking-wider mt-0.5">Kartu Tamu</p>

            <div class="my-4 py-3 bg-pesantren-50 rounded-xl">
                <p class="text-5xl font-extrabold text-pesantren-700 leading-none"><?= str_pad($visitor['queue_number'], 3, '0', STR_PAD_LEFT) ?></p>
            </div>

            <p class="font-bold text-sm text-gray-800"><?= e($visitor['nama_lengkap']) ?></p>
            <p class="text-[10px] text-gray-500 mt-1"><?= e(tujuan_label($visitor['tujuan_kunjungan'])) ?></p>
            <p class="text-[9px] text-gray-400 mt-1"><?= date('d/m/Y H:i') ?> · <?= e($visitor['hijri_date']) ?></p>

            <div class="mt-3 inline-block p-1.5 border border-gray-200 rounded-lg">
                <img src="<?= qr_url($visitor['ticket_code'], 72) ?>" alt="QR" class="mx-auto">
            </div>
            <p class="text-[9px] font-mono tracking-widest text-gray-500 mt-1"><?= e($visitor['ticket_code']) ?></p>
        </div>
    </div>
</div>

<button onclick="window.print()" class="no-print fixed bottom-4 right-4 btn-primary shadow-lg">
    Cetak Stiker
</button>

<script>window.onload = () => setTimeout(() => window.print(), 500);</script>
