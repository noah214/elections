<?php
session_start();
require_once "../php/db_conn.php";
require_once "../php/add_logs.php";

// Check if user is in registration process
if (!isset($_SESSION['email']) || !isset($_SESSION['otp'])) {
    header("Location: register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $email = $_SESSION['email'];
    $stored_otp = $_SESSION['otp'];

    if ($otp == $stored_otp) {
        // Update user verification status
        $update = mysqli_query($conn, "UPDATE user_table SET verified = 1 WHERE email = '$email'");
        
        if ($update) {
            // Log successful verification
            $user_query = mysqli_query($conn, "SELECT username, fullname FROM user_table WHERE email = '$email'");
            $user_data = mysqli_fetch_assoc($user_query);
            $description = "Email verified for user: " . $user_data['fullname'];
            add_logs($conn, $user_data['username'], 'VERIFY_EMAIL: ' . $description);
            
            // Clear session variables
            unset($_SESSION['email']);
            unset($_SESSION['otp']);
            
            $_SESSION['success'] = "Email verified successfully! You can now login.";
            header("Location: login.php");
            exit();
        } else {
            // Log verification failure
            $user_query = mysqli_query($conn, "SELECT username, fullname FROM user_table WHERE email = '$email'");
            $user_data = mysqli_fetch_assoc($user_query);
            $description = "Failed to update verification status for: " . $user_data['fullname'];
            add_logs($conn, $user_data['username'], 'VERIFY_FAILED: ' . $description);
            
            $_SESSION['error'] = "Verification failed. Please try again.";
            header("Location: otp.php");
            exit();
        }
    } else {
        // Log invalid OTP
        $user_query = mysqli_query($conn, "SELECT username, fullname FROM user_table WHERE email = '$email'");
        $user_data = mysqli_fetch_assoc($user_query);
        $description = "Invalid OTP entered for: " . $user_data['fullname'];
        add_logs($conn, $user_data['username'], 'INVALID_OTP: ' . $description);
        
        $_SESSION['error'] = "Invalid OTP. Please try again.";
        header("Location: otp.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - BOTOmasino Elections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .verification-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .otp-input {
            letter-spacing: 0.5em;
            text-align: center;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-container">
            <h2 class="text-center mb-4">Email Verification</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input type="text" class="form-control otp-input" id="otp" name="otp" 
                           maxlength="6" pattern="[0-9]{6}" required 
                           placeholder="Enter 6-digit code">
                    <div class="form-text">Please enter the 6-digit code sent to your email.</div>
                </div>
                <button type="submit" class="btn btn-navy w-100">Verify Email</button>
            </form>
            
            <div class="text-center mt-3">
                <a href="register.php" class="text-decoration-none">Back to Registration</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Only allow numbers in OTP input
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html> 