<?php
session_start();
require 'helpers.php';

/* ---------- auto-login via cookie ---------- */
if(!isset($_SESSION['user']) && isset($_COOKIE['rpg_user'],$_COOKIE['rpg_token'])){
    if(verify_user($_COOKIE['rpg_user'],$_COOKIE['rpg_token'])){
        $_SESSION['user'] = $_COOKIE['rpg_user'];
    }
}

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $user = trim($_POST['user']??'');
    $pass = $_POST['pass']??'';
    if(!$user || !$pass){ $err='Fill both fields.'; }
    elseif(find_user($user)){                         // existing user → login
        if(verify_user($user,$pass)){
            $_SESSION['user']=$user;
        } else { $err='Wrong password.'; }
    } else {                                          // new user → register
        add_user($user,$pass);
        $_SESSION['user']=$user;
    }

    /* set 1-week remember-me cookies */
    if(isset($_SESSION['user'])){
        setcookie('rpg_user',  $user, time()+604800,'/');
        setcookie('rpg_token', $pass, time()+604800,'/');   // ⚠️ demo only – store hash in prod
        header('Location:start.php'); exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RPG Login</title><link rel="stylesheet" href="style.css">
</head>

<body class="login_bg">
    <div class="box">
        <h1>Ancient Adventure!</h1>
        <h2>Log In / Register</h2>
        <?php if($err) echo "<p style='color:#f55'>$err</p>"; ?>
        <form method="post">
            <input name="user" placeholder="Username" required>
            <input type="password" name="pass" placeholder="Password" required>
            <button type="submit">Save & Start</button>
        </form>
    </div>
</body>
</html>
