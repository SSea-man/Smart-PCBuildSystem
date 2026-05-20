<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_auth();
if (!is_post()) { redirect('dashboard.php'); }
verify_csrf();
$uid      = (int)get_auth_user()['id'];
$build_id = (int)input('build_id');
if ($build_id) {
    db_exec('DELETE FROM `build` WHERE build_id=? AND user_id=?', [$build_id, $uid]);
    flash_message('success','Build deleted.');
}
redirect('dashboard.php');
