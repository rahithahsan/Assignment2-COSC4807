<?php
/* ---------- config.php ---------- */
define('VALID_USERNAME', 'admin');
define('VALID_PASSWORD', 'secret');

define('MAX_FAILED', 5);        // lock out after 5 bad tries
define('LOCKOUT_SECONDS', 60);  // …for one minute
date_default_timezone_set('America/Toronto');
?>