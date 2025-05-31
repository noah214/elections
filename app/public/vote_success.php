<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../public/db_conn.php';
require_once '../public/functions.php';

// Check if user is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Check if user has already voted
$check_vote_query = "SELECT COUNT(*) as vote_count FROM vote_table WHERE voter_id = '$voter_id'";
$vote_result = $conn->query($check_vote_query);
$vote_count = $vote_result->fetch_assoc()['vote_count'];

if ($vote_count == 0) {
    // If no votes found, redirect to voting page
    header("Location: vote.php");
    exit();
}

// Get all positions and their candidates
$positions_query = "SELECT * FROM position_table ORDER BY position_id";
$positions = $conn->query($positions_query);
$positions_array = [];
while ($position = $positions->fetch_assoc()) {
    $positions_array[$position['position_id']] = $position;
}

// Get the user's votes
$votes_query = "SELECT v.*, c.*, p.position_name 
                FROM vote_table v 
                JOIN candidate_table c ON v.candidate_id = c.candidate_id 
                JOIN position_table p ON c.position_id = p.position_id 
                WHERE v.voter_id = '$voter_id' 
                ORDER BY p.position_id";
$votes = $conn->query($votes_query);

// Check if results are available
$results_query = "SELECT * FROM settings_table WHERE setting_name = 'show_results'";
$results_setting = $conn->query($results_query);
$show_results = $results_setting->fetch_assoc()['setting_value'] ?? '0';

// Get election results if available
$results = [];
if ($show_results == '1') {
    $results_query = "SELECT 
        p.position_name,
        c.candidate_name,
        c.party_affiliation,
        c.college,
        c.img_path,
        COUNT(v.vote_id) as vote_count,
        (SELECT COUNT(DISTINCT voter_id) FROM vote_table) as total_voters
    FROM position_table p
    LEFT JOIN candidate_table c ON p.position_id = c.position_id
    LEFT JOIN vote_table v ON c.candidate_id = v.candidate_id
    GROUP BY p.position_id, c.candidate_id, c.candidate_name, c.party_affiliation, c.college, c.img_path
    ORDER BY p.position_id, vote_count DESC";
    $results_result = $conn->query($results_query);
    
    while ($row = $results_result->fetch_assoc()) {
        $position_id = $row['position_name'];
        if (!isset($results[$position_id])) {
            $results[$position_id] = [];
        }
        $results[$position_id][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Summary - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../css/global.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .summary-section { 
            padding: 4rem 0;
            position: relative;
            z-index: 1;
        }

        .summary-section::before {
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

        .summary-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .success-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }

        .position-title {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffc107;
        }

        .vote-card {
            background: #fff;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .vote-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .candidate-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .candidate-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: none;
        }

        .candidate-details {
            flex: 1;
        }

        .candidate-name {
            color: #000;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .candidate-position {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .candidate-description {
            color: #666;
            font-size: 0.9rem;
        }

        .vote-timestamp {
            color: #666;
            font-size: 0.9rem;
            text-align: right;
            margin-top: 0.5rem;
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

        .view-results-btn {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 2rem auto;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
        }
    </style>
</head>
<body> <!--navbar-->
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
                    <a class="nav-item nav-link" href="home.php" aria-current="page">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link vote" href="vote.php">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="Account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="summary-section">
        <div class="container">
            <div class="summary-card">
                <div class="text-center">
                    <i class="bi bi-check-circle-fill success-icon"></i>
                    <h1 class="mb-4">Your Vote Has Been Recorded!</h1>
                    <p class="lead mb-4">Thank you for participating in the BOTOmasino Elections.</p>
                </div>

                <h2 class="position-title">Your Votes</h2>
                
                <?php while ($vote = $votes->fetch_assoc()): ?>
                    <div class="vote-card">
                        <div class="candidate-info">
                            <img src="<?= htmlspecialchars($vote['img_path']) ?>" alt="Candidate" class="candidate-image">
                            <div class="candidate-details">
                                <div class="candidate-name"><?= htmlspecialchars($vote['candidate_name']) ?></div>
                                <div class="candidate-position"><?= htmlspecialchars($vote['position_name']) ?></div>
                                <div class="candidate-description">
                                    <?= htmlspecialchars($vote['party_affiliation']) ?> - 
                                    <?= htmlspecialchars($vote['college']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <?php if ($show_results == '1'): ?>
                    <a href="election_results.php" class="btn btn-primary view-results-btn">
                        <i class="bi bi-bar-chart-line-fill"></i> View Election Results
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© 2024 UST Supreme Student Council. All rights reserved.</span>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
</body>
</html> 