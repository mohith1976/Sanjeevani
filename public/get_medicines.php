<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';


// Initialize Database connection
$database = new Database();
$db = $database->getConnection();

$login_code = $_SESSION['user_login_code'];

$query = "SELECT m.id, m.medicineName, c.categoryName, s.supplierName, m.quantity, m.expiry_date
          FROM medicines m
          JOIN categories c ON m.category_id = c.id
          JOIN suppliers s ON m.supplier_id = s.id
          WHERE m.login_code = :login_code";
$stmt = $db->prepare($query);
$stmt->bindParam(':login_code', $login_code);
$stmt->execute();

$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "data" => $medicines
]);
?>
