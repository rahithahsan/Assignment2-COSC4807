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
  /* ---- POST branch ---- */

  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';

  /* --- make sure session vars are arrays --- */
  if (!isset($_SESSION['failed']) || !is_array($_SESSION['failed'])) {
      $_SESSION['failed'] = [];
  }
  if (!isset($_SESSION['lockout_until']) || !is_array($_SESSION['lockout_until'])) {
      $_SESSION['lockout_until'] = [];
  }

  $userFailed  = $_SESSION['failed'][$u]        ?? 0;
  $userLockout = $_SESSION['lockout_until'][$u] ?? 0;

  /* ---------- lockout check ---------- */
  if ($now < $userLockout) {
      $remaining = $userLockout - $now;
      $error = "Too many failed attempts. Try again in {$remaining} s.";
  } else {
      // reset counter after lock-out expires
      if ($userLockout) {
          $userFailed = 0;
          unset($_SESSION['lockout_until'][$u]);
      }

      /* ---------- DB lookup ---------- */
      $pdo  = db();
      $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
      $stmt->execute([$u]);
      $user = $stmt->fetch();

      $auth_ok = $user && password_verify($p, $user['password_hash']);

      if ($auth_ok) {
          /* --- success --- */
          session_regenerate_id(true);
          $_SESSION['authenticated'] = true;
          $_SESSION['username']      = $u;
          unset($_SESSION['failed'][$u], $_SESSION['lockout_until'][$u]);
          header('Location: index.php');
          exit;
      }

      /* ---------- failure for THIS user ---------- */
      $userFailed++;
      $_SESSION['failed'][$u] = $userFailed;

      if ($userFailed >= MAX_FAILED) {
          $_SESSION['lockout_until'][$u] = $now + LOCKOUT_SECONDS;
          $error = "Too many failed attempts. Locked for " . LOCKOUT_SECONDS . " s.";
      } else {
          $tries = MAX_FAILED - $userFailed;
          $error = $user
                   ? "Invalid password. {$tries} attempt(s) left."
                   : "User not found. {$tries} attempt(s) left.";
      }
  }
  /* ---- end POST branch ---- */
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
<h2>Login</h2>

<?php if ($notice): ?>
  <p style="color:blue"><?= htmlspecialchars($notice) ?></p>
<?php endif; ?>

<form method="post">
  <label>Username
    <input type="text" name="username" required autocomplete="username">
  </label><br><br>
  <label>Password
    <input type="password" name="password" required autocomplete="current-password">
  </label><br><br>
  <button type="submit">Submit</button>
</form>

<p>Need an account? <a href="register.php">Register here</a></p>

<?php if (!empty($error)): ?>
  <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

</body>
</html>