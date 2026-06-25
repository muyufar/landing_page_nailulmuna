<?php
require __DIR__ . '/../src/Database.php';
$db = Database::connect();
$db->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('pesantren_logo', '')");
echo "Setting pesantren_logo OK\n";
