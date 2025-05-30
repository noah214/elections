<?php
session_start();
require_once '../public/db_conn.php';


// Get all positions
$positions_query = "SELECT * FROM position_table ORDER BY position_id";
$positions = $conn->query($positions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/global.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .candidate-section { 
            padding: 4rem 0;
            position: relative;
            z-index: 1;
        }

        .candidate-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            z-index: -1;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .position-card {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .position-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .position-title {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffc107;
        }

        .candidate-card {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .candidate-card:hover {
            border-color: #ffc107;
            background-color: #fff9e6;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .candidate-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .candidate-name {
            color: #000;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .candidate-party {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .candidate-college {
            color: #666;
            font-size: 0.9rem;
        }


        .alert {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
    </style>
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
                    <a class="nav-item nav-link " href="home.php" aria-current="page">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link active" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="vote.php">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="Account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>

    
    <section class="candidate-section">
        <div class="container">
            <?php 
            while ($position = $positions->fetch_assoc()): 
                // Get candidates for this position
                $candidates_query = "SELECT * FROM candidate_table WHERE position_id = ?";
                $stmt = $conn->prepare($candidates_query);
                $stmt->bind_param("i", $position['position_id']);
                $stmt->execute();
                $candidates = $stmt->get_result();

                if ($candidates->num_rows > 0):
            ?>
                <div class="mb-5 text-center">
                    <h2 class="position-title"><?= htmlspecialchars($position['position_name']) ?></h2>
                    <p class="position-description"><?= htmlspecialchars($position['position_description']) ?></p>
                    <div class="row d-flex justify-content-center align-items-center">
                        <?php while ($candidate = $candidates->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="candidate-card">
                                    <img src="../<?= htmlspecialchars($candidate['img_path']) ?>" 
                                         alt="<?= htmlspecialchars($candidate['candidate_name']) ?>" 
                                         class="candidate-image">
                                    <div class="candidate-info">
                                        <h3 class="candidate-name"><?= htmlspecialchars($candidate['candidate_name']) ?></h3>
                                        <p class="candidate-position">Running for <?= htmlspecialchars($position['position_name']) ?></p>
                                        <p class="candidate-description"><?= htmlspecialchars($candidate['college']) ?></p>
                                        <div class="candidate-platform">
                                            <h4 class="platform-title">Platform</h4>
                                            <ul class="platform-list">
                                                <li><?= htmlspecialchars($candidate['party_affiliation']) ?></li>
                                                <li><?= htmlspecialchars($position['position_description']) ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <hr class="my-5">
            <?php 
                endif;
            endwhile; 
            ?>
        </div>
    </section>
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
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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