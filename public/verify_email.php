<?php
// Set the correct timezone
date_default_timezone_set('Asia/Kolkata'); // Replace with your correct timezon
// public/verify_email.php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/EmailVerification.php';

$database = new Database();
$db = $database->getConnection();

$emailVerification = new EmailVerification($db);

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    if ($emailVerification->verifyToken($token)) {
        header("Location: register.php?token=$token");
        exit;
    } else {
        echo "This link has expired or is invalid.";
    }
} else {
    echo "No token provided.";
}
?>
