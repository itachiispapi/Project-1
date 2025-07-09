<?php
/* ---------- config ---------- */
define('USERS_FILE',  __DIR__ . '/data/users.json');

/* ---------- JSON helpers ----- */
function load_json($file)  {
    return file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];
}
function save_json($file,$data){
    file_put_contents(
        $file,
        json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );
}

/* ---------- user helpers ----- */
function find_user($username){
    $users = load_json(USERS_FILE);
    return $users[$username] ?? null;          // ['hash'=>..., 'created'=>...]
}
function add_user($username,$password){
    $users = load_json(USERS_FILE);
    $users[$username] = [
        'hash'    => password_hash($password, PASSWORD_DEFAULT),
        'created' => date('c')
    ];
    save_json(USERS_FILE, $users);
}
function verify_user($username,$password){
    $u = find_user($username);
    return $u && password_verify($password, $u['hash']);
}
