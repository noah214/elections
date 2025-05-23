<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/register.css">

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
                <form action="" method="post">
                    <div class="row">
                        <div class="col bg-warning block mb-4">
                            
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
                            <input type="submit" name="sub" class="btn btn-primary btn-block w-100 fw-bold" value="Register User" id=sub >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col bg-dark block">
                            
                        </div>
                    </div>
                </form>
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

require_once "db_conn.php";

if ($conn->connect_error) {
    die("Connection Error");
} else {
    echo "Connected";
}

if (isset($_POST['sub'])) {
    
    //Registration for User Table
    $ppfullname = $_POST['fname']." ".$_POST['mname']." ".$_POST['lname'];
    $pprole = "voter"; //default role when creating an account in registration - voter view
    $ppusername = $_POST['username'];
    $pppassword = $_POST['pass'];
    $ppemail = $_POST['email'];

    //Registration for Voter Table
    $ppvotername = $_POST['fname']." ".$_POST['mname']." ".$_POST['lname'];
    $ppdate_birth = $_POST['date_birth'];
    $ppgender = $_POST['gender'];
    $ppcontact = $_POST['contact'];
    $ppstu_id = $_POST['stu_id'];

    //Inserting data in User Table
    $insertUser = "Insert into user_table (full_name, role, username, password, email) 
    values ('$ppfullname', '$pprole', '$ppusername', '$pppassword', '$ppemail')";

    //Inserting data in VoterTable
    $insertVoter = "Insert into voter_table (voter_name, date_of_birth, gender, contact_information, student_id) 
    values ('$ppvotername', '$ppdate_birth', '$ppgender', '$ppcontact', '$ppstu_id')";


    $resultUser = $conn->query($insertUser);
    $resultVoter = $conn->query($insertVoter);

    if ($resultUser && $resultVoter == TRUE){

?>
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Your work has been saved.",
                showConfirmButton: false,
                timer: 1500

            }).then(function() {
            window.location = "login.php"; 
            });
        </script>
<?php
        echo "Account Successfully Created!";
    } else {
        echo $db_conn->error;
    }
}

?>