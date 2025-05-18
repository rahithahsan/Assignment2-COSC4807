<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Create account</title></head>
<body>
<h2>Register</h2>

<?php if (!empty($_SESSION['flash'])): ?>
  <p style="color:blue"><?= htmlspecialchars($_SESSION['flash']) ?></p>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="post" action="register_handler.php">
  <label>Username <input name="username" required></label><br><br>
  <label>Password <input type="password" name="password" required></label><br><br>
  <button>Create account</button>
</form>

<p><a href="login.php">Back to login</a></p>
</body>
</html>