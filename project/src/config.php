<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("742640877548-86ueog4oi9uudf960m598ojl8rs6ufp5.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/DoAn1/project/public/callback.php");
$client->addScope("email");
$client->addScope("profile");
?>