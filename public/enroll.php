  <?php
  
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

include 'connect.php';

  

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_login_code'])) {
    echo 'Login code or user ID is not set in the session.';
    exit;
}




  // Check if form is submitted
  if (isset($_POST['enroll_button'])) {
      // Retrieve form data
      $patientname = $_POST['patientname'];
      $age = $_POST['age'];
      $gender = $_POST['gender'];
      $login_code = $_SESSION['user_login_code'];
      // Prepare SQL statement to prevent SQL injection
      $stmt = $con->prepare("INSERT INTO patient_enroll (login_code, patient_name, age, gender) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssis", $login_code, $patientname, $age, $gender); // Changed to 'sis'

      // Execute the statement and check for success
      if ($stmt->execute()) {
          header("Location: enroll.php?status=success");
          exit();
      } else {
          echo "Error: " . htmlspecialchars($stmt->error); // Use htmlspecialchars for security
      }

      // Close the statement
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Enroll Patient</title>
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/enroll.css">
  </head>
  <body>
    <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
      <div class="container">
        <div class="card login-card">
          <div class="row no-gutters">
            <div class="col-md-5">
              <!-- Optional content or images -->
            </div>
            <div class="col-md-7">
              <div class="card-body">
                <p class="login-card-description">Patient Enrollment</p>

                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                  <div class="alert alert-success">
                    Data inserted successfully!
                  </div>
                <?php endif; ?>

                <form action="enroll.php" method="POST">
                  <div class="form-group">
                    <label for="patientname" class="sr-only">Patient Name</label>
                    <input type="text" name="patientname" id="patientname" class="form-control" placeholder="Patient Name" required>
                  </div>

                  <div class="form-group">
                    <label for="age" class="sr-only">Age</label>
                    <input type="number" name="age" id="age" class="form-control" placeholder="Age" required>
                  </div>

                  <div class="form-group">
                    <label for="gender" class="sr-only">Gender</label>
                    <select class="form-control" id="gender" name="gender" required>
                      <option value="disabled selected" >Select Gender</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </div>
                  
                  <button type="submit" name="enroll_button" class="btn btn-primary">Enroll</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  </body>
  </html>
