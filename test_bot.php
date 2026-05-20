<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'name' => 'Admin', 'role' => 'admin'];
$_SERVER['REQUEST_METHOD'] = 'POST';
file_put_contents('php://memory', '{"messages":[{"role":"user","content":"price of RTX 4070"}]}');
// Wait, php://input cannot be mocked easily like this for included files.
// Let's just use curl with a cookie.
