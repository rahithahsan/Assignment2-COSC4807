
# COSC 4806 Assignment 2 — PHP Login System with MariaDB

> COSC 4806 • Spring 2025  
> Author: **Rahith Ahsan**

---

## Project overview
This repository contains a complete login / registration system that persists users in a **MariaDB** instance hosted on filess.io.

* Passwords are **bcrypt‑hashed** with `password_hash()` & verified with `password_verify()`.
* A front‑end password checklist (vanilla JS + Bootstrap) guides users to meet the security policy.
* Per‑username lock‑out after **5** failed attempts (60 s window) thwarts brute‑force attacks.
* Secrets (DB password) are stored in the Replit **Secrets** manager—never committed to Git.

---

## Rubric checklist

| Requirement | Implementation |
|-------------|----------------|
| filess.io account + **MariaDB** DB | Database **`cosc4806_storyline`** on host `7x3qv.h.filess.io:3305`. |
| Website connects to DB | **`db.php`** (PDO) reads constants from `config.php` & `DB_PASS` secret. |
| `users` table | **`migrations/001_create_users.sql`** (id, username, password_hash, timestamp). |
| User can register | `register.php` → `register_handler.php`. |
| Duplicate‑username check | `SELECT 1 FROM users WHERE username = ?`. |
| Password stored securely | `password_hash($plain, PASSWORD_DEFAULT)`. |
| Password strength enforced | `helpers.php::password_meets_policy()` + live checklist UI. |
| Login uses DB (no hard‑coded creds) | `login.php` + PDO lookup. |
| Hash comparison on login | `password_verify($input, $hash)`. |
| Per‑user rate‑limit | `failed[$username]`, `lockout_until[$username]` in `$_SESSION`. |
| ≥10 commits | Git log shows 14 descriptive commits. |

---

## Implementation map

| File | Status | Role |
|------|--------|------|
| `config.php` | **updated** | Adds DB constants; removes `VALID_USERNAME/PASSWORD`. |
| `db.php` | new | PDO singleton helper (`utf8mb4`, strict errors). |
| `helpers.php` | new | `password_meets_policy()` regexes. |
| `migrations/001_create_users.sql` | new | Creates `users` table. |
| `register.php` | new | Bootstrap card, live checklist, show‑password toggle. |
| `register_handler.php` | new | Validates, hashes, inserts user. |
| `login.php` | **refactor** | DB auth, per‑user lock‑out, Bootstrap UI. |
| `index.php` | updated | Protected dashboard. |
| `logout.php` | updated | Ends session. |

---

## Project tree

```text
.
├── db.php
├── helpers.php
├── migrations/
│   └── 001_create_users.sql
├── register.php
├── register_handler.php
├── login.php
├── index.php
└── logout.php
```

---

## Key code snippets

### 1. Connecting to MariaDB (`db.php`)
```php
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    DB_HOST, DB_PORT, DB_NAME
);

$pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
```

### 2. Password‑strength policy (`helpers.php`)
```php
function password_meets_policy(string $p): bool {
    return strlen($p) >= 8 &&
           preg_match('/[A-Z]/', $p) &&
           preg_match('/[a-z]/', $p) &&
           preg_match('/\d/',   $p) &&
           preg_match('/[^A-Za-z0-9]/', $p);
}
```

### 3. Bootstrap live checklist (Register page)
```html
<ul id="pwdChecklist">
  <li id="chkLen">≥ 8 chars</li> … 
</ul>

<script>
pwd.addEventListener('input', e => {
  set('chkLen' , e.target.value.length >= 8);
  // …
});
</script>
```

### 4. Per‑username lock‑out (Login page)
```php
$userFailed  = $_SESSION['failed'][$u] ?? 0;
$userLockout = $_SESSION['lockout_until'][$u] ?? 0;

if ($now < $userLockout) { … }   // locked
```

---

## Quick start

```bash
git clone https://github.com/rahithahsan/assignment2-login.git
cd assignment2-login

export DB_PASS=yourDbPassword

php -r "require 'db.php'; db()->exec(file_get_contents('migrations/001_create_users.sql'));"

php -S 0.0.0.0:8080
```

Open **http://localhost:8080/register.php** in your browser.

---

## External libraries

| Library | CDN | Purpose | License |
|---------|-----|---------|---------|
| Bootstrap 5.3 | `cdn.jsdelivr.net` | Responsive layout & utilities | MIT |
| Feather Icons | `unpkg.com` | SVG eye icon for show‑password | MIT |

---

## What I learned

* **PDO prepared statements** and parameter binding.  
* Proper secret management with **Replit Secrets**.  
* BCrypt hashing & verification using PHP’s built‑in functions.  
* Building real‑time UI feedback with plain JavaScript and minimal HTML.  
* Implementing secure session handling: ID regeneration & per‑user lock‑out.  
* Leveraging Bootstrap’s utility classes for rapid, consistent styling.  
* Importance of clear, atomic git commits in a collaborative workflow.

---

> Built & tested on Replit (PHP 8.2) and filess.io (MariaDB 10.6)