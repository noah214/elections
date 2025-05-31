<?php
session_start();
require_once "db_conn.php";
require_once "../public/logic/emailverification.php";
require_once "../public/functions.php";

if (!isset($_SESSION['register_stage'])) {
    $_SESSION['register_stage'] = 'register_account';
}

// Handle OTP verification
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $user_email = $_SESSION['email'];
    
    $verify_query = "SELECT * FROM user_table WHERE email = '$user_email' AND otp = '$entered_otp'";
    $verify_result = $conn->query($verify_query);
    
    if ($verify_result->num_rows > 0) {
        // OTP is valid, update the user status and clear the OTP
        $updateQuery = "UPDATE user_table SET otp = NULL, status = 'Verified' WHERE email = '$user_email'";
        $conn->query($updateQuery);
        
        // Set register stage to completed
        $_SESSION['register_stage'] = 'completed';
        
        // Get the registration data
        if (!isset($_SESSION['register_data'])) {
            $_SESSION['error_message'] = "Registration Error";
            header("Location: register.php");
            exit();
        }
        
        $register_data = $_SESSION['register_data'];
        
        // Insert into voter_table
        $fullname = $register_data['fname'] . " " . $register_data['mname'] . " " . $register_data['lname'];
        $insert_voter = "INSERT INTO voter_table (voter_name, date_of_birth, gender, contact_information, student_id) 
                         VALUES ('$fullname', '{$register_data['date_birth']}', '{$register_data['gender']}', '{$register_data['contact']}', '{$register_data['stu_id']}')";
        
        $voter_result = $conn->query($insert_voter);
        
        if ($voter_result) {
            // Clear session data
            unset($_SESSION['register_data']);
            unset($_SESSION['register_stage']);
            unset($_SESSION['email']);
            unset($_SESSION['otp']);
            
            $_SESSION['registration_success'] = true;
            
            // Log account verification
            $description = "Account verified for user: " . $register_data['username'];
            logActivity($conn, $register_data['username'], 'VERIFY', $description);
            
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Registration Failed";
            header("Location: register.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Invalid OTP";
        header("Location: register.php");
        exit();
    }
}

// Handle initial registration
if (isset($_POST['register_submit'])) {
    $email = $_POST['email'];
    
    // Check if email already exists
    $check_query = "SELECT * FROM user_table WHERE email = '$email'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $_SESSION['error_message'] = "Email Already Exists";
        header("Location: register.php");
        exit();
    } else {
        // Store all form data in session for later use
        $_SESSION['register_data'] = array(
            'stu_id' => $_POST['stu_id'],
            'fname' => $_POST['fname'],
            'mname' => $_POST['mname'],
            'lname' => $_POST['lname'],
            'email' => $_POST['email'],
            'date_birth' => $_POST['date_birth'],
            'gender' => $_POST['gender'],
            'contact' => $_POST['contact'],
            'username' => $_POST['username'],
            'password' => md5($_POST['pass'])
        );
        
        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Store email and OTP in session
        $_SESSION['email'] = $email;
        $_SESSION['otp'] = $otp;
        
        // Insert into database with Pending status and all user data
        $fullname = $_POST['fname'] . " " . $_POST['mname'] . " " . $_POST['lname'];
        $username = $_POST['username'];
        $password = md5($_POST['pass']);
        
        $insert_query = "INSERT INTO user_table (full_name, role, username, password, email, otp, status) 
                        VALUES ('$fullname', 'Voter', '$username', '$password', '$email', '$otp', 'Pending')";
        
        if ($conn->query($insert_query)) {
            // Send OTP email using send_emailverification function
            if(send_emailverification($fullname, $email, $otp)) {
                $_SESSION['register_stage'] = 'otp';
                $_SESSION['success_message'] = "OTP Sent! Please check your email for the verification code.";
                header("Location: register.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Failed to send OTP. Please try again.";
                header("Location: register.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Registration Failed. Please try again.";
            header("Location: register.php");
            exit();
        }
    }
}

// Add this before the HTML output
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Clear the messages after retrieving them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - BOTOmasino Elections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/register.css">
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
    <div class="container-fluid ust-bg">
        <div class="row">
            <div class="col-6 border bg-white">
            <div class="row">
                            <div class="col bg-warning block mb-4">
                                <!--Yellow Block at Top -->
                            </div> 
                        </div>

                <!-- Step Indicator -->
                <div class="step-indicator mt-4">
                    <div class="step <?php echo $_SESSION['register_stage'] == 'register_account' ? 'active' : ($_SESSION['register_stage'] == 'otp' || $_SESSION['register_stage'] == 'completed' ? 'completed' : ''); ?>">
                        1
                        <div class="step-line <?php echo $_SESSION['register_stage'] == 'otp' || $_SESSION['register_stage'] == 'reset' ? 'completed' : ''; ?>"></div>
                    </div>
                    <div class="step <?php echo $_SESSION['register_stage'] == 'otp' ? 'active' : ($_SESSION['register_stage'] == 'reset' ? 'completed' : ''); ?>">
                        2
                        <div class="step-line <?php echo $_SESSION['register_stage'] == 'reset' ? 'completed' : ''; ?>"></div>
                    </div>
                    <div class="step <?php echo $_SESSION['register_stage'] == 'completed' ? 'active' : ''; ?>">
                        3
                    </div>
                </div>

                <?php if ($_SESSION['register_stage'] == 'register_account') { ?>
                    <!-- Registration Form -->
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname" required>
                                <div class="invalid-feedback">
                                    Please enter your first name.
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="mname" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="mname" name="mname" required>
                                <div class="invalid-feedback">
                                    Please enter your middle name.
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname" required>
                                <div class="invalid-feedback">
                                    Please enter your last name.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stu_id" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="stu_id" name="stu_id" required>
                                <div class="invalid-feedback">
                                    Please enter your student ID.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_birth" name="date_birth" required>
                                <div class="invalid-feedback">
                                    Please enter your date of birth.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select your gender.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Information</label>
                            <input type="tel" class="form-control" id="contact" name="contact" required>
                            <div class="invalid-feedback">
                                Please enter your contact information.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <div class="invalid-feedback">
                                    Please choose a username.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pass" class="form-label">Password</label>
                                <input type="password" class="form-control" id="pass" name="pass" required>
                                <div class="invalid-feedback">
                                    Please enter a password.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-warning" type="submit" name="register_submit">Register</button>
                        </div>
                    </form>
                <?php } else if ($_SESSION['register_stage'] == 'otp') { ?>
                    <!-- OTP Verification Form -->
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" required>
                            <div class="invalid-feedback">
                                Please enter the OTP sent to your email.
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-warning" type="submit" name="verify_otp">Verify OTP</button>
                        </div>
                    </form>
                <?php } ?>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-center">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bold mb-4">Welcome to <span class="text-yellow">BOTO</span>masino Elections</h1>
                    <p class="lead">Join us in shaping the future of UST through democratic elections.</p>
                    <a href="login.php" class="btn btn-warning mt-3">Already have an account? Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>

    <?php if ($success_message): ?>
    <script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "<?= $success_message ?>",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <script>
        Swal.fire({
            position: "center",
            icon: "error",
            title: "<?= $error_message ?>",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php endif; ?>
  </body>
</html>