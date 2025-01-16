<?php
// Start the session and include necessary files

require_once '../config/config.php';
require_once '../classes/Database.php';

// Initialize Database connection
$database = new Database();
$db = $database->getConnection();

// Handle form submission for issuing medicine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['medicineName'])) {
    $medicineName = $_POST['medicineName'];
    $issueQuantity = (int) $_POST['issueQuantity'];
    $login_code = $_SESSION['user_login_code'];

    // Check if the medicine exists and get its current quantity
    $query = "SELECT quantity FROM medicines WHERE medicineName = :medicineName AND login_code = :login_code";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':medicineName', $medicineName);
    $stmt->bindParam(':login_code', $login_code);
    $stmt->execute();

    $medicine = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medicine) {
        // Medicine not found
        echo json_encode(['status' => 'error', 'message' => 'Medicine not available']);
    } else {
        $currentQuantity = (int) $medicine['quantity'];

        if ($currentQuantity < 1) {
            // Medicine out of stock
            echo json_encode(['status' => 'error', 'message' => 'Out of stock']);
        } elseif ($issueQuantity > $currentQuantity) {
            // Not enough quantity available
            echo json_encode(['status' => 'error', 'message' => 'Insufficient quantity available']);
        } else {
            // Update the quantity
            $newQuantity = $currentQuantity - $issueQuantity;
            $updateQuery = "UPDATE medicines SET quantity = :newQuantity WHERE medicineName = :medicineName AND login_code = :login_code";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':newQuantity', $newQuantity);
            $updateStmt->bindParam(':medicineName', $medicineName);
            $updateStmt->bindParam(':login_code', $login_code);

            if ($updateStmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Medicine issued successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update medicine quantity']);
            }
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Medicine</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <form id="issueMedicineForm" method="POST">
        <div>
            <label for="medicineName">Medicine Name:</label>
            <input type="text" id="medicineName" name="medicineName" required>
        </div>
        <div>
            <label for="issueQuantity">Quantity to Issue:</label>
            <input type="number" id="issueQuantity" name="issueQuantity" required>
        </div>
        <button type="submit">Issue Medicine</button>
    </form>
    <div id="issueResult"></div>

    <script>
        $(document).ready(function() {
            $('#issueMedicineForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '', // Same file
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        let result = JSON.parse(response);
                        $('#issueResult').text(result.message);

                        if (result.status === 'success') {
                            $('#issueMedicineForm')[0].reset();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
