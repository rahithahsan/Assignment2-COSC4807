<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create account</title>

  <!-- Bootstrap 5 CDN -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- Feather icons for eye toggle -->
  <script src="https://unpkg.com/feather-icons"></script>

  <style>
    .checklist li.done { color: #198754; }   /* green */
  </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card shadow p-4" style="min-width: 360px;">
  <h3 class="mb-3">Create account</h3>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <form method="post" action="register_handler.php" id="regForm">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input name="username" class="form-control" required>
    </div>

    <div class="mb-3 position-relative">
      <label class="form-label">Password</label>
      <input type="password" name="password" id="pwd" class="form-control" required>
      <span class="position-absolute top-50 end-0 translate-middle-y me-3"
            style="cursor:pointer" id="togglePwd">
        <i data-feather="eye"></i>
      </span>
    </div>

    <ul class="checklist small mb-3" id="pwdChecklist">
      <li id="chkLen"> ≥ 8 characters</li>
      <li id="chkUpper">Upper-case letter</li>
      <li id="chkLower">Lower-case letter</li>
      <li id="chkDigit">Number</li>
      <li id="chkSpecial">Special character</li>
    </ul>

    <button class="btn btn-primary w-100">Create account</button>
  </form>

  <hr>
  <p class="text-center mb-0">
    Already have an account?
    <a href="login.php">Log in</a>
  </p>
</div>

<!-- Live password checker -->
<script>
const pwd  = document.getElementById('pwd');
const list = {
  len:  document.getElementById('chkLen'),
  up:   document.getElementById('chkUpper'),
  lo:   document.getElementById('chkLower'),
  num:  document.getElementById('chkDigit'),
  spec: document.getElementById('chkSpecial'),
};

pwd.addEventListener('input', () => {
  const v = pwd.value;
  set(list.len , v.length >= 8);
  set(list.up  , /[A-Z]/.test(v));
  set(list.lo  , /[a-z]/.test(v));
  set(list.num , /\d/.test(v));
  set(list.spec, /[^A-Za-z0-9]/.test(v));
});

function set(el, ok) {
  el.classList.toggle('done', ok);
}

document.getElementById('togglePwd').addEventListener('click', () => {
  pwd.type = (pwd.type === 'password') ? 'text' : 'password';
});

feather.replace();      // load eye icon
</script>
</body>
</html>