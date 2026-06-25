<?php $title = 'Login Ndalem'; ?>

<div class="min-h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 login-brand items-center justify-center p-12">
        <div class="relative z-10 text-white max-w-md">
            <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center mb-6">
                <i data-lucide="home" class="w-8 h-8 text-gold-400"></i>
            </div>
            <h1 class="font-display text-4xl font-bold mb-3">Dashboard Ndalem</h1>
            <p class="text-emerald-100/80 text-lg">Asisten Pengasuh — Kelola antrean sowan & status ketersediaan</p>
            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 text-emerald-100/70 text-sm">
                    <i data-lucide="bell" class="w-4 h-4"></i> Notifikasi tamu sowan
                </div>
                <div class="flex items-center gap-3 text-emerald-100/70 text-sm">
                    <i data-lucide="list-ordered" class="w-4 h-4"></i> Manajemen antrean
                </div>
                <div class="flex items-center gap-3 text-emerald-100/70 text-sm">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Kirim WhatsApp otomatis
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center bg-pattern px-4 py-12">
        <div class="max-w-md w-full animate-fade-up">
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex w-14 h-14 rounded-2xl bg-pesantren-700 items-center justify-center mb-3">
                    <i data-lucide="home" class="w-7 h-7 text-white"></i>
                </div>
                <h1 class="font-display text-2xl font-bold text-gray-800">Dashboard Ndalem</h1>
            </div>

            <div class="card p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-1">Masuk Ndalem</h2>
                <p class="text-gray-400 text-sm mb-6">Asisten Pengasuh</p>

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
        </div>
    </div>
</div>
<script>if (typeof lucide !== 'undefined') lucide.createIcons();</script>
