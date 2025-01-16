<?php
// Database connection
$con = mysqli_connect('localhost', 'root', '', 'hospital');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $bloodgroup = $_POST['bloodgroup'];
    $bags = $_POST['bags'];
    $date = $_POST['date'];

    // Server-side validation for age
    if ($age < 18 || $age > 65) {
        $message = $age < 18 ? "Your Age is less than 18." : "Your Age is greater than 65.";
    } else {
        // Begin transaction
        mysqli_begin_transaction($con);

        try {
            // Insert blood donation data
            $query = "INSERT INTO blood_donation (donor_name, age, blood_group, number_of_bags, date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param('sisis', $name, $age, $bloodgroup, $bags, $date);
            $stmt->execute();
            $stmt->close();

            // Check if the blood group already exists in the blood bank
            $query = "SELECT id, number_of_bags FROM blood_bank WHERE blood_group = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('s', $bloodgroup);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Blood group exists, update the record
                $row = $result->fetch_assoc();
                $existing_bags = $row['number_of_bags'] + $bags;
                $id = $row['id'];

                $update_query = "UPDATE blood_bank SET number_of_bags = ? WHERE id = ?";
                $update_stmt = $con->prepare($update_query);
                $update_stmt->bind_param('ii', $existing_bags, $id);
                $update_stmt->execute();
                $update_stmt->close();
            } else {
                // Blood group does not exist, insert a new record
                $insert_query = "INSERT INTO blood_bank (blood_group, number_of_bags) VALUES (?, ?)";
                $insert_stmt = $con->prepare($insert_query);
                $insert_stmt->bind_param('si', $bloodgroup, $bags);
                $insert_stmt->execute();
                $insert_stmt->close();
            }

            // Commit transaction
            mysqli_commit($con);
            $message = "New record created successfully and blood bank updated.";
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($con);
            $message = "Error: " . $e->getMessage();
        }
    }

    // Close the database connection
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script>
        // Client-side validation for age
        function validateForm(event) {
            var age = document.getElementById('age').value;
            if (age < 18) {
                mysqli_commit($con);
                $message = "Age must be 18 or older";
                event.preventDefault(); // Prevent form submission
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Blood Donation Form</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form id="donationForm" method="post" action="" onsubmit="return validateForm(event)">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>
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
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-tkS4DD6fuCjzm8A3fN0z8yZRe5wpaDkWTCx4QQ9K5BQ4BuF7AkLX0z6ntJKjLzTO" crossorigin="anonymous"></script>
</body>
</html>
