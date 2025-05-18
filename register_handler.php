<?php
session_start();
require_once 'db.php';
require_once 'helpers.php';

$u = trim($_POST['username'] ?? '');
$p = $_POST['password'] ?? '';

if ($u === '' || $p === '') {
  $_SESSION['flash'] = 'Both fields required.';
  header('Location: register.php'); exit;
}

if (!password_meets_policy($p)) {
  $_SESSION['flash'] = 'Password must be ≥8 chars, include upper/lower/number/special.';
  header('Location: register.php'); exit;
}

$pdo = db();

/* Already taken? */
$stmt = $pdo->prepare('SELECT 1 FROM users WHERE username = ?');
$stmt->execute([$u]);
if ($stmt->fetch()) {
  $_SESSION['flash'] = 'Username already exists.';
  header('Location: register.php'); exit;
}

/* Insert */
$hash = password_hash($p, PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)')
    ->execute([$u, $hash]);

$_SESSION['flash'] = 'Account created — log in!';
header('Location: login.php');
exit;