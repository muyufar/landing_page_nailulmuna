<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
session_destroy();
header('Location: login.php');
exit;
