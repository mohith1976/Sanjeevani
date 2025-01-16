<?php
// Database connection
$con = mysqli_connect('localhost', 'root', '', 'hospital');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $bloodgroup = $_POST['bloodgroup'];
    $bags = $_POST['bags'];

    // Check if the blood group already exists
    $query = "SELECT id, number_of_bags FROM blood_bank WHERE blood_group = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $bloodgroup);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Blood group exists, update the record
        $row = $result->fetch_assoc();
        $existing_bags = $row['number_of_bags'] + $bags;
        $id = $row['id'];  // Get the ID of the existing record

        $update_query = "UPDATE blood_bank SET number_of_bags = ? WHERE id = ?";
        $update_stmt = $con->prepare($update_query);
        $update_stmt->bind_param('ii', $existing_bags, $id);

        if ($update_stmt->execute()) {
            $message = "Record updated successfully";
        } else {
            $message = "Error: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        // Blood group does not exist, insert a new record
        $insert_query = "INSERT INTO blood_bank (blood_group, number_of_bags) VALUES (?, ?)";
        $insert_stmt = $con->prepare($insert_query);
        $insert_stmt->bind_param('si', $bloodgroup, $bags);

        if ($insert_stmt->execute()) {
            $message = "New record created successfully";
        } else {
            $message = "Error: " . $insert_stmt->error;
        }

        $insert_stmt->close();
    }

    $stmt->close();
}

// Close the database connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script>
        // Client-side validation for age
        function validateForm(event) {
            var age = document.getElementById('age').value;
            if (age < 18) { 
                alert('Age must be 18 or older.');
                event.preventDefault(); // Prevent form submission
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Blood Bank Form</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form id="patientForm" method="post" action="" onsubmit="return validateForm(event)">
            <div class="mb-3">
                <label for="bloodgroup" class="form-label">Blood Group</label>
                <select class="form-select" id="bloodgroup" name="bloodgroup" required>
                    <option value="" disabled selected>Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="bags" class="form-label">Number of Bags</label>
                <input type="number" class="form-control" id="bags" name="bags" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-tkS4DD6fuCjzm8A3fN0z8yZRe5wpaDkWTCx4QQ9K5BQ4BuF7AkLX0z6ntJKjLzTO" crossorigin="anonymous"></script>
</body>
</html>
            