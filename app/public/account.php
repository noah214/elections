<?php

session_start();

require_once '../php/db_conn.php';
require_once '../php/add_logs.php';



// Get voter information
$voter_id = $_SESSION['voter_id'];
$voter_query = "SELECT * FROM voter_table WHERE voter_id = '$voter_id'";
$voter_result = $conn->query($voter_query);
$voter = $voter_result->fetch_assoc();

// get current user stuff
$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];
$email = $_SESSION['email'];

$contactinfo = $_SESSION['contact_information'];
$date_of_birth = $_SESSION['date_of_birth'];
$student_id = $_SESSION['student_id'];


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account - BOTOmasino Elections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/account.css">
    
   </head>
  <body>
    <!--navbar-->
    <nav class="navbar navbar-expand-lg custom-navbar" id="mainNavbar">
        <div class="container-fluid px-5">
            <a class="navbar-brand d-flex align-items-center" href="home.php">
                <img src="../images/USTLogo.png" width="40" height="40" class="d-inline-block me-2" alt="SSC Logo">
                <span class="text-yellow">UST</span>&nbsp;Supreme Student Council
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
                    <a class="nav-item nav-link active" href="Account.php" aria-current="page">Account</a>
                </div>
            </div>
        </div>
    </nav>
    
        <div class="container-fluid ust-bg vh-100">
            <div class="row h-100">
                <div class="col d-flex justify-content-center align-items-center">
                    <div class="bg-white shadow rounded-3 p-4" style="width: 500px;">
                        <div class="row">
                                <div class="col bg-warning block">
                                </div> 
                            </div>
                        <h1 class="text-center mb-4 mt-2">Account Details</h1>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" value="<?php echo $fullname; ?>" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" value="<?php echo $username; ?>" readonly>
                            </div>
                            <div class="col">
                                <label for="studentid" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="studentid" value="<?php echo  $student_id; ?>" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="<?php echo $email; ?>" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="dateofbirth" class="form-label">Date of Birth</label>
                                <input type="text" class="form-control" id="dateofbirth" value="<?php echo $date_of_birth; ?>" readonly>
                            </div>
                        </div>            
                        <div class="row mb-3">
                            <div class="col">
                                <label for="contactinfo" class="form-label">Contact Information</label>
                                <input type="text" class="form-control" id="contactinfo" value="<?php echo $contactinfo; ?>" readonly>
                            </div>
                        </div>             
                        <div class="row">
                                <div class="col bg-dark block">
                                    
                                </div> 
                        </div>
                        <div class="row mt-4">
                            <div class="col text-center">
                                <a href="../php/logout.php" class="btn btn-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 </body>
</html>
