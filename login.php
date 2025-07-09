<?php
    session_start();
        if($_SERVER['REQUEST_METHOD']==='POST'){
        setcookie('rpg_user',$_POST['user'],time()+604800,'/');
        setcookie('rpg_pass',$_POST['pass'],time()+604800,'/');
        header('Location:start.php');
        exit;
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="box">
        <h1>Login</h1>
        <form method="post">
        <input name="user" placeholder="Username" required>
        <input type="password" name="pass" placeholder="Password" required>
        <button type="submit">Save & Start</button>
        </form>
    </div>
</body>
</html>
