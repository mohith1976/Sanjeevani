<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
// Initialize Database connection
$database = new Database();
$db = $database->getConnection();

$medicine_id = $_POST['id'];
$login_code = $_SESSION['user_login_code'];

$query = "DELETE FROM medicines WHERE id = :id AND login_code = :login_code";
$stmt = $db->prepare($query);

$stmt->bindParam(':id', $medicine_id);
$stmt->bindParam(':login_code', $login_code);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Medicine deleted successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete medicine.']);
}
?>
