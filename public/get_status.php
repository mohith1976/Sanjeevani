<?php
session_start();
require_once '../classes/Database.php'; // Include your database connection
require_once '../classes/User.php';
require_once '../config/config.php';

if (isset($_SESSION['login_code'])) {
    $login_code = $_SESSION['login_code'];

    $query = "SELECT status FROM hospital_status WHERE login_code = :login_code";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':login_code', $login_code);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo $result['status'];
} else {
    echo "Invalid request.";
}
?>
