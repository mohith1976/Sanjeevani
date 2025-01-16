<?php
// Start the session
session_start();

// Database configuration
$host = 'localhost';       // Replace with your database host
$username = 'root';        // Replace with your database username
$password = '';            // Replace with your database password
$dbname = 'hospital';     // Replace with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vaccine_name = $_POST['vaccine_name'];
    $date = $_POST['date_created'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO vaccine (vaccine_name, date_created) VALUES (?, ?)");
    $stmt->bind_param("ss", $vaccine_name, $date);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Store success message in session
        $_SESSION['message'] = "<div class='message success'>New vaccine added successfully.</div>";
    } else {
        // Store error message in session
        $_SESSION['message'] = "<div class='message error'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();

    // Close the connection
    $conn->close();

    // Redirect to the same page to avoid form resubmission
    header("Location: vaccine.php");
    exit();
}

// Retrieve and clear the message from session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vaccine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f9;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .message {
            padding: 15px;
            margin-bottom: 0.5cm; /* Space between message and form */
            border-radius: 5px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="date"] {
            width: calc(100% - 20px);
            padding: 15px;
            font-size: 1rem;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        <form action="vaccine.php" method="POST">
            <h1>Add Vaccine</h1>

            <label for="vaccine_name">Vaccine Name:</label>
            <input type="text" id="vaccine_name" name="vaccine_name" required>

            <label for="date_created">Date:</label>
            <input type="date" id="date_created" name="date_created" required>

            <input type="submit" value="Add Vaccine">
        </form>
    </div>
</body>
</html>
