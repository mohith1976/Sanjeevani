<?php
// /public/add_supplier.php

require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_code = $_SESSION['user_login_code']; // Get the login code from session
    $supplierName = $_POST['supplierName'];
    $contact_info = $_POST['contact_info'];

    $database = new Database();
    $db = $database->getConnection();

    $query = "INSERT INTO suppliers (supplierName, contact_info, login_code) VALUES (:supplierName, :contact_info, :login_code)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplierName', $supplierName);
    $stmt->bindParam(':contact_info', $contact_info);
    $stmt->bindParam(':login_code', $login_code);

    if ($stmt->execute()) {
        // Success: No output needed, silent success
    } else {
        echo 'Error adding supplier';
    }
}
