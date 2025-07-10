<?php
session_start();
require 'scenes.php';
require_once 'helpers.php';          

/* cookie-based leaderboard  (7-day lifetime) */
function load_leaderboard(): array {
    return isset($_COOKIE['leaderboard'])
        ? json_decode($_COOKIE['leaderboard'], true) ?: []
        : [];
}
function save_leaderboard(array $rows): void {
    setcookie('leaderboard', json_encode($rows), time()+604800, '/');
}
$leaderboard = load_leaderboard();   // ready for later use


if (!isset($_SESSION['scene'])) {
    header('Location: login.php');
    exit;
}

/* -------- auto-save & jump to leaderboard on endings ---------- */
$endings = ['ending_hero','ending_cursed','ending_freedom','ending_trapped'];

/* -------- choice button ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choice'])) {
    $c = $_POST['choice'];
    $s = $_SESSION['scene'];
    
    if (isset($scenes[$s]['choices'][$c])) {
        $_SESSION['scene'] = $scenes[$s]['choices'][$c];
    }
}

$cur = $scenes[$_SESSION['scene']];

// Special handling for throne_room decisions
if ($_SESSION['scene'] === 'throne_room' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $choice = $_POST['choice'];
    
    if ($choice === 'offer key') {
        if (in_array('silver_key', $_SESSION['inventory'])) {
            $_SESSION['scene'] = 'ending_freedom';
        } else {
            $_SESSION['scene'] = 'ending_fail_key';
        }
    }

    if ($choice === 'cast spell') {
        if (in_array('spellbook', $_SESSION['inventory'])) {
            $_SESSION['scene'] = 'ending_cursed'; // real ending
        } else {
            $_SESSION['scene'] = 'ending_fail_spell';
        }
    }

    if ($choice === 'fight') {
        if (in_array('sword', $_SESSION['inventory'])) {
            $_SESSION['scene'] = 'ending_hero';
        } else {
            $_SESSION['scene'] = 'ending_fail_fight';
        }
    }
}


/* when ending reached, update leaderboard */
$topScores = [];                       // will feed HTML below
$endings   = ['ending_hero','ending_cursed','ending_freedom','ending_trapped'];

if (in_array($_SESSION['scene'], $endings)) {

    $_SESSION['score'] = $_SESSION['health'] + count($_SESSION['inventory'])*10;
    $user = $_SESSION['user'] ?? 'anon';

    /* in-memory leaderboard*/
    $found = false;
    foreach ($leaderboard as &$row) {
        if ($row['user'] === $user) {
            $found = true;
            if ($_SESSION['score'] > $row['score']) {
                $row['score'] = $_SESSION['score'];
                $row['ts']    = time();
            }
            break;
        }
    }
    if (!$found) {
        $leaderboard[] = [
            'user'  => $user,
            'score' => $_SESSION['score'],
            'ts'    => time()
        ];
    }

    /* sort, trim, save back to cookie */
    usort($leaderboard, fn($a,$b)=>$b['score'] <=> $a['score']);
    $leaderboard = array_slice($leaderboard, 0, 10);
    save_leaderboard($leaderboard);

    $topScores = $leaderboard;        // hand to HTML
}

/* background switch */
$bgClass = in_array($_SESSION['scene'], $endings) ? 'end-bg' : 'game-bg';


/* health & item tweaks for specific scenes */
switch ($_SESSION['scene']) {

    /* torch_tunnel: everyone loses 10 HP on entry */
    case 'torch_tunnel':
        $_SESSION['health'] -= 10;
        break;

    /* shadow_corridor: –15 HP, and maybe pick up the key */
    case 'shadow_corridor':
        $_SESSION['health'] -= 15;
        break;

    case 'key_taken':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['choice'] === 'take key') {
            if (!in_array('silver_key', $_SESSION['inventory'])) {
                $_SESSION['inventory'][] = 'silver_key';
            }
        }
        break;

    /* spellbook: +20 HP and add the spellbook once */
    case 'spellbook':
        $_SESSION['health'] += 20;
        if (!in_array('spellbook', $_SESSION['inventory'])) {
            $_SESSION['inventory'][] = 'spellbook';
        }
        break;

    /* watch_tower: add sword only if that choice was clicked */
    case 'sword_taken':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['choice'] === 'grab sword') {
            if (!in_array('sword', $_SESSION['inventory'])) {
                $_SESSION['inventory'][] = 'sword';
            }
        }
        break;

    /* catacombs: rats take 20 HP */
    case 'catacombs':
        $_SESSION['health'] -= 20;
        break;

    /* campfire: rest restores 15 HP */
    case 'campfire':
        $_SESSION['health'] += 15;
        break;
}


    /* Apply scene effects  */
if (isset($cur['effects'])) {
    // effect keyed by choice label or '*' for everyone
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choice'])) {
        $c = $_POST['choice'];
        if (isset($cur['effects'][$c]))          $effect = $cur['effects'][$c];
        elseif (isset($cur['effects']['*']))     $effect = $cur['effects']['*'];
    } elseif (isset($cur['effects']['*'])) {
        $effect = $cur['effects']['*'];
    }

    if (!empty($effect)) {
        if (isset($effect['health'])) {
            $_SESSION['health'] += $effect['health'];
        }
        if (isset($effect['add_item'])) {
            if (!in_array($effect['add_item'], $_SESSION['inventory'])) {
                $_SESSION['inventory'][] = $effect['add_item'];
            }
        }
    }
}

/* Item-gated scenes */
if (isset($cur['requires_item']) && isset($cur['success_scene'])) {
    if (!in_array($cur['requires_item'], $_SESSION['inventory'])) {
        // player lacks the item, stay in place or show message
        $cur['text'] .= "\n\n(The door is locked. You need the {$cur['requires_item']}.)";
        // strip the forbidden choice
        unset($cur['choices']['unlock']);
    } else {
        // auto-redirect once they click “unlock”
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['choice'] === 'unlock') {
            $_SESSION['scene'] = $cur['success_scene'];
            header('Location: game.php');   // reload
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RPG</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="<?= $bgClass ?>">
 <?php if ($_SESSION['scene'] === 'ending_hero' || $_SESSION['scene'] === 'ending_freedom'): ?>
  <div class="confetti"></div>
<?php endif; ?>   
    <?php if ($topScores !== null) : ?>
        <div class="leaderboard">
            <h3>Top Adventurers</h3>
            <table>
                <tr><th>#</th><th>User</th><th>Score</th></tr>
                <?php foreach ($topScores as $i => $r): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($r['user']) ?></td>
                    <td><?= $r['score'] ?></td>
                    <?php if (empty($topScores)): ?>
                        <tr><td colspan="3">No scores yet</td></tr>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <div class="box">
        <div class="hud">
            <p>Health: <?= $_SESSION['health'] ?></p>
            <p>Inventory: <?= implode(', ', $_SESSION['inventory']) ?: 'None' ?></p>
        </div>
        
        <div id="story" class="typewriter story-text" data-text="<?=htmlspecialchars($cur['text'])?>"></div>

        <?php if($cur['choices']):?>
        <form method="post" id="choiceForm">
            <?php foreach($cur['choices'] as $txt=>$next):?>
            <button class="choice" name="choice" value="<?=$txt?>"><?=ucfirst($txt)?></button>
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
