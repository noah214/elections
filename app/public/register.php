<?php
session_start();
require_once "db_conn.php";
require_once "../includes/emailverification.php";

if (!isset($_SESSION['register_stage'])) {
    $_SESSION['register_stage'] = 'register_account' ?? 'register_account'; // Initialize register stage
}

if (isset($_POST['register_submit'])) {
    $email = $_POST['email'];
    $_SESSION['email'] = $email; // Store email in session for OTP verification
    
    $check_email = "SELECT * FROM user_table WHERE email = '$email'";
    $result = $conn->query($check_email);
    
    if($result->num_rows > 0) {
        echo 'email already exists';
    } else {
        $fname = $_POST['fname'];
        $mname = $_POST['mname'];
        $lname = $_POST['lname'];
        $name = "$fname $mname $lname";
        $role = "Voter";
        $username = $_POST['username'];
        $password = md5($_POST['pass']);
        $otp = rand(100000, 999999);
        $status = 'Pending';

        $insertQuery = "INSERT INTO user_table (full_name, role, username, password, email, otp, status) 
                        VALUES ('$name', '$role', '$username', '$password', '$email', $otp, '$status')";
        $resultUser = mysqli_query($conn, $insertQuery);


        //voter information
        $voterDateBirth = $_POST['date_birth'];
        $voterGender = $_POST['gender'];
        $voterContact = $_POST['contact'];
        $voterStuId = $_POST['stu_id'];
        
        $insertVoterQuery = "INSERT INTO voter_table (voter_name, date_of_birth, gender, contact_information, student_id) 
                            VALUES ('$name', '$voterDateBirth', '$voterGender', '$voterContact', '$voterStuId')";
       $resultVoter = mysqli_query($conn, $insertVoterQuery);

       //put otp in session so that it can be used in the next step
       $_SESSION['otp'] = $otp;

        send_emailverification($name, $email, $otp);
        
        // Set the session stage to otp for the next step
        $_SESSION['register_stage'] = 'otp';
        
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
    }
}




?>

<!-- The rest of the HTML (form and structure) stays unchanged -->


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
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
   <!-- <link rel="stylesheet" href="https://use.typekit.net/xxx6zlw.css"> FONT po ito-->
    
  </head>
  <body >
    <nav class="navbar navbar-expand-lg custom-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="your-logo.svg" width="30" height="30" class="d-inline-block align-top" alt="SSC Logo">
                    UST Supreme Student Council
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link" href="home.php">Home</a>
                        <div class="vr mx-2 d-none d-lg-block"></div>
                        <a class="nav-item nav-link" href="candidate.php">Candidates</a>
                        <div class="vr mx-2 d-none d-lg-block"></div>
                        <a class="nav-item nav-link" href="vote.php">Vote</a>
                        <div class="vr mx-2 d-none d-lg-block"></div>
                        <a class="nav-item nav-link active" href="#" aria-current="page">Account</a>
                    </div>
                </div>
            </div>
        </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-6 border">
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
                    <div class="row">
                        <div class="col bg-warning block mb-4">
                            <!--Yellow Block at Top -->
                        </div> 
                    </div>
                   

                    <div class="row mt-4 mb-1">
                        <div class="col d-flex justify-content-center">
                            <h1>Register</h1>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col d-flex justify-content-center ">
                            <h6>Be a voter today!</h6>
                        </div>
                    </div>
                    <div class="row mx-5 mt-3">
                        <div class="col">
                            <div class="form-floating">
                                <input type="tel" name="stu_id" id="stu_id" class="form-control " placeholder=" "
                                pattern="^\d{10}$" required> <!-- only accepts 10 digit sutdent id -->

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
                                <input type="email" name="email" id="email" class="form-control" placeholder="" 
                                required> <!-- pattern="^[a-z]+\.[a-z]+\.[a-z]+@ust\.edu\.ph$"  only accepts ust formatted emails -->

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
                                <input type="tel" name="contact" id="contact" class="form-control " placeholder=" "
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
                            <input type="submit" name="register_submit" class="btn btn-primary btn-block w-100 fw-bold" value="Register User" id=sub >
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
                        <div class="row">
                            <div class="col bg-warning block"></div>
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
<?php
    // Check if OTP verification is successful
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $user_email = $_SESSION['email']; // Get the email from session
    $verify_query = "SELECT * FROM user_table WHERE email = '$user_email' AND otp = '$entered_otp'";
    $verify_result = $conn->query($verify_query);
    if ($verify_result->num_rows > 0) {
        // OTP is valid, update the user status and clear the OTP
         // Update the user status to 'Verified' and clear the OTP
             $updateQuery = "UPDATE user_table SET otp = NULL, status = 'Verified' WHERE email = '$user_email'";
             $conn->query($updateQuery);


             unset($_SESSION['register_stage']); // Clear the register stage session variable so it doesn't show the OTP form again
             unset($_SESSION['otp']); // Clear the OTP session variable
             unset($_SESSION['email']); // Clear the email session variable

            ?>
            <script>
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Your account has been verified.",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location = "login.php"; 
                });
            </script>
            <?php
        } else {
            echo $conn->error;
        }
    }


?>
