<?php
session_start();
require_once '../config/database.php';
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
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/landing.css">
    <style>
        body { background: #f8f9fa; }
        .ust-header { background: #060841; color: #efb409; }
        .ust-header .navbar-brand, .ust-header .nav-link { color: #efb409 !important; font-weight: 600; }
        .ust-hero {
            background: linear-gradient(rgba(6,8,65,0.7),rgba(6,8,65,0.7)), url('../images/ust-bg.png') center/cover no-repeat;
            color: #fff;
            padding: 60px 0 0 0;
            min-height: 480px;
            position: relative;
        }
        .ust-hero .hero-title { font-size: 2.5rem; font-weight: 700; color: #ffd700; }
        .ust-hero .hero-sub { font-size: 2rem; font-weight: 600; color: #efb409; }
        .ust-hero .hero-announcement {
            background: rgba(0,0,0,0.7);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            color: #fff;
        }
        .ust-hero .hero-logo {
            width: 80px; height: 80px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;
        }
        .ust-section-title {
            background: #060841;
            color: #ffd700;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-align: center;
            padding: 0.75rem 0;
            margin-bottom: 0;
        }
        .what-now-section { background: #efb409; }
        .what-now-card {
            background: #060841;
            color: #fff;
            border-radius: 20px;
            padding: 2rem 1.5rem;
            min-height: 220px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: relative;
        }
        .what-now-card.yellow {
            background: #efb409;
            color: #060841;
        }
        .what-now-btn {
            background: #ffd700;
            color: #060841;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            padding: 0.5rem 2rem;
            margin-top: 1rem;
        }
        .what-now-btn.blue {
            background: #060841;
            color: #ffd700;
        }
        .mission-vision-section {
            background: #fff;
            padding: 2rem 0 1rem 0;
        }
        .mission-vision-title {
            color: #060841;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .mission-vision-text {
            color: #222;
            font-size: 1rem;
        }
        .ust-footer {
            background: #060841;
            color: #ffd700;
            font-size: 0.95rem;
        }
        .ust-footer a { color: #ffd700; }
        .ust-footer .social-icons a { color: #ffd700; font-size: 1.5rem; margin-right: 1rem; }
        .back-to-top { color: #ffd700; text-decoration: underline; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-4 mb-3">Welcome to BOTOmasino Elections</h1>
                <p class="lead">Cast your vote and make your voice heard in the UST Supreme Student Council Elections.</p>
                
                <?php if ($is_active): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Election is currently active. Cast your vote now!
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-clock-fill me-2"></i>
                        Election is not active at the moment.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-dark text-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill display-1 mb-3"></i>
                        <h3><?= $total_voters ?></h3>
                        <p class="mb-0">Total Voters</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle-fill display-1 mb-3"></i>
                        <h3><?= $total_votes ?></h3>
                        <p class="mb-0">Votes Cast</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge-fill display-1 mb-3"></i>
                        <h3><?= $total_candidates ?></h3>
                        <p class="mb-0">Candidates</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-warning">
                        <h5 class="mb-0">Election Timeline</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Registration Period
                                <span class="badge bg-warning text-dark"><?= date('M d, Y', strtotime($election['registration_start'])) ?> - <?= date('M d, Y', strtotime($election['registration_end'])) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Campaign Period
                                <span class="badge bg-warning text-dark"><?= date('M d, Y', strtotime($election['campaign_start'])) ?> - <?= date('M d, Y', strtotime($election['campaign_end'])) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Voting Period
                                <span class="badge bg-warning text-dark"><?= date('M d, Y', strtotime($election['voting_start'])) ?> - <?= date('M d, Y', strtotime($election['voting_end'])) ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark text-warning">
                        <h5 class="mb-0">Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="candidates.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-person-badge me-2"></i>
                                View Candidates
                            </a>
                            <a href="vote.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-check-circle me-2"></i>
                                Cast Your Vote
                            </a>
                            <a href="login.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Login to Your Account
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

