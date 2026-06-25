<?php
require __DIR__ . '/../src/Database.php';
$db = Database::connect();
$db->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('wa_on_register_guest', '1')");
echo "Setting wa_on_register_guest OK\n";
