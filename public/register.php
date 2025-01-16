<?php
// /public/register.php

require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/EmailVerification.php';
require_once '../classes/User.php';
require_once '../vendor/autoload.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize Database connection
$database = new Database();
$db = $database->getConnection();

// Initialize EmailVerification object
$emailVerification = new EmailVerification($db);

// Check if token is provided
if(isset($_GET['token'])) {
    $token = $_GET['token'];

    if($emailVerification->verifyToken($token)) {
        // Token is valid, proceed to display registration form
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Handle form submission
            $name = $_POST['name'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];

            // Basic validation
            if(empty($name) || empty($password) || empty($confirm_password)) {
                $error = "All fields are required.";
            } elseif($password !== $confirm_password) {
                $error = "Passwords do not match.";
            } else {
                // Retrieve email associated with the token
        $query = "SELECT email FROM email_verification WHERE token = :token LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

                if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $email = $row['email'];

                        // Create User object
                        $user = new User($db);
                        $user->email = $email;
                        $user->name = $name;
                    $user->password = $password;
                    $user->login_code = $user->generateLoginCode();
                    $user->phone = $phone;
                    $user->address = $address;
                        $user->created_at = date('Y-m-d H:i:s');
                        
                    if($user->create()) {
                            // Send the 6-digit login code via email
                            $mail = new PHPMailer(true);

                            try {
                                // Server settings
                            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server
                                $mail->SMTPAuth = true;
                                $mail->Username = 'mohith.nakka1976@gmail.com'; // SMTP username
                                $mail->Password = 'mprl adum kqwa mvyw'; // SMTP password
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = 587;

                                // Recipients
                                $mail->setFrom('mohith.nakka1976@gmail.com', 'Sanjeevani+');
                                $mail->addAddress($user->email, $user->name);

                                // Content
                                $mail->isHTML(true);
                                $mail->Subject = 'Your 6-Digit Login Code';
                            $mail->Body    = "Hello $user->name,<br><br>Your login code is: <strong>$user->login_code</strong><br><br>This code will be used to log in to your account.<br><br>Regards,<br>Your App Team";

                                $mail->send();
                                $success = "Registration successful! A login code has been sent to your email.";
                            } catch (Exception $e) {
                                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                            }
                        } else {
                            $error = "Unable to register. Please try again.";
                        }
                } else {
                    $error = "Invalid token.";
                }
            }
        }
    } else {
        $error = "This registration link has expired or is invalid.";
    }
} else {
    $error = "No token provided.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Register</title>
  <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/register.css">
</head>
<body>
  <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
    <div class="container">
      <div class="card login-card">
        <div class="row no-gutters">
            <div class="col-md-5">
            <!-- You can add an image or any content here if needed -->
          </div>
          <div class="col-md-7">
            <div class="card-body">
              <p class="login-card-description">Register Now!!!</p>
              <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

    <?php if(!isset($success) && isset($token) && $emailVerification->verifyToken($token)): ?>
        <form method="POST" action="register.php?token=<?php echo htmlspecialchars($token); ?>">

                  
                  <div class="form-group1">
                    <label for="name" class="sr-only">Hospital Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Hospital Name" required >
                  </div>  

                  <div class="form-group">
                    <label for="address" class="sr-only">Address</label>3
                     <textarea class="form-control" id="address" name="address" rows="3" placeholder="Address" required></textarea>
                  </div>
                    <div class="form-group12">
                      <label for="password" class="sr-only">Password</label>
                      <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    </div>
                    
                    <div class="form-group23">
                      <label for="confirm_password" class="sr-only">Re-Password</label>
                      <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-Password" required>
                    </div>
                    <div class="form-group13">
                      <label for="phone" class="sr-only">Phone Number</label>
                      <input type="tel" name="phone" id="phone" class="form-control" placeholder="Phone number" required pattern="[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}[0-9]{1}">
                    </div>  
                    
                    <button type="submit" class="registor_button" id="registor_button"><span>Register</span></button>
                
                </form>
                        <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  
  <script >
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
    // Get the values of the form fields
    var password = document.getElementById('password').value;
    var repassword = document.getElementById('repassword').value;
    var phone = document.getElementById('phone').value;

    // Check if passwords match
    if (password !== repassword) {
        alert('Passwords do not match.');
        event.preventDefault(); // Prevent form submission
        return; // Exit the function early
    }

    // Phone number validation (basic example, adjust as needed)
    var phonePattern = /^[0-9]{10}$/; // Adjust the pattern according to your needs
    if (!phonePattern.test(phone)) {
        alert('Please enter a valid 10-digit phone number.');
        event.preventDefault(); // Prevent form submission
        return; // Exit the function early
    }

    // Add additional validation logic as needed
  });
  </script>
</body>
</html>
