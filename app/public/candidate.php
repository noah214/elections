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
        .candidate-section {
            padding: 4rem 0;
        }

        .position-title {
            color: #000;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .position-description {
            color: #666;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            font-style: italic;
        }

        .candidate-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            height: 100%;
            border: 2px solid #ffc107;
            max-width: 320px;
            margin: 0 auto;
        }

        .candidate-card:hover {
            transform: translateY(-5px);
        }

        .candidate-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border: none;
            border-radius: 0;
        }

        .candidate-info {
            padding: 1.2rem;
        }

        .candidate-name {
            color: #000;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .candidate-position {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.4rem;
            font-size: 1rem;
        }

        .candidate-description {
            color: #666;
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
        }

        .platform-title {
            color: #000;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            border-top: 1px solid #eee;
            padding-top: 0.8rem;
        }

        .platform-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .platform-list li {
            color: #666;
            margin-bottom: 0.4rem;
            padding-left: 1.2rem;
            position: relative;
            font-size: 0.9rem;
        }

        .platform-list li:before {
            content: "•";
            color: #ffc107;
            position: absolute;
            left: 0;
        }

        .row {
            justify-content: center;
        }

        .col-md-6.col-lg-4 {
            padding: 0 15px;
        }
    </style>
</head>
<body>
        <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar" id="mainNavbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.css">
                <img src="" width="30" height="30" class="d-inline-block align-top me-2" alt="SSC Logo">
                UST Supreme Student Council
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-item nav-link" href="home.php" aria-current="page">Home</a>
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
                <div class="mb-5">
                    <h2 class="position-title"><?= htmlspecialchars($position['position_name']) ?></h2>
                    <p class="position-description"><?= htmlspecialchars($position['position_description']) ?></p>
                    <div class="row">
                        <?php while ($candidate = $candidates->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="candidate-card">
                                    <img src="<?= htmlspecialchars($candidate['img_path']) ?>" 
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
            <?php 
                endif;
            endwhile; 
            ?>
        </div>
    </section>
    
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