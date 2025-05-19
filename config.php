<?php
/* ---------- config.php (updated for Assignment 2) ---------- */
/* DB connection */
define('DB_HOST', '7x3qv.h.filess.io');
define('DB_PORT', 3305);
define('DB_NAME', 'cosc4806_storyline');
define('DB_USER', 'cosc4806_storyline');
define('DB_PASSWORD', getenv('DB_PASS'));

/* Login-rate-limit settings */
define('MAX_FAILED',      5);   // lock out after 5 bad tries
define('LOCKOUT_SECONDS', 60);  // …for one minute

date_default_timezone_set('America/Toronto');