<?php
session_start();
$_SESSION['scene'] = 'start';
$_SESSION['health'] = 100;
$_SESSION['inventory'] = [];
$_SESSION['score'] = 0;
header('Location:game.php');   
exit;
