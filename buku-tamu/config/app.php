<?php

return [
    'name' => 'Buku Tamu Online',
    'pesantren_name' => 'A.P.I Nailul Muna',
    'pesantren_address' => 'Jl. Pesantren No. 1, Desa Muna, Kecamatan Ilmu, Jawa Timur',
    'timezone' => 'Asia/Jakarta',
    'base_url' => '/landing page/buku-tamu/public',
    'upload_path' => __DIR__ . '/../public/uploads/',
    'upload_max_size' => 5 * 1024 * 1024, // 5MB
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
    'whatsapp' => [
        'enabled' => false,
        'provider' => 'fonnte', // fonnte | wablas
        'token' => '',
        'fonnte_url' => 'https://api.fonnte.com/send',
        'wablas_url' => 'https://wablas.com/api/send-message',
    ],
    'ndalem_ruang' => 'Ruang Tunggu Ndalem Barat',
    'tujuan_options' => [
        'sowan' => 'Sowan Pengasuh',
        'jenguk' => 'Jenguk Santri',
        'administrasi' => 'Urusan Administrasi',
        'kerjasama' => 'Kerjasama / Instansi',
        'lainnya' => 'Lainnya',
    ],
    'rombongan_options' => [
        '1' => '1 orang',
        '2-5' => '2-5 orang',
        '>5' => 'Lebih dari 5 orang',
    ],
];
