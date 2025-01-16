
<?php
// /config/config.php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);



// /config/config.php

// Start the session with secure settings
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'use_only_cookies' => true,
]);

// Implement session timeout
$timeout_duration = 1800; // 30 minutes

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();

?>