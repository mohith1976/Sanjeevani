<?php
// Database connection
$con = mysqli_connect('localhost', 'root', '', 'hospital');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $name = $_POST['name'];
    $bloodgroup = $_POST['bloodgroup'];
    $bags = $_POST['bags'];
    $date = $_POST['date'];

    // Begin transaction
    mysqli_autocommit($con, false); // Disable autocommit mode

    try {
        // Check if the blood group exists and has enough bags
        $query = "SELECT id, number_of_bags FROM blood_bank WHERE blood_group = ?";
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $con->error);
        }
        $stmt->bind_param('s', $bloodgroup);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['number_of_bags'] >= $bags) {
                // Insert blood issue data
                $query = "INSERT INTO blood_issue (patient, blood_group, number_of_bags, date) VALUES (?, ?, ?, ?)";
                $stmt->prepare($query);
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $con->error);
                }
                $stmt->bind_param('ssis', $name, $bloodgroup, $bags, $date);
                $stmt->execute();

                // Update blood bank data
                $query = "UPDATE blood_bank SET number_of_bags = number_of_bags - ? WHERE blood_group = ?";
                $stmt->prepare($query);
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $con->error);
                }
                $stmt->bind_param('is', $bags, $bloodgroup);
                $stmt->execute();

                // Commit transaction
                mysqli_commit($con);
                $message = "New record created successfully and blood bank updated.";
            } else {
                throw new Exception("Insufficient bags available for this blood group.");
            }
        } else {
            throw new Exception("Blood group not found in the blood bank.");
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        $message = "Error: " . $e->getMessage();
    } finally {
        if (isset($stmt) && !$stmt->close()) {
            $stmt->close(); // Only close if the statement is not already closed
        }
        $con->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Issue Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<body>
    <div class="container mt-5">
        <h2>Blood Issue Form</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form id="donationForm" method="post" action="" onsubmit="return validateForm(event)">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
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
