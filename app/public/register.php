<?php
session_start();
require_once "../php/db_conn.php";
require_once "../php/add_logs.php";
require_once "../php/emailverification.php";


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
        // Get the user_id
        $user_data = $verify_result->fetch_assoc();
        $user_id = $user_data['user_id'];
        
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
            add_logs($conn, $user_id, 'ACCOUNT VERIFIED');
            
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
            // Get the user_id of the newly created user
            $user_id = $conn->insert_id;
            
            // Log successful registration
            add_logs($conn, $user_id, 'ACCOUNT REGISTERED');
            
            // Send OTP for email verification
            $result = send_emailverification($email, $otp);
            
            if ($result) {
                // Log OTP sent
                add_logs($conn, $user_id, 'OTP SENT');
                
                $_SESSION['success'] = "OTP Sent! Please check your email for the verification code.";
                $_SESSION['email'] = $email;
                $_SESSION['otp'] = $otp;
                $_SESSION['register_stage'] = 'otp';  // Set stage to OTP verification
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
                // Log OTP send failure
                add_logs($conn, $user_id, 'OTP FAILED');
                
                // Delete the user record since email verification failed
                $delete = mysqli_query($conn, "DELETE FROM user_table WHERE email = '$email'");
                $_SESSION['error'] = "Failed to send OTP. Please try again later.";
                header("Location: register.php");
                exit();
            }
        } else {
            // Log registration failure
            add_logs($conn, 0, 'REGISTER FAILED');
            
            $_SESSION['error'] = "Registration failed. Please try again.";
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
                    <h2><span class="text-warning">BOTO</span>masino Elections</h2>
                </div>
            </div>
            
            <div class="col d-flex align-items-center">
                <div class="bg-white w-75 mx-auto shadow">
                    <!--Yellow block design !-->
                    <div class="row">
                        <div class="col bg-warning block"></div>
                    </div>

                    <!-- Step Indicator -->
                    <div class="step-indicator mt-4">
                        <div class="step <?php echo $_SESSION['register_stage'] == 'register_account' ? 'active' : ($_SESSION['register_stage'] == 'otp' || $_SESSION['register_stage'] == 'completed' ? 'completed' : ''); ?>">
                            1
                            <div class="step-line <?php echo $_SESSION['register_stage'] == 'otp' || $_SESSION['register_stage'] == 'completed' ? 'completed' : ''; ?>"></div>
                        </div>
                        <div class="step <?php echo $_SESSION['register_stage'] == 'otp' ? 'active' : ($_SESSION['register_stage'] == 'completed' ? 'completed' : ''); ?>">
                            2
                        </div>
                    </div>

                    <?php if ($_SESSION['register_stage'] == 'register_account') { ?>
                        <!-- Registration Form -->
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <h1>Create Account</h1>
                                    <p class="text-muted">Fill in your details to register</p>
                                </div>
                            </div>

                            <div class="row mx-5 mt-3">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-secondary" id="fname" name="fname" placeholder=" " required>
                                        <label for="fname">First Name</label>
                                        <div class="invalid-feedback">Please enter your first name.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-secondary" id="mname" name="mname" placeholder=" " required>
                                        <label for="mname">Middle Name</label>
                                        <div class="invalid-feedback">Please enter your middle name.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-secondary" id="lname" name="lname" placeholder=" " required>
                                        <label for="lname">Last Name</label>
                                        <div class="invalid-feedback">Please enter your last name.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-5">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-secondary" id="stu_id" name="stu_id" placeholder=" " required>
                                        <label for="stu_id">Student ID</label>
                                        <div class="invalid-feedback">Please enter your student ID.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control border-secondary" id="email" name="email" placeholder=" " required>
                                        <label for="email">Email</label>
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-5">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control border-secondary" id="date_birth" name="date_birth" placeholder=" " required>
                                        <label for="date_birth">Date of Birth</label>
                                        <div class="invalid-feedback">Please enter your date of birth.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select border-secondary" id="gender" name="gender" required>
                                            <option value="">Select gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <label for="gender">Gender</label>
                                        <div class="invalid-feedback">Please select your gender.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-5">
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="tel" class="form-control border-secondary" id="contact" name="contact" placeholder=" " required>
                                        <label for="contact">Contact Information</label>
                                        <div class="invalid-feedback">Please enter your contact information.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-5">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control border-secondary" id="username" name="username" placeholder=" " required>
                                        <label for="username">Username</label>
                                        <div class="invalid-feedback">Please choose a username.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control border-secondary" id="pass" name="pass" placeholder=" " required>
                                        <label for="pass">Password</label>
                                        <div class="invalid-feedback">Please enter a password.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-5 mt-5 mb-1">
                                <div class="col">   
                                    <input type="submit" name="register_submit" class="btn btn-primary btn-block w-100 fw-bold" value="Register">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex justify-content-center">
                                    <p>Already have an account? <a href="login.php">Login Here!</a></p>
                                </div>
                            </div>
                        </form>
                    <?php } else if ($_SESSION['register_stage'] == 'otp') { ?>
                        <!-- OTP Verification Form -->
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <h1>Verify OTP</h1>
                                    <p class="text-muted">Enter the verification code sent to your email</p>
                                </div>
                            </div>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-secondary" id="otp" name="otp" placeholder=" " required>
                                        <label for="otp">Enter OTP</label>
                                        <div class="invalid-feedback">Please enter the OTP sent to your email.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-5 mt-5 mb-1">
                                <div class="col">   
                                    <input type="submit" name="verify_otp" class="btn btn-primary btn-block w-100 fw-bold" value="Verify OTP">
                                </div>
                            </div>
                        </form>
                    <?php } ?>

                    <!--Dark block design !-->
                    <div class="row">
                        <div class="col bg-dark block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </body>
</html>