<?php
session_start();
require_once '../php/db_conn.php';
require_once '../php/add_logs.php';

// Get all candidates with their positions
$candidates_query = "SELECT c.*, p.position_name, p.position_description 
                    FROM candidate_table c 
                    JOIN position_table p ON c.position_id = p.position_id 
                    ORDER BY p.position_id, c.candidate_name";
$candidates = $conn->query($candidates_query);

if (!$candidates) {
    die("Error in candidates query: " . $conn->error);
}

// Group candidates by position
$candidates_by_position = [];
while ($candidate = $candidates->fetch_assoc()) {
    $position_id = $candidate['position_id'];
    if (!isset($candidates_by_position[$position_id])) {
        $candidates_by_position[$position_id] = [
            'position_name' => $candidate['position_name'],
            'position_description' => $candidate['position_description'],
            'candidates' => []
        ];
    }
    $candidates_by_position[$position_id]['candidates'][] = $candidate;
}
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
            background-image: url('../images/ust-bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        div.container{
            margin-top: 2.5%;
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
            z-index: -1;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 4rem;
            margin-bottom: 2rem;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
            transition: all 0.3s ease;
            
        }

        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .position-title {
            color: #000;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            margin-top: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffc107;
        }

        .position-description {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            text-align: center;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .candidate-card {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 0;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            width: 100%;
            display: flex;
            flex-direction: column;
            max-width: 280px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .candidate-card:hover {
            border-color: #ffc107;
            background-color: #fff9e6;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .candidate-image {
            width: 100%;
            aspect-ratio: 1;
            position: relative;
            overflow: hidden;
            max-height: 280px;
        }

        .candidate-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .candidate-info {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            flex: 1;
            align-items: center;
        }

        .candidate-name {
            color: #000;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            line-height: 1.2;
        }

        .candidate-position {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }

        .candidate-description {
            color: #666;
            font-size: 0.8rem;
            line-height: 1.2;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.4em;
        }

        .candidate-container {
            display: flex;
            justify-content: center;
            align-items: stretch;
            height: 100%;
            padding: 0.5rem;
        }

        @media (max-width: 1200px) {
            .candidate-container {
                width: 33.333%;
            }
        }

        @media (max-width: 992px) {
            .candidate-container {
                width: 50%;
            }
        }

        @media (max-width: 576px) {
            .candidate-container {
                width: 100%;
            }
        }

        .btn-primary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-primary:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            color: #000;
        }

        .alert {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .position-section {
            margin-bottom: 4rem;
        }

        .position-section:last-child {
            margin-bottom: 0;
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
            <div class="form-card">
                
                <h2 class="candidate-title text-center">Candidates</h2>

                <p class="text-muted mb-4 text-center">Meet our candidates for the upcoming election</p>

                <?php if (!empty($candidates_by_position)): ?>
                    <?php foreach ($candidates_by_position as $position_id => $position_data): ?>
                        <div class="position-section">
                            <h3 class="position-title text-center"><?= htmlspecialchars($position_data['position_name']) ?></h3>
                            <p class="position-description"><?= htmlspecialchars($position_data['position_description']) ?></p>
                            <div class="row d-flex justify-content-center align-items-center g-2">
                                <?php foreach ($position_data['candidates'] as $candidate): ?>
                                    <div class="candidate-container col-3">
                                        <div class="candidate-card">
                                            <div class="candidate-image">
                                                <img src="../<?= htmlspecialchars($candidate['img_path']) ?>" 
                                                     alt="<?= htmlspecialchars($candidate['candidate_name']) ?>">
                                            </div>
                                            <div class="candidate-info">
                                                <h3 class="candidate-name"><?= htmlspecialchars($candidate['candidate_name']) ?></h3>
                                                <p class="candidate-position"><?= htmlspecialchars($candidate['party_affiliation']) ?></p>
                                                <p class="candidate-description"><?= htmlspecialchars($candidate['college']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No candidates have been registered yet.
                    </div>
                <?php endif; ?>
            </div>
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
                            <li><a href="home.php" class="text-white text-decoration-none">Home</a></li>
                            <li><a href="candidate.php" class="text-white text-decoration-none active">Candidates</a></li>
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