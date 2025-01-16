<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize Database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Fetch user details
$user->id = $_SESSION['user_id'];
$user->email = $_SESSION['user_email'];
$user->name = $_SESSION['user_name'];
$user->login_code = $_SESSION['user_login_code'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Inventory Management</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1>Medicine Inventory Management</h1>

        <!-- Add Supplier Form -->
        <h2>Manage Suppliers</h2>
        <form id="addSupplierForm" class="mb-3">
            <div class="form-group">
                <label for="supplier_name">Supplier Name:</label>
                <input type="text" id="supplier_name" name="supplierName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="supplier_contact_info">Contact Info:</label>
                <input type="text" id="supplier_contact_info" name="contact_info" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Supplier</button>
        </form>
        <table id="supplierTable" class="display">
            <thead>
                <tr>
                <th>Id</th>
                    <th>Name</th>
                    <th>Contact Info</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>

        <!-- Add Category Form -->
        <h2>Manage Categories</h2>
        <form id="addCategoryForm" class="mb-3">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="categoryName" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
        <table id="categoryTable" class="display">
            <thead>
                <tr>
                 <th>Id</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will populate this -->
            </tbody>
        </table>

        <!-- Add Medicine Form -->
        <h2>Manage Medicines</h2>
        <form id="addMedicineForm" class="mb-3">
            <div class="form-group">
                <label for="medicine_name">Medicine Name:</label>
                <input type="text" id="medicine_name" name="medicineName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="medicine_category">Category:</label>
                <select id="medicine_category" name="category_id" class="form-control" required></select>
            </div>
            <div class="form-group">
                <label for="medicine_supplier">Supplier:</label>
                <select id="medicine_supplier" name="supplier_id" class="form-control" required></select>
            </div>
            <div class="form-group">
                <label for="medicine_quantity">Quantity:</label>
                <input type="number" id="medicine_quantity" name="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="medicine_expiry_date">Expiry Date:</label>
                <input type="date" id="medicine_expiry_date" name="expiry_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Medicine</button>
        </form>
        <table id="medicineTable" class="display">
            <thead>
                <tr>
                <th>Id</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables will populate this -->
            </tbody>
        </table>

        
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <!-- Your custom JS -->
    <script src="../assets/js/script.js"></script>
</body>
</html>
