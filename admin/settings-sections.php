<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    content()->saveSettings([
        'section_muassis_title' => trim($_POST['section_muassis_title'] ?? ''),
        'section_muassis_subtitle' => trim($_POST['section_muassis_subtitle'] ?? ''),
        'section_stories_title' => trim($_POST['section_stories_title'] ?? ''),
        'section_stories_subtitle' => trim($_POST['section_stories_subtitle'] ?? ''),
        'section_programs_title' => trim($_POST['section_programs_title'] ?? ''),
        'section_programs_subtitle' => trim($_POST['section_programs_subtitle'] ?? ''),
        'section_fasilitas_title' => trim($_POST['section_fasilitas_title'] ?? ''),
        'section_fasilitas_subtitle' => trim($_POST['section_fasilitas_subtitle'] ?? ''),
        'section_stats_title' => trim($_POST['section_stats_title'] ?? ''),
        'section_blog_title' => trim($_POST['section_blog_title'] ?? ''),
        'section_blog_subtitle' => trim($_POST['section_blog_subtitle'] ?? ''),
    ]);
    flash('success', 'Judul section berhasil disimpan.');
    redirect('settings-sections.php');
}

$s = content()->getSettings();
admin_header('Judul Section');
?>
<div class="card card-admin">
    <div class="card-body">
        <form method="post">
            <?php csrf_field(); ?>
            <h6 class="text-success">Muassis</h6>
            <div class="row g-2 mb-3">
                <div class="col-md-6"><input type="text" name="section_muassis_subtitle" class="form-control" placeholder="Subtitle" value="<?= e($s['section_muassis_subtitle'] ?? '') ?>"></div>
                <div class="col-md-6"><input type="text" name="section_muassis_title" class="form-control" placeholder="Judul" value="<?= e($s['section_muassis_title'] ?? '') ?>"></div>
            </div>
            <h6 class="text-success">Wajah Santri</h6>
            <div class="row g-2 mb-3">
                <div class="col-md-6"><input type="text" name="section_stories_subtitle" class="form-control" value="<?= e($s['section_stories_subtitle'] ?? '') ?>"></div>
                <div class="col-md-6"><input type="text" name="section_stories_title" class="form-control" value="<?= e($s['section_stories_title'] ?? '') ?>"></div>
            </div>
            <h6 class="text-success">Program</h6>
            <div class="row g-2 mb-3">
                <div class="col-md-6"><input type="text" name="section_programs_subtitle" class="form-control" value="<?= e($s['section_programs_subtitle'] ?? '') ?>"></div>
                <div class="col-md-6"><input type="text" name="section_programs_title" class="form-control" value="<?= e($s['section_programs_title'] ?? '') ?>"></div>
            </div>
            <h6 class="text-success">Fasilitas</h6>
            <div class="row g-2 mb-3">
                <div class="col-md-6"><input type="text" name="section_fasilitas_subtitle" class="form-control" placeholder="Subtitle" value="<?= e($s['section_fasilitas_subtitle'] ?? '') ?>"></div>
                <div class="col-md-6"><input type="text" name="section_fasilitas_title" class="form-control" placeholder="Judul" value="<?= e($s['section_fasilitas_title'] ?? '') ?>"></div>
            </div>
            <h6 class="text-success">Statistik & Blog</h6>
            <div class="mb-2"><input type="text" name="section_stats_title" class="form-control" placeholder="Judul Angka Bicara" value="<?= e($s['section_stats_title'] ?? '') ?>"></div>
            <div class="row g-2 mb-3">
                <div class="col-md-6"><input type="text" name="section_blog_subtitle" class="form-control" value="<?= e($s['section_blog_subtitle'] ?? '') ?>"></div>
                <div class="col-md-6"><input type="text" name="section_blog_title" class="form-control" value="<?= e($s['section_blog_title'] ?? '') ?>"></div>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
<?php admin_footer(); ?>
