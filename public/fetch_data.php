<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in and has a login_code in the session
if (!isset($_SESSION['user_login_code'])) {
    echo json_encode(["error" => "Login code not found in session"]);
    exit();
}

// Retrieve the login_code from session
$login_code = $_SESSION['user_login_code'];

// Database connection
$con = mysqli_connect('localhost', 'root', '', 'hospital');

if (!$con) {
    echo json_encode(["error" => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

// Query to fetch data based on login_code
$query = "SELECT * FROM patient_enroll WHERE login_code = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $login_code);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res) {
    echo json_encode(["error" => "Query failed: " . mysqli_error($con)]);
    exit();
}

// Fetch data
$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

// Get the count of entries based on login_code
$countQuery = "SELECT COUNT(*) as total FROM patient_enroll WHERE login_code = ?";
$stmt = mysqli_prepare($con, $countQuery);
mysqli_stmt_bind_param($stmt, 's', $login_code);
mysqli_stmt_execute($stmt);
$countResult = mysqli_stmt_get_result($stmt);
$countRow = mysqli_fetch_assoc($countResult);
$count = $countRow['total'];

// Close connection
mysqli_close($con);

// Return the data and count as JSON
echo json_encode([
    'data' => $data,
    'count' => $count
]);
?>
