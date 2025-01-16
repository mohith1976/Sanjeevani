<?php
// /public/delete_category.php

require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_code = $_SESSION['user_login_code'];
    $category_id = $_POST['id'];  // This must match the key sent in the AJAX request

    $database = new Database();
    $db = $database->getConnection();

    $query = "DELETE FROM categories WHERE id = :id AND login_code = :login_code";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $category_id);
    $stmt->bindParam(':login_code', $login_code);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unable to delete category.']);
    }
}
?>
