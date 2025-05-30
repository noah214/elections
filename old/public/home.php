<?php
session_start();
require_once '../public/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit();
}

// Get voter information
$voter_id = $_SESSION['voter_id'];
$voter_query = "SELECT * FROM voter_table WHERE voter_id = '$voter_id'";
$voter_result = $conn->query($voter_query);
$voter = $voter_result->fetch_assoc();

// Check if voter has already voted
$check_vote = "SELECT * FROM vote_table WHERE voter_id = '$voter_id'";
$vote_result = $conn->query($check_vote);
$has_voted = $vote_result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    
    <style>
        /* Navbar styles */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: #000;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .nav-link {
            color: #666;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #ffc107;
        }
        
        .nav-link.active {
            color: #ffc107;
        }

        /* Home section styles */
        .home-section {
            padding: 4rem 0;
            margin-bottom: 4rem;
        }

        .welcome-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .welcome-title {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .welcome-text {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .action-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
        }

        .action-icon {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }

        .action-title {
            color: #000;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .action-text {
            color: #666;
            margin-bottom: 1.5rem;
        }

        /* Footer styles */
        .footer {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-top: auto;
        }
        
        .footer-content {
            text-align: center;
            color: #666;
        }
        
        .footer-content p {
            margin-bottom: 0.5rem;
        }
        
        .footer-content a {
            color: #ffc107;
            text-decoration: none;
        }
        
        .footer-content a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="home.php">BOTOmasino</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vote.php">Vote</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="candidates.php">Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="home-section">
        <div class="container">
            <div class="welcome-card">
                <h1 class="welcome-title">Welcome, <?= htmlspecialchars($voter['firstname'] . ' ' . $voter['lastname']) ?>!</h1>
                <p class="welcome-text">Thank you for participating in the BOTOmasino Elections. Your voice matters!</p>
                <?php if ($has_voted): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>You have already cast your vote.
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>You haven't cast your vote yet.
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="action-card">
                        <i class="bi bi-person-badge action-icon"></i>
                        <h2 class="action-title">View Candidates</h2>
                        <p class="action-text">Get to know the candidates running for different positions.</p>
                        <a href="candidates.php" class="btn btn-outline-warning">View Candidates</a>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="action-card">
                        <i class="bi bi-check-square action-icon"></i>
                        <h2 class="action-title">Cast Your Vote</h2>
                        <p class="action-text">Make your voice heard by voting for your preferred candidates.</p>
                        <?php if ($has_voted): ?>
                            <button class="btn btn-secondary" disabled>Already Voted</button>
                        <?php else: ?>
                            <a href="vote.php" class="btn btn-warning">Vote Now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?= date('Y') ?> BOTOmasino Elections. All rights reserved.</p>
                <p>Designed and developed with <i class="bi bi-heart-fill text-danger"></i> for the community</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
</body>
</html>

