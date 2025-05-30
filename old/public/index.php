<?php
require_once '../config/database.php';

// Get election status
$stmt = $conn->prepare("SELECT * FROM settings WHERE setting_key = 'election_status'");
$stmt->execute();
$result = $stmt->get_result();
$election_status = $result->fetch_assoc()['setting_value'];

// Get total voters
$total_voters = $conn->query("SELECT COUNT(*) as count FROM voters")->fetch_assoc()['count'];

// Get total votes cast
$total_votes = $conn->query("SELECT COUNT(DISTINCT voter_id) as count FROM votes")->fetch_assoc()['count'];

// Get positions with vote counts
$positions = $conn->query("SELECT p.*, 
    (SELECT COUNT(*) FROM votes v WHERE v.position_id = p.id) as vote_count 
    FROM positions p ORDER BY p.priority");
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
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../assets/images/ust.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .position-card {
            transition: all 0.3s ease;
        }
        
        .position-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container text-center">
                <h1 class="display-4 mb-4">BOTOmasino Elections</h1>
                <p class="lead mb-4">A secure and efficient way to conduct student elections at the University of Santo Tomas.</p>
                <?php if ($election_status == 'active'): ?>
                    <a href="vote.php" class="btn btn-warning btn-lg">
                        <i class="bi bi-check2-circle me-2"></i>Cast Your Vote
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning d-inline-block">
                        <i class="bi bi-clock me-2"></i>Election is currently closed
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Stats Section -->
        <section class="container mb-5">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card stat-card bg-dark text-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-people-fill display-4 mb-3"></i>
                            <h3><?= number_format($total_voters) ?></h3>
                            <p class="mb-0">Registered Voters</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card stat-card bg-dark text-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-check2-square display-4 mb-3"></i>
                            <h3><?= number_format($total_votes) ?></h3>
                            <p class="mb-0">Votes Cast</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card stat-card bg-dark text-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-percent display-4 mb-3"></i>
                            <h3><?= $total_voters > 0 ? number_format(($total_votes / $total_voters) * 100, 1) : 0 ?>%</h3>
                            <p class="mb-0">Voter Turnout</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Positions Section -->
        <section class="container mb-5">
            <h2 class="text-center mb-4">Election Positions</h2>
            <div class="row">
                <?php while ($position = $positions->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card position-card h-100">
                            <div class="card-header bg-dark text-warning">
                                <h5 class="mb-0"><?= htmlspecialchars($position['name']) ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Votes Cast:</strong> <?= number_format($position['vote_count']) ?>
                                </p>
                                <a href="candidates.php" class="btn btn-outline-warning">
                                    <i class="bi bi-person-badge me-2"></i>View Candidates
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 