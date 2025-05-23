<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Candidates - BOTOmasino</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/candidate.css">
</head>
<body>
    <!--Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i>
                UST Supreme Student Council
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-item nav-link" href="home.php">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link active" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="vote.php">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Candidates Section -->
    <section class="candidate-section">
        <div class="container">
            <!-- President Position -->
            <div class="mb-5">
                <h2 class="position-title">President</h2>
                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="candidate-card">
                            <img src="../assets/candidates/president1.jpg" alt="John Doe" class="candidate-image">
                            <div class="candidate-info">
                                <h3 class="candidate-name">John Doe</h3>
                                <p class="candidate-position">Running for President</p>
                                <p class="candidate-description">4th Year BS Computer Science</p>
                                <div class="candidate-platform">
                                    <h4 class="platform-title">Platform</h4>
                                    <ul class="platform-list">
                                        <li>Enhance student welfare programs</li>
                                        <li>Improve campus facilities</li>
                                        <li>Strengthen student representation</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="candidate-card">
                            <img src="../assets/candidates/president2.jpg" alt="Jane Smith" class="candidate-image">
                            <div class="candidate-info">
                                <h3 class="candidate-name">Jane Smith</h3>
                                <p class="candidate-position">Running for President</p>
                                <p class="candidate-description">4th Year BS Psychology</p>
                                <div class="candidate-platform">
                                    <h4 class="platform-title">Platform</h4>
                                    <ul class="platform-list">
                                        <li>Mental health awareness programs</li>
                                        <li>Academic support initiatives</li>
                                        <li>Community engagement projects</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vice President Position -->
            <div class="mb-5">
                <h2 class="position-title">Vice President</h2>
                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="candidate-card">
                            <img src="../assets/candidates/vp1.jpg" alt="Mike Johnson" class="candidate-image">
                            <div class="candidate-info">
                                <h3 class="candidate-name">Mike Johnson</h3>
                                <p class="candidate-position">Running for Vice President</p>
                                <p class="candidate-description">3rd Year BS Accountancy</p>
                                <div class="candidate-platform">
                                    <h4 class="platform-title">Platform</h4>
                                    <ul class="platform-list">
                                        <li>Financial transparency</li>
                                        <li>Student organization support</li>
                                        <li>Leadership development programs</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secretary Position -->
            <div class="mb-5">
                <h2 class="position-title">Secretary</h2>
                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="candidate-card">
                            <img src="../assets/candidates/secretary1.jpg" alt="Sarah Wilson" class="candidate-image">
                            <div class="candidate-info">
                                <h3 class="candidate-name">Sarah Wilson</h3>
                                <p class="candidate-position">Running for Secretary</p>
                                <p class="candidate-description">3rd Year BS Journalism</p>
                                <div class="candidate-platform">
                                    <h4 class="platform-title">Platform</h4>
                                    <ul class="platform-list">
                                        <li>Improved communication channels</li>
                                        <li>Documentation system upgrade</li>
                                        <li>Student feedback mechanism</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>

