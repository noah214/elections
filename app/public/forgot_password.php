<?php
session_start();
require_once "db_conn.php";
require_once "../public/logic/emailverification.php";

//activates if user clicks resend otp
// Handle reset request
if (isset($_GET['reset'])) {
    unset($_SESSION['forgot_stage']);
    unset($_SESSION['reset_email']);
    header("Location: forgot_password.php");
    exit();
}

// Initialize stage
if (!isset($_SESSION['forgot_stage'])) {
    $_SESSION['forgot_stage'] = 'email';
}

//stage 1

// Handle form submissions
if (isset($_POST['email_submit'])) {
    $email = $_POST['email'];
    
    // Check if email exists in database
    $check_email = "SELECT * FROM user_table WHERE email = '$email'";
    $result = $conn->query($check_email);
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['reset_email'] = $email;
        
        // Generate OTP
        $otp = rand(000000, 999999);
        
        // Update OTP in database
        $update_otp = "UPDATE user_table SET otp = '$otp' WHERE user_id = '{$user['user_id']}'";
        $conn->query($update_otp);
        
        // Send OTP via email
        send_verification($user['full_name'], $email, $otp);
        
        $_SESSION['forgot_stage'] = 'otp';
        
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "OTP sent to your email!",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    } else {
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Email not found!",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    }
}
//stage 2

if (isset($_POST['verify_otp'])) {
    $otp = $_POST['otp'];
    $email = $_SESSION['reset_email'];

    // Verify OTP
    $verify_sql = "SELECT * FROM user_table WHERE email = '$email' AND otp = '$otp'";
    $result = $conn->query($verify_sql);
    
    if($result->num_rows > 0) {
        $_SESSION['verified_email'] = $email;
        $_SESSION['forgot_stage'] = 'reset';
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "OTP Verified Successfully!",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    } else {
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Invalid OTP!",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    }
}

//stage 3

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 15px;
            position: relative;
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
            width: 30px;
            height: 2px;
            background-color: #e9ecef;
            top: 50%;
            right: -30px;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar" id="mainNavbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.css">
                <img src="" width="30" height="30" class="d-inline-block align-top me-2" alt="SSC Logo">
                UST Supreme Student Council
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-item nav-link" href="home.php" aria-current="page">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="vote.php">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link active" href="account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid ust-bg">
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
                    <!--Yello block design !-->
                     <div class="row">
                            <div class="col bg-warning block"></div>
                    </div>
                    <!-- Step Indicator -->
                    <div class="step-indicator mt-4">
                        <div class="step <?php echo $_SESSION['forgot_stage'] == 'email' ? 'active' : ($_SESSION['forgot_stage'] == 'otp' || $_SESSION['forgot_stage'] == 'reset' ? 'completed' : ''); ?>">
                            1
                            <div class="step-line <?php echo $_SESSION['forgot_stage'] == 'otp' || $_SESSION['forgot_stage'] == 'reset' ? 'completed' : ''; ?>"></div>
                        </div>
                        <div class="step <?php echo $_SESSION['forgot_stage'] == 'otp' ? 'active' : ($_SESSION['forgot_stage'] == 'reset' ? 'completed' : ''); ?>">
                            2
                            <div class="step-line <?php echo $_SESSION['forgot_stage'] == 'reset' ? 'completed' : ''; ?>"></div>
                        </div>
                        <div class="step <?php echo $_SESSION['forgot_stage'] == 'reset' ? 'active' : ''; ?>">
                            3
                        </div>
                    </div>

                    <?php if ($_SESSION['forgot_stage'] == 'email'): ?>
                    <!-- Email Form -->
                    <form action="" method="post">
                       
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <h1>Forgot Password</h1>
                                <p class="text-muted">Enter your email address to receive a verification code</p>
                            </div>
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="email" name="email" id="email" class="form-control border-secondary" placeholder=" " required>
                                    <label for="email" class="form-label">Email Address</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mx-5 mt-5 mb-1">
                            <div class="col">   
                                <input type="submit" name="email_submit" class="btn btn-primary btn-block w-100 fw-bold" value="Send OTP">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <p>Remember your password? <a href="login.php">Login Here!</a></p>
                            </div>
                        </div>
                        
                    </form>

                    <?php elseif ($_SESSION['forgot_stage'] == 'otp'): ?>
                    <!-- OTP Verification Form -->
                    <form action="" method="post">
                        <div class="row">
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <h1>Verify OTP</h1>
                                <p class="text-muted">Enter the verification code sent to your email</p>
                            </div>
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="otp" id="otp" class="form-control border-secondary" placeholder=" " required>
                                    <label for="otp" class="form-label">Enter OTP</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mx-5 mt-5 mb-1">
                            <div class="col">   
                                <input type="submit" name="verify_otp" class="btn btn-primary btn-block w-100 fw-bold" value="Verify OTP">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <p>Didn't receive OTP? <a href="?reset=1">Try Again</a></p>
                            </div>
                        </div>
                    </form>

                    <?php elseif ($_SESSION['forgot_stage'] == 'reset'): ?>
                    <!-- Reset Password Form -->
                    <form action="" method="post">
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <h1>Reset Password</h1>
                                <p class="text-muted">Enter your new password</p>
                            </div>
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="password" name="new_password" id="new_password" class="form-control border-secondary" placeholder=" " required>
                                    <label for="new_password" class="form-label">New Password</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control border-secondary" placeholder=" " required>
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mx-5 mt-5 mb-1">
                            <div class="col">   
                                <input type="submit" name="reset_password" class="btn btn-primary btn-block w-100 fw-bold" value="Reset Password">
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                    <!--Dark block design !-->
                    <!--Outside of the multiple forms !-->
                    <div class="row">
                            <div class="col bg-dark block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

<?php
// Handle password reset
if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['verified_email'];

    if ($new_password === $confirm_password) {
        // Update password in database
        $hashed_password = md5($new_password);
        $update_password = "UPDATE user_table SET password = '$hashed_password', otp = NULL WHERE email = '$email'";
        
        if ($conn->query($update_password)) {
            // Clear session data
            unset($_SESSION['forgot_stage']);
            unset($_SESSION['reset_email']);
            unset($_SESSION['verified_email']);
            
            ?>
            <script>
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Password reset successful!",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = "login.php";
                });
            </script>
            <?php
        } else {
            ?>
            <script>
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Password reset failed!",
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>
            <?php
        }
    } else {
        ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Passwords do not match!",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    }
}
?> 