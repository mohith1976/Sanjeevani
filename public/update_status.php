<?php
session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_login_code'])) {
    echo 'Login code or user ID is not set in the session.';
    exit;
}

$login_code = $_SESSION['user_login_code'];
$status = isset($_POST['status']) ? $_POST['status'] : 'off';

require_once '../config/config.php';
require_once '../classes/Database.php';

$database = new Database();
$db = $database->getConnection();

// Check if the record already exists
$query = "SELECT id FROM hospital_status WHERE login_code = :login_code LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':login_code', $login_code);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Update the existing record
    $query = "UPDATE hospital_status SET status = :status WHERE login_code = :login_code";
} else {
    // Insert a new record
    $query = "INSERT INTO hospital_status (login_code, status) VALUES (:login_code, :status)";
}

$stmt = $db->prepare($query);
$stmt->bindParam(':login_code', $login_code);
$stmt->bindParam(':status', $status);

if ($stmt->execute()) {
    echo 'Status updated successfully.';
} else {
    echo 'Failed to update status.';
}
?>
