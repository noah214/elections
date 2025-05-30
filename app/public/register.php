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
    
    // Debug information
    error_log("OTP Verification Attempt - Email: " . $user_email . ", Entered OTP: " . $entered_otp);
    error_log("Current register_stage: " . $_SESSION['register_stage']);
    
    $verify_query = "SELECT * FROM user_table WHERE email = '$user_email' AND otp = '$entered_otp'";
    $verify_result = $conn->query($verify_query);
    
    if ($verify_result->num_rows > 0) {
        error_log("OTP verification successful");
        
        // OTP is valid, update the user status and clear the OTP
        $updateQuery = "UPDATE user_table SET otp = NULL, status = 'Verified' WHERE email = '$user_email'";
        if (!$conn->query($updateQuery)) {
            error_log("Database error: " . $conn->error);
        }
        
        // Set register stage to completed
        $_SESSION['register_stage'] = 'completed';
        
        // Get the registration data
        if (!isset($_SESSION['register_data'])) {
            ?>
            <script>
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Registration Error",
                    showConfirmButton: false,
                    timer: 1500
                });
            </script>
            <?php
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
            ?>
            <script>
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Registration Failed",
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
                title: "Invalid OTP",
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        <?php
    }
}

// Handle initial registration
if (isset($_POST['register_submit'])) {
    $email = $_POST['email'];
    
    error_log("Initial registration attempt for email: " . $email);
    
    // Check if email already exists
    $check_query = "SELECT * FROM user_table WHERE email = '$email'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        error_log("Email already exists: " . $email);
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Email Already Exists',
                text: 'This email is already registered. Please use a different email or login.',
                confirmButtonColor: '#ffc107'
            });
        </script>";
    } else {
        error_log("Email is new, proceeding with registration");
        
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
        
        error_log("Registration data stored in session");
        
        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Store email and OTP in session
        $_SESSION['email'] = $email;
        $_SESSION['otp'] = $otp;
        
        error_log("OTP generated and stored: " . $otp);
        
        // Insert into database with Pending status and all user data
        $fullname = $_POST['fname'] . " " . $_POST['mname'] . " " . $_POST['lname'];
        $username = $_POST['username'];
        $password = md5($_POST['pass']);
        
        $insert_query = "INSERT INTO user_table (full_name, username, password, email, otp, status, role) 
                        VALUES ('$fullname', '$username', '$password', '$email', '$otp', 'Pending', 'Voter')";
        
        if ($conn->query($insert_query)) {
            error_log("Initial user record created successfully");
            
            // Send OTP email using send_emailverification function
            if(send_emailverification($fullname, $email, $otp)) {
                error_log("OTP email sent successfully");
                $_SESSION['register_stage'] = 'otp';
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Sent!',
                        text: 'Please check your email for the verification code.',
                        confirmButtonColor: '#ffc107'
                    });
                </script>";
            } else {
                error_log("Failed to send OTP email");
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Error',
                        text: 'Failed to send OTP. Please try again.',
                        confirmButtonColor: '#ffc107'
                    });
                </script>";
            }
        } else {
            error_log("Failed to create initial user record: " . $conn->error);
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#ffc107'
                });
            </script>";
        }
    }
}

// Handle final registration after OTP verification
if (isset($_POST['verify_otp']) && $_SESSION['register_stage'] == 'completed') {
    $register_data = $_SESSION['register_data'];
    
    // Insert into user_table
    $fullname = $register_data['fname'] . " " . $register_data['mname'] . " " . $register_data['lname'];
    $insert_user = "INSERT INTO user_table (full_name, username, password, email, status) 
                    VALUES ('$fullname', '{$register_data['username']}', '{$register_data['password']}', '{$register_data['email']}', 'Verified')";
    
    // Insert into voter_table
    $insert_voter = "INSERT INTO voter_table (voter_name, date_of_birth, gender, contact_information, student_id) 
                     VALUES ('$fullname', '{$register_data['date_birth']}', '{$register_data['gender']}', '{$register_data['contact']}', '{$register_data['stu_id']}')";
    
    if ($conn->query($insert_user) && $conn->query($insert_voter)) {
        // Clear session data
        unset($_SESSION['register_data']);
        unset($_SESSION['register_stage']);
        unset($_SESSION['email']);
        unset($_SESSION['otp']);
        
        // Log account verification
        $description = "Account verified for user: " . $register_data['username'];
        logActivity($conn, $register_data['username'], 'VERIFY', $description);
        
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Complete!',
                text: 'Your account has been created successfully.',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location.href = 'login.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: 'An error occurred while creating your account.',
                confirmButtonColor: '#ffc107'
            });
        </script>";
    }
}
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 border">
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
                </div>
            
                <!-- Registration Form -->
                <?php if ($_SESSION['register_stage'] == 'register_account'){ ?>
                    <form action="" method="post">
                      

                        <div class="row mt-4 mb-1">
                            <div class="col d-flex justify-content-center">
                                <h1>Register</h1>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col d-flex justify-content-center">
                                <h6>Be a voter today!</h6>
                            </div>
                        </div>
                    <hr>
                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="tel" name="stu_id" id="stu_id" class="form-control" placeholder=" "
                                    pattern="^\d{10}$" required> <!-- only accepts 10 digit student id -->
                                    <label for="stu_id" class="form-label">Student ID</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3">
                            <div class="col-8">
                                <div class="form-floating">
                                    <input type="text" name="fname" id="fname" class="form-control" placeholder=" " required>
                                    <label for="fname" class="form-label">First Name</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="mname" id="mname" class="form-control" placeholder=" ">
                                    <label for="mname" class="form-label">Middle Name</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="lname" id="lname" class="form-control" placeholder=" " required>
                                    <label for="lname" class="form-label">Last Name</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="email" name="email" id="email" class="form-control" placeholder=" " 
                                    pattern="^[a-z]+\.[a-z]+\.[a-z]+@ust\.edu\.ph$" required> <!-- only accepts ust formatted emails -->
                                    <label for="email" class="form-label">Email</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="date" name="date_birth" id="date_birth" class="form-control" placeholder=" " required>
                                    <label for="date_birth" class="form-label">Date of Birth</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3">
                            <div class="col-6">
                                <select name="gender" class="form-select" id="gender" required>
                                    <option disabled selected>Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="tel" name="contact" id="contact" class="form-control" placeholder=" "
                                    pattern="^[0-9]{11}$" required>
                                    <label for="contact" class="form-label">Contact Information</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" name="username" id="username" class="form-control" placeholder=" " required>
                                    <label for="username" class="form-label">Username</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 mt-3 mb-5">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="password" name="pass" id="pass" class="form-control" placeholder=" " required>
                                    <label for="pass" class="form-label">Password</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-5 my-5">
                            <div class="col">   
                                <input type="submit" name="register_submit" class="btn btn-primary btn-block w-100 fw-bold" value="Register User" id="sub">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col bg-dark block">
                                <!-- Footer Section Black -->
                            </div>
                        </div>
                    </form>
                <?php
                    } elseif ($_SESSION['register_stage'] == 'otp' || isset($_GET['reset'])) { 
                ?>
                    <!--Send OTP Form -->
                    <form action="" method="post">
                        
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
                        <div class="row">
                            <div class="col bg-dark block"></div>
                        </div>
                    </form>
                <?php
                    }  //End of Register Stages
                ?>
            </div>
            <div class="col d-flex justify-content-center flex-column align-items-center">
                <div>
                    <h1 class="text-warning">University of Santo Tomas</h1>
                </div>
                <div>
                    <h3>Supreme Student Council:</h3>
                </div>
                <div>
                    <h3>BOTOmasino Elections</h3>
                </div>
            </div>  
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    </body>
</html>