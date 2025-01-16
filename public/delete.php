<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_login_code'])) {
    echo json_encode(["error" => "Login code not found in session"]);
    exit();
}

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'hospital');

if (!$con) {
    echo json_encode(["error" => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

// Get the ID from POST data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id == 0) {
    echo json_encode(["error" => "Invalid ID"]);
    exit();
}

// Prepare delete query
$query = "DELETE FROM patient_enroll WHERE id = ? AND login_code = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['user_login_code']);
$success = mysqli_stmt_execute($stmt);

if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to delete: " . mysqli_error($con)]);
}

// Close connection
mysqli_close($con);
?>
