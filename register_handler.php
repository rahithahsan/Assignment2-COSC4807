<?php
ob_start();
session_start();
require_once 'db.php';
require_once 'helpers.php';

$u  = trim($_POST['username'] ?? '');
$p  = $_POST['password'] ?? '';
$cp = $_POST['confirm']  ?? '';

/* presence */
if ($u === '' || $p === '' || $cp === '') {
  $_SESSION['flash'] = 'All fields are required.';
  header('Location: register.php'); exit;
}

/* match */
if ($p !== $cp) {
  $_SESSION['flash'] = 'Passwords do not match.';
  header('Location: register.php'); exit;
}

/* strength */
if (!password_meets_policy($p)) {
  $_SESSION['flash'] = 'Password must be ≥8 chars and include upper/lower/number/special.';
  header('Location: register.php'); exit;
}

/* duplicate username */
$pdo = db();
$stmt = $pdo->prepare('SELECT 1 FROM users WHERE username = ?');
$stmt->execute([$u]);
if ($stmt->fetch()) {
  $_SESSION['flash'] = 'Username already exists.';
  header('Location: register.php'); exit;
}

/* insert user */
$hash = password_hash($p, PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)')
    ->execute([$u, $hash]);

$_SESSION['flash'] = 'Account created — log in!';
header('Location: login.php');
exit;          // ← no closing PHP tag, no HTML, no extra whitespace