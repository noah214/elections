<?php

session_start();
    require_once "db_conn.php";

    // get current user stuff
    $username = $_SESSION['username'];
    $fullname = $_SESSION['fullname'];
    $email = $_SESSION['email'];
    $contactinfo = $_SESSION['contact_information'] ??'';
    $date_of_birth = $_SESSION['date_of_birth'] ?? '';
    $student_id = $_SESSION['student_id'] ?? '';

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/home.css">
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
                    <a class="nav-item nav-link active" href="home.php" aria-current="page">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="vote.php">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="Account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>
    
    <section class="hero-section d-flex flex-column justify-content-around align-items-center text-center ">
        <div class="hero-content ">
            <h1 class="hero-title">University of Santo Tomas</h1>
            <h2 class="hero-subtitle"><span class="text-yellow">BOTO</span>masino Elections</h2>
        </div>
        <div class="row bg-semiblack">
          <div class="col-3">
              <div class="d-flex justify-content-center align-items-center h-100">
                  <img src="../images/USTLogo.png" alt="UST Logo" style="width: 250px; height: 250px;">
              </div>
          </div>
          <div class="col">
              <div class="content-wrapper text-white">
                <h2 class="display-4 text-yellow fw-bold mb-4">Hello, Thomasian. It's Election Day!</h2>
                <p class="justified-text fw-bold fs-5">Election season has arrived at the University of Santo Tomas—a time for Thomasians to shape the future of our student body. 
                  As we choose new Supreme Student Council leaders, let us be guided by our core values: competence, compassion, and commitment. 
                  Voting is not just a right, but a duty to uphold Veritas and servant leadership. Choose the candidates who will lead with integrity and serve with heart. 
                  Vote wisely. Vote as a true Thomasian.</p>
              </div>
          </div>
        </div>
    </section>

    <div class="row bg-yellow">
            <div class="col">
              <div class="content-wrapper text-white d-flex flex-column justify-content-center align-items-center text-center">
                <h2 class="display-5 fw-bold">What Now?</h2>
              </div>
          </div>
    </div>
        <div class="row ust-bg2">
            <div class="col">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col">
                        <div class="content-wrapper mx-5 mt-3 bg-semiblack p-4">
                            <h3 class="text-yellow fw-bold mb-4 d-flex justify-content-center align-items-center">Make your Vote Count!</h3>
                            <p class="justified-text fw-bold fs-5 text-white">Your vote matters. Take part in shaping the future of UST by joining the Central Student Council elections. 
                                Be the Thomasian who chooses to lead change—vote and be heard.</p>
                        </div>
                        <div class="row mt-4">
                            <div class="col text-center my-3">
                                <a href="vote.php" class="btn btn-warning">
                                    <i class="bi bi-check2-square me-2"></i>Vote Now!
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="content-wrapper mx-5 mt-3 bg-semiblack p-4">
                                    <h3 class=" text-yellow fw-bold mb-4 d-flex justify-content-center align-items-center">Get to know your candidates!</h3>
                                    <p class="justified-text fw-bold fs-5 text-white"> Great leadership starts with informed choices. Learn about the candidates, their platforms, and their vision. 
                                        Choose those who truly represent your values as a Thomasian..</p>
                                </div>
                                <div class="row mt-4">
                                    <div class="col text-center my-3">
                                        <a href="vote.php" class="btn btn-warning">
                                            <i class="bi bi-people-fill me-2"></i>View Candidates
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!--footer-->
    <footer class="footer mt-auto py-4 bg-navy text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-5 mb-1">
                    <div class="row">
                        <div class="col-3">
                            <img src="../images/USTLogo.png" alt="UST Logo" style="width: 100px; height: 100px;">
                        </div>
                        <div class="col d-flex flex-column justify-content-center">
                            <h3 class="text-white"><span class="text-yellow">UST</span> Supreme Student Council</h3>
                            <h5 class="text-white"><span class="text-yellow">BOTO</span>masino Elections</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-1 ">
                    <h5 class="text-yellow mx-4 d-flex justify-content-center">Quick Links</h5>
                    <div class="row ">
                        <div class="col-6 d-flex flex-column justify-content-center align-items-center">
                            <ul class="list-unstyled">
                            <li><a href="home.php" class="text-white text-decoration-none active">Home</a></li>
                            <li><a href="candidate.php" class="text-white text-decoration-none">Candidates</a></li>
                        </div>
                        <div class="col d-flex flex-column justify-content-center align-items-center">
                            <ul class="list-unstyled">
                            <li><a href="vote.php" class="text-white text-decoration-none">Vote</a></li>
                            <li><a href="Account.php" class="text-white text-decoration-none">Account</a></li>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <h5 class="text-yellow d-flex flex-column justify-content-center align-items-center">Contact Us</h5>
                    <p class="desc"><span class="text-yellow">Address:</span> España Blvd, Sampaloc, Manila, Metro Manila</p>
                    <p><span class="text-yellow">Email:</span> ssc@ust.edu.ph</p>
                </div>
            </div>
            <hr class="my-2">
            <div class="row">
                <div class="col text-center">
                    <p class="small mb-0">&copy; <?php echo date('Y'); ?> UST Supreme Student Council. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
     <script>
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