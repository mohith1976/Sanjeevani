<?php
// /public/delete_supplier.php

require_once '../config/config.php';
require_once '../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_code = $_SESSION['user_login_code'];
    $supplier_id = $_POST['id'];

    $database = new Database();
    $db = $database->getConnection();

    $query = "DELETE FROM suppliers WHERE id = :id AND login_code = :login_code";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $supplier_id);
    $stmt->bindParam(':login_code', $login_code);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Supplier deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unable to delete supplier.']);
    }
}
?>
