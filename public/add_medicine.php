<?php
require_once '../config/config.php';
require_once '../classes/Database.php';

// Initialize Database connection
$database = new Database();
$db = $database->getConnection();

$medicineName = $_POST['medicineName'];
$category_id = $_POST['category_id'];
$supplier_id = $_POST['supplier_id'];
$quantity = $_POST['quantity'];
$expiry_date = $_POST['expiry_date'];
$login_code = $_SESSION['user_login_code'];

$query = "INSERT INTO medicines (medicineName, category_id, supplier_id, quantity, expiry_date, login_code) VALUES (:medicineName, :category_id, :supplier_id, :quantity, :expiry_date, :login_code)";
$stmt = $db->prepare($query);

$stmt->bindParam(':medicineName', $medicineName);
$stmt->bindParam(':category_id', $category_id);
$stmt->bindParam(':supplier_id', $supplier_id);
$stmt->bindParam(':quantity', $quantity);
$stmt->bindParam(':expiry_date', $expiry_date);
$stmt->bindParam(':login_code', $login_code);

if ($stmt->execute()) {
    echo 'Medicine added successfully!';
} else {
    echo 'Failed to add medicine.';
}
?>
