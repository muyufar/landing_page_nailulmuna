<?php $user = Auth::user(); ?>
<header class="md:hidden bg-pesantren-800 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-20">
    <div>
        <p class="font-bold text-sm"><?= e($mobileTitle ?? 'Admin') ?></p>
        <p class="text-emerald-300/60 text-xs"><?= e($user['name']) ?></p>
    </div>
    <div class="flex gap-3 items-center">
        <?php if (!empty($mobileBackUrl)): ?>
        <a href="<?= $mobileBackUrl ?>" class="text-emerald-200"><i data-lucide="arrow-left" class="w-5 h-5"></i></a>
        <?php endif; ?>
        <a href="<?= base_url('/admin/logout') ?>" class="text-red-300"><i data-lucide="log-out" class="w-5 h-5"></i></a>
    </div>
</header>
