<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create account</title>

  <!-- Bootstrap 5 -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Feather icons -->
  <script src="https://unpkg.com/feather-icons"></script>

  <style>.checklist li.done{color:#198754}</style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card shadow p-4" style="min-width:360px">
  <h3 class="mb-3">Create account</h3>

  <?php if(!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <form method="post" action="register_handler.php" id="regForm" novalidate>
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input name="username" class="form-control" required>
    </div>

    <!-- Password -->
    <div class="mb-3 position-relative">
      <label class="form-label">Password</label>
      <input type="password" name="password" id="pwd"
             class="form-control" required>
      <span class="position-absolute top-50 end-0 translate-middle-y me-3"
            id="togglePwd" style="cursor:pointer">
        <i data-feather="eye"></i>
      </span>
    </div>

    <!-- Confirm -->
    <div class="mb-3 position-relative">
      <label class="form-label">Confirm password</label>
      <input type="password" name="confirm" id="pwd2" class="form-control" required>
      <span class="position-absolute top-50 end-0 translate-middle-y me-3"
            id="togglePwd2" style="cursor:pointer">
        <i data-feather="eye"></i>
      </span>
      <div class="invalid-feedback">Passwords do not match.</div>
    </div>

    <!-- Live checklist -->
    <ul class="checklist small mb-3" id="pwdChecklist">
      <li id="chkLen">≥ 8 characters</li>
      <li id="chkUpper">Upper-case letter</li>
      <li id="chkLower">Lower-case letter</li>
      <li id="chkDigit">Number</li>
      <li id="chkSpecial">Special character</li>
    </ul>

    <button class="btn btn-primary w-100" id="submitBtn" disabled>Create account</button>
  </form>

  <hr>
  <p class="text-center mb-0">
    Already have an account? <a href="login.php">Log in</a>
  </p>
</div>

<!-- JS -->
<script>
const pwd  = document.getElementById('pwd');
const pwd2 = document.getElementById('pwd2');
const btn  = document.getElementById('submitBtn');
const list = {
  len : document.getElementById('chkLen'),
  up  : document.getElementById('chkUpper'),
  lo  : document.getElementById('chkLower'),
  num : document.getElementById('chkDigit'),
  spec: document.getElementById('chkSpecial'),
};

pwd.addEventListener('input', validate);
pwd2.addEventListener('input', validate);

function validate(){
  const v = pwd.value;
  const ok = [
    set(list.len , v.length >= 8),
    set(list.up  , /[A-Z]/.test(v)),
    set(list.lo  , /[a-z]/.test(v)),
    set(list.num , /\d/.test(v)),
    set(list.spec, /[^A-Za-z0-9]/.test(v))
  ].every(Boolean);

  const match = v !== '' && v === pwd2.value;
  pwd2.classList.toggle('is-invalid', !match && pwd2.value !== '');
  btn.disabled = !(ok && match);
}

function set(el, ok){ el.classList.toggle('done',ok); return ok; }

document.getElementById('togglePwd' ).onclick=()=>pwd .type=pwd .type==='password'?'text':'password';
document.getElementById('togglePwd2').onclick=()=>pwd2.type=pwd2.type==='password'?'text':'password';
feather.replace();
</script>
</body>
</html>