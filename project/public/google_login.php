<?php
require_once __DIR__ . '/../src/config.php';
$login_url = $client->createAuthUrl();
header("Location: " . $login_url);
exit;
?>