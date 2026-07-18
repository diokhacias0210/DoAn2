<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("");
$client->setClientSecret("");
$client->setRedirectUri("http://localhost/DoAn1/project/public/callback.php");
$client->addScope("email");
$client->addScope("profile");
?>