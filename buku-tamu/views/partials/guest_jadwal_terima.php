<?php
/**
 * Partial: tampilan jadwal terima tamu di form tamu
 * @var array $jadwalPesantren grouped
 * @var array $jadwalNdalem grouped
 */
?>
<div class="card border-pesantren-100 bg-gradient-to-br from-white to-pesantren-50/30 p-4 mb-5 animate-fade-up animate-delay-1" id="jadwalTerimaCard">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-pesantren-100 flex items-center justify-center flex-shrink-0">
            <i data-lucide="calendar-days" class="w-5 h-5 text-pesantren-700"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-sm" id="jadwalTerimaTitle">Jadwal Terima Tamu</p>
            <p class="text-[10px] text-gray-400 mb-2">Informasi rekap — pendaftaran tetap bisa dilakukan</p>
            <div id="jadwalTerimaContent" class="text-sm">
                <p class="text-gray-400 italic text-xs">Pilih tujuan kunjungan untuk melihat jadwal</p>
            </div>
        </div>
    </div>
</div>

<script>
const jadwalData = {
    pesantren: <?= json_encode($jadwalPesantrenJson ?? []) ?>,
    ndalem: <?= json_encode($jadwalNdalemJson ?? []) ?>
};
const jadwalLabels = { pesantren: 'Pesantren (Kantor)', ndalem: 'Ndalem (Sowan Pengasuh)' };
const hariLabels = <?= json_encode(jadwal_terima_hari_options()) ?>;

function renderJadwalHtml(area) {
    const grouped = jadwalData[area] || {};
    const keys = Object.keys(grouped).sort((a, b) => a - b);
    if (!keys.length) {
        return '<p class="text-gray-400 italic text-xs">Belum ada jadwal terima tamu.</p>';
    }
    let html = '<div class="space-y-1.5">';
    keys.forEach(hari => {
        const slots = grouped[hari].map(s => {
            let t = s.mulai + ' – ' + s.selesai;
            if (s.ket) t += ' <span class="text-gray-400">(' + s.ket + ')</span>';
            return t;
        }).join('<span class="text-gray-300 mx-1">·</span>');
        html += '<div class="flex gap-2 text-xs leading-relaxed">';
        html += '<span class="font-semibold text-pesantren-800 w-12 flex-shrink-0">' + hariLabels[hari] + '</span>';
        html += '<span class="text-gray-600">' + slots + '</span></div>';
    });
    html += '</div>';
    return html;
}

function updateJadwalDisplay() {
    const tujuan = document.querySelector('input[name="tujuan_kunjungan"]:checked');
    const content = document.getElementById('jadwalTerimaContent');
    const title = document.getElementById('jadwalTerimaTitle');
    if (!tujuan) {
        content.innerHTML = '<p class="text-gray-400 italic text-xs">Pilih tujuan kunjungan untuk melihat jadwal</p>';
        return;
    }
    const area = tujuan.value === 'sowan' ? 'ndalem' : 'pesantren';
    title.textContent = 'Jadwal Terima Tamu — ' + jadwalLabels[area];
    content.innerHTML = renderJadwalHtml(area);
}
</script>
