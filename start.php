<?php
session_start();
$_SESSION['scene'] = 'start';
$_SESSION['health'] = 100;
$_SESSION['inventory'] = [];
header('Location: game.php');
exit;