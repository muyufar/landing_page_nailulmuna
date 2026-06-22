<?php
/**
 * Akses Back Office — URL terpisah (tidak ditampilkan di landing page).
 * Bookmark: http://localhost/landing%20page/panel.php
 */
declare(strict_types=1);

header('Location: admin/login.php', true, 302);
exit;
