<?php
// Start the session
session_start();

// Database configuration
$host = 'localhost';       // Replace with your database host
$username = 'root';        // Replace with your database username
$password = '';            // Replace with your database password
$dbname = 'vaccinedb';     // Replace with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        // Delete data
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM vaccine WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
        // Fetch data
        $query = "SELECT * FROM vaccine";
        $result = $conn->query($query);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Set Content-Type header to JSON
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        $conn->close();
        exit();
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccine Data</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .action-btn {
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="main">
    <h1>Vaccine Data</h1>

    <table id="vaccineTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#vaccineTable').DataTable({
                "ajax": {
                    "url": "index1.php?action=fetch",
                    "type": "GET",
                    "dataSrc": "data"
                },
                "columns": [
                    { "data": "id" },
                    { "data": "vaccine_name" },
                    { "data": "date_created" },
                    {
                        "data": null,
                        "defaultContent": "<button class='action-btn'>Delete</button>"
                    }
                ]
            });

            // Handle delete button click
            $('#vaccineTable tbody').on('click', 'button.action-btn', function() {
                var data = table.row($(this).parents('tr')).data();
                var id = data.id;

                if (confirm("Are you sure you want to delete this record?")) {
                    $.ajax({
                        url: "index1.php",
                        type: "POST",
                        data: { action: 'delete', id: id },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                table.ajax.reload(); // Reload data to reflect changes
                            } else {
                                alert('Error: ' + result.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
    </div>
</body>
</html>
