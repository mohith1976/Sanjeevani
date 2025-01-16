<?php
// /public/get_suppliers.php

require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_code = $_SESSION['user_login_code'];

    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT id, supplierName, contact_info FROM suppliers WHERE login_code = :login_code";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':login_code', $login_code);
    $stmt->execute();

    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $suppliers]);
}
?>
