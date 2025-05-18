<?php
session_start();
require_once 'db.php';

/* ---------- flash message handling ---------- */
$notice = '';
if (!empty($_SESSION['flash'])) {
  $notice = $_SESSION['flash'];
  unset($_SESSION['flash']);          // display once
}

/* ---------- bounce if already logged in ---------- */
if (!empty($_SESSION['authenticated'])) {
  $_SESSION['flash'] = 'ℹ️  You are already logged in.';
  header('Location: index.php');
  exit;
}

/* ---------- per-user lockout + login logic ---------- */
$now = time();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';

  // ensure arrays
  $_SESSION['failed']        = is_array($_SESSION['failed']        ?? null) ? $_SESSION['failed']        : [];
  $_SESSION['lockout_until'] = is_array($_SESSION['lockout_until'] ?? null) ? $_SESSION['lockout_until'] : [];

  $userFailed  = $_SESSION['failed'][$u]        ?? 0;
  $userLockout = $_SESSION['lockout_until'][$u] ?? 0;

  /* ---- lockout window ---- */
  if ($now < $userLockout) {
      $remaining = $userLockout - $now;
      $error = "Too many failed attempts. Try again in {$remaining} s.";
  } else {

      if ($userLockout) {
          // lockout expired – reset counter
          $userFailed = 0;
          unset($_SESSION['lockout_until'][$u]);
      }

      /* ---- DB lookup ---- */
      $stmt = db()->prepare('SELECT * FROM users WHERE username = ?');
      $stmt->execute([$u]);
      $user = $stmt->fetch();

      $auth_ok = $user && password_verify($p, $user['password_hash']);

      if ($auth_ok) {
          session_regenerate_id(true);
          $_SESSION['authenticated'] = true;
          $_SESSION['username']      = $u;
          unset($_SESSION['failed'][$u], $_SESSION['lockout_until'][$u]);
          header('Location: index.php'); exit;
      }

      /* ---------- failure path ---------- */
      if ($user) {
          // only increment / lock out for existing usernames
          $userFailed++;
          $_SESSION['failed'][$u] = $userFailed;

          if ($userFailed >= MAX_FAILED) {
              $_SESSION['lockout_until'][$u] = $now + LOCKOUT_SECONDS;
              $error = "Too many failed attempts. Locked for " . LOCKOUT_SECONDS . " s.";
          } else {
              $tries = MAX_FAILED - $userFailed;
              $error = "Invalid password. {$tries} attempt(s) left.";
          }
      } else {
          // unknown username – show message but DON’T count toward lockout
          $error = "User not found.";
      }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>

  <!-- Bootstrap & Feather icons -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card shadow p-4" style="min-width: 360px;">
  <h3 class="mb-3">Log in</h3>

  <?php if ($notice): ?>
    <div class="alert alert-info"><?= htmlspecialchars($notice) ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required autocomplete="username">
    </div>

    <div class="mb-3 position-relative">
      <label class="form-label">Password</label>
      <input type="password" name="password" id="pwd" class="form-control" required autocomplete="current-password">
      <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="togglePwd" style="cursor:pointer">
        <i data-feather="eye"></i>
      </span>
    </div>

    <button class="btn btn-primary w-100">Submit</button>
  </form>

  <hr>
  <p class="text-center mb-0">
    Need an account?
    <a href="register.php">Register here</a>
  </p>
</div>

<script>
const pwd = document.getElementById('pwd');
document.getElementById('togglePwd').addEventListener('click', () => {
  pwd.type = (pwd.type === 'password') ? 'text' : 'password';
});
feather.replace();
</script>
</body>
</html>