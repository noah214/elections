<?php
session_start();
require_once "db_conn.php";
require_once "../includes/emailverification.php";

// Check if user is logged in or has a pending verification
if (!isset($_SESSION['user_id']) && !isset($_SESSION['pending_verification'])) {
    header("Location: login.php");
    exit();
}

// Initialize stage
if (!isset($_SESSION['verification_stage'])) {
    $_SESSION['verification_stage'] = 'send';
}

// Get user information
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $get_user = "SELECT * FROM user_table WHERE user_id = '$user_id'";
    $result = $conn->query($get_user);
    $user = $result->fetch_assoc();
    $email = $get_user['email'];
  
} 

//stage 1 - Send OTP

// Handle send OTP request
if (isset($_POST['send_otp']) || $_SESSION['verification_stage'] == 'send') {
    
    // Generate OTP
    $otp = rand(100000, 999999);
    
    // Update OTP in database
    $update_otp = "UPDATE user_table SET otp = '$otp' WHERE user_id = '$user_id'";
    $conn->query($update_otp);
    
    // Send OTP via email
    send_verification($full_name, $email, $otp);
    
    $_SESSION['verification_stage'] = 'verify';
    $_SESSION['verification_email'] = $email;
    
    if (isset($_POST['send_otp'])) {
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Verification code sent to your email!",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    }
}

//stage 2 - Verify OTP

if (isset($_POST['verify_otp'])) {
    $otp = $_POST['otp'];
    
    // Verify OTP
    $verify_sql = "SELECT * FROM user_table WHERE user_id = '$user_id' AND otp = '$otp'";
    $result = $conn->query($verify_sql);
    
    if($result->num_rows > 0) {
        // Mark email as verified
        $verify_email_sql = "UPDATE user_table SET status = 'Active', otp = NULL WHERE user_id = '$user_id'";
        $conn->query($verify_email_sql);
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Email Verified Successfully!",
                text: "Your account is now activated.",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "dashboard.php";
            });
        </script>
        <?php
    } else {
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Invalid verification code!",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    }
}

// Handle resend request
if (isset($_GET['resend'])) {
    $_SESSION['verification_stage'] = 'send';
    header("Location: emailverify.php");
    exit();
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 20px;
            position: relative;
            font-weight: bold;
        }
        .step.active {
            background-color: #0d6efd;
            color: white;
        }
        .step.completed {
            background-color: #198754;
            color: white;
        }
        .step-line {
            position: absolute;
            width: 40px;
            height: 2px;
            background-color: #e9ecef;
            top: 50%;
            right: -40px;
        }
        .step-line.active {
            background-color: #0d6efd;
        }
        .step-line.completed {
            background-color: #198754;
        }
        .step:last-child .step-line {
            display: none;
        }
        .verification-icon {
            font-size: 4rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .email-display {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid login-banner">
        <div class="row h-100">
            <div class="col-5 d-flex justify-content-center flex-column align-items-center">
                <div>
                    <h1 class="text-warning display-5 fw-bold">University of Santo Tomas</h1>
                </div>
                <div class="text-white">
                    <h2>Supreme Student Council:</h2>
                </div>
                <div class="text-white">
                    <h2>BOTOmasino Elections</h2>
                </div>
            </div>
            <div class="col d-flex align-items-center">
                <div class="bg-white w-75 mx-auto shadow">
                    <!-- Step Indicator -->
                    <div class="step-indicator mt-4">
                        <div class="step <?php echo $_SESSION['verification_stage'] == 'send' ? 'active' : 'completed'; ?>">
                            ✉️
                            <div class="step-line <?php echo $_SESSION['verification_stage'] == 'verify' ? 'completed' : ''; ?>"></div>
                        </div>
                        <div class="step <?php echo $_SESSION['verification_stage'] == 'verify' ? 'active' : ''; ?>">
                            ✓
                        </div>
                    </div>

                    <?php if ($_SESSION['verification_stage'] == 'send'): ?>
                    <!-- Send OTP Stage -->
                    <form action="" method="post">
                        <div class="row">
                            <div class="col bg-warning block"></div>
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col text-center">
                                <div class="verification-icon">📧</div>
                                <h1>Email Verification Required</h1>
                                <p class="text-muted">We need to verify your email address to activate your account</p>
                                <div class="email-display">
                                    📧 <?php echo htmlspecialchars($email); ?>
                                </div>
                                <p class="text-muted small">A verification code will be sent to this email address</p>
                            </div>
                        </div>
                        <div class="row mx-5 mt-4 mb-1">
                            <div class="col">   
                                <input type="submit" name="send_otp" class="btn btn-primary btn-block w-100 fw-bold py-3" value="Send Verification Code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <p>Wrong email? <a href="profile.php">Update Email Address</a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col bg-dark block"></div>
                        </div>
                    </form>

                    <?php elseif ($_SESSION['verification_stage'] == 'verify'): ?>
                    <!-- OTP Verification Form -->
                    <form action="" method="post">
                        <div class="row">
                            <div class="col bg-warning block"></div>
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col text-center">
                                <div class="verification-icon">🔐</div>
                                <h1>Enter Verification Code</h1>
                                <p class="text-muted">We've sent a 6-digit verification code to:</p>
                                <div class="email-display">
                                    📧 <?php echo htmlspecialchars($email); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="otp" id="otp" class="form-control border-secondary text-center" 
                                           placeholder=" " required maxlength="6" pattern="[0-9]{6}" 
                                           style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                                    <label for="otp" class="form-label">Enter 6-digit code</label>
                                </div>
                                <small class="text-muted">Check your spam folder if you don't see the email</small>
                            </div>
                        </div>
                        <div class="row mx-5 mt-4 mb-1">
                            <div class="col">   
                                <input type="submit" name="verify_otp" class="btn btn-success btn-block w-100 fw-bold py-3" value="Verify Email Address">
                            </div>
                        </div>
                        <div class="row mx-5 mt-2">
                            <div class="col text-center">
                                <p class="mb-2">Didn't receive the code?</p>
                                <a href="?resend=1" class="btn btn-outline-primary">Resend Verification Code</a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col d-flex justify-content-center">
                                <p><a href="logout.php">Use Different Account</a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col bg-dark block"></div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Auto-focus on OTP input and format it
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
                
                // Format OTP input (numbers only)
                otpInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length > 6) {
                        this.value = this.value.slice(0, 6);
                    }
                });
                
                // Auto-submit when 6 digits are entered
                otpInput.addEventListener('input', function(e) {
                    if (this.value.length === 6) {
                        // Optional: Auto-submit after a short delay
                        setTimeout(() => {
                            if (this.value.length === 6) {
                                this.form.submit();
                            }
                        }, 500);
                    }
                });
            }
        });
    </script>
</body>
</html>