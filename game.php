<?php
    session_start();
    require'scenes.php';
    if(!isset($_SESSION['scene'])){header('Location:login.php');exit;}
    if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['choice'])){
    $c=$_POST['choice'];$s=$_SESSION['scene'];
    if(isset($scenes[$s]['choices'][$c]))$_SESSION['scene']=$scenes[$s]['choices'][$c];
    }
    $cur=$scenes[$_SESSION['scene']];

    $cur = $scenes[$_SESSION['scene']];

// decrease health or add item
    if ($_SESSION['scene'] === 'bear_attack') {
        $_SESSION['health'] -= 30;
    }
    if ($_SESSION['scene'] === 'meet_old_man') {
        if (!in_array('magic ring', $_SESSION['inventory'])) {
            $_SESSION['inventory'][] = 'magic ring';
        }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RPG</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="box">
        <div class="hud">
            <p>Health: <?= $_SESSION['health'] ?></p>
            <p>Inventory: <?= implode(', ', $_SESSION['inventory']) ?: 'None' ?></p>
        </div>
        
        <div id="story" class="typewriter" data-text="<?=htmlspecialchars($cur['text'])?>"></div>

        <?php if($cur['choices']):?>
        <form method="post" id="choiceForm">
            <?php foreach($cur['choices'] as $txt=>$next):?>
            <button name="choice" value="<?=$txt?>"><?=ucfirst($txt)?></button>
            <?php endforeach;?>
        </form>

        <?php else:?>
        <a href="login.php">Play Again</a>
        <?php endif;?>
    </div>

    <script>
        const el=document.getElementById('story');const t=el.dataset.text;
        let i=0;function type(){if(i<t.length){el.textContent+=t.charAt(i);i++;setTimeout(type,35);}else{document.getElementById('choiceForm')?.classList.add('show');}}
        el.textContent='';type();
    </script>
</body>
</html>
