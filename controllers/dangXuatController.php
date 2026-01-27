<?php

session_start();

$_SESSION = [];

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Hủy hoàn toàn session trên server
session_destroy();

header("Location: dangNhapController.php");
exit;
