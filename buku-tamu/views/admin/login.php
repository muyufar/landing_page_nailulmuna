<?php $title = 'Login Admin'; ?>

<div class="min-h-screen flex">
    <!-- Brand Panel -->
    <div class="hidden lg:flex lg:w-1/2 login-brand items-center justify-center p-12">
        <div class="relative z-10 text-white max-w-md">
            <?php
            $brandSize = 'lg';
            $brandIcon = 'shield';
            $brandMargin = 'mb-6';
            require __DIR__ . '/../partials/pesantren_brand.php';
            ?>
            <h1 class="font-display text-4xl font-bold mb-3">Pos Keamanan</h1>
            <p class="text-emerald-100/80 text-lg leading-relaxed"><?= e($app['pesantren_name']) ?></p>
            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 text-emerald-100/70 text-sm">
                    <i data-lucide="eye" class="w-4 h-4"></i> Monitor tamu real-time
                </div>
                <div class="flex items-center gap-3 text-emerald-100/70 text-sm">
                    <i data-lucide="log-in" class="w-4 h-4"></i> Check-in & Check-out
                </div>
                <div class="flex items-center gap-3 text-emerald-100/70 text-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak stiker tamu
                </div>
            </div>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="flex-1 flex items-center justify-center bg-pattern px-4 py-12">
        <div class="max-w-md w-full animate-fade-up">
            <div class="lg:hidden text-center mb-8">
                <?php
                $brandSize = 'md';
                $brandIcon = 'shield';
                $brandClass = 'bg-pesantren-700 rounded-2xl';
                $brandMargin = 'mb-3';
                $brandIconClass = 'text-white';
                require __DIR__ . '/../partials/pesantren_brand.php';
                ?>
                <h1 class="font-display text-2xl font-bold text-gray-800">Pos Keamanan</h1>
            </div>

            <div class="card p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-1">Masuk ke Dasbor</h2>
                <p class="text-gray-400 text-sm mb-6">Petugas keamanan & front office</p>

                <?php if (!empty($error)): ?>
                <div class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <?= e($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                        <div class="input-wrap">
                            <i data-lucide="user" class="input-icon w-4 h-4"></i>
                            <input type="text" name="username" required class="input-field" placeholder="Masukkan username">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="input-wrap">
                            <i data-lucide="lock" class="input-icon w-4 h-4"></i>
                            <input type="password" name="password" required class="input-field" placeholder="Masukkan password">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full py-3.5">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Masuk
                    </button>
                </form>
            </div>
            <p class="text-center text-xs text-gray-400 mt-4">Hubungi administrator jika lupa password</p>
        </div>
    </div>
</div>
<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
