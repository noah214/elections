<?php
ob_start(); // Start output buffering
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">
    
   </head>
  <body>
    

        <div class="container-fluid ust-bg">
            <div class="row h-100">
                <div class="col-5 d-flex justify-content-center flex-column align-items-center">
                    <div>
                        <h1 class="text-yellow display-5 fw-bold">University of Santo Tomas</h1>
                    </div>
                    <div class="text-white">
                        <h2>Supreme Student Council:</h2>
                    </div>
                    <div class="text-white">
                        <h2><span class="text-yellow">BOTO</span>masino Elections</h2>
                    </div>
                </div>
                <div class="col d-flex align-items-center">
                    <!-- Container -->
                    <div class="bg-white w-75 mx-auto shadow">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col bg-warning block">
                                    
                                </div> 
                            </div>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <h1>Login</h1>
                                </div>
                            </div>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <div class="form-floating">
                                        <input type="text" name="username" id="username" class="form-control border-secondary" placeholder=" ">
                                        <label for="username" class="form-label">Username</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-5 mt-3">
                                <div class="col">
                                    <div class="form-floating position-relative">
                                        <input type="password" name="pass" id="pass" class="form-control border-secondary" placeholder=" ">
                                        <label for="pass" class="form-label">Password</label>
                                        <span class="position-absolute end-0 top-50 translate-middle-y pe-3" style="cursor: pointer;">
                                            <i class="bi bi-eye-slash" id="togglePassword"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-5 mt-5 mb-1">
                                <div class="col">   
                                    <input type="submit" name="sub" class="btn btn-primary btn-block w-100 fw-bold" value="Login" id=sub >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex justify-content-center">
                                    <p>Don't have an account? <a href="register.php">Sign Up Here!</a></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col d-flex justify-content-center">
                                    <p><a href="../public/forgot_password.php">Forgot Password?</a></p>
                                </div>
                            </div>
                         

                        </form>
                        <div class="row">
                                <div class="col bg-dark block">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script>
        // Password visibility toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#pass');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // toggle the eye icon
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            const scrolled = window.pageYOffset;
            
            if (scrolled > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
 </body>
</html>

<?php
session_start();
require_once "db_conn.php";

// Show success message if redirected from registration
if (isset($_SESSION['registration_success'])) {
    unset($_SESSION['registration_success']);
    ?>
    <script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Registration Complete!",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php
}

// Handle login
if (isset($_POST['sub'])){
    $ppusername = $_POST['username'];
    $pppassword = md5($_POST['pass']);

    $_SESSION['username'] = $ppusername;

    $pploginsql = "Select * from user_table WHERE username = '".$ppusername."' AND password = '".$pppassword."'";
    $ppresult = $conn ->query($pploginsql);

    if ($ppresult->num_rows == 1) {
        $ppfielddata = $ppresult->fetch_assoc();
        // print_r($ppfielddata);
        
        //Type of User
        $pprole = $ppfielddata['role'];
        $ppfullname = $ppfielddata['full_name'];
        $ppemail = $ppfielddata['email'];

        $_SESSION['username'] = $ppusername;
        $_SESSION['fullname'] = $ppfullname;
        $_SESSION['role'] = $pprole;
        $_SESSION['email'] = $ppemail;

        // If user is a voter, get their voter information
        if ($pprole == "Voter") {
            $voterQuery = "SELECT * FROM voter_table WHERE voter_name = '$ppfullname'";
            $voterResult = $conn->query($voterQuery);
            
            if ($voterResult->num_rows == 1) {
                $voterData = $voterResult->fetch_assoc();
                
                // Store voter information in session
                $_SESSION['voter_id'] = $voterData['voter_id'];
                $_SESSION['date_of_birth'] = $voterData['date_of_birth'];
                $_SESSION['gender'] = $voterData['gender'];
                $_SESSION['contact_information'] = $voterData['contact_information'];
                $_SESSION['student_id'] = $voterData['student_id'];
            }
        }

       if ($pprole == "Admin" || $pprole == "Organizer") {
            header("location: ../admin/home.php");
            exit;
        } elseif ($pprole == "Voter"){
            ?> 
            <script>
                window.location.href = "../public/home.php";
            </script>
            <?php
        }

    } else {
        ?>    
        <script>
            Swal.fire({
            position: "center",
            icon: "error",
            title: "Invalid username or password",
            showConfirmButton: false,
            timer: 1500
            });
        </script>
        <?php
    }
}

