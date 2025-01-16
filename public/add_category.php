<?php
// /public/add_category.php

require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_code = $_SESSION['user_login_code']; // Get the login code from session
    $categoryName = $_POST['categoryName'];

    $database = new Database();
    $db = $database->getConnection();

    $query = "INSERT INTO categories (categoryName, login_code) VALUES (:categoryName, :login_code)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':categoryName', $categoryName);
    $stmt->bindParam(':login_code', $login_code);

    if ($stmt->execute()) {
        // Success: No output needed, silent success
    } else {
        echo 'Error adding category';
    }
}
