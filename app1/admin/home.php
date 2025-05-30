<?php
session_start();
require_once "db_conn.php";

// Get user details from session
$role = $_SESSION['role'];
$fullname = $_SESSION['fullname'] ?? 'Admin';

// Get counts from each table
$tables = [
    'voter_table' => 'Total Voters',
    'position_table' => 'Total Positions',
    'candidate_table' => 'Total Candidates',
    'vote_table' => 'Total Votes Cast'
];

$counts = [];
foreach ($tables as $table => $label) {
    $query = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($query);
    $counts[$table] = $result->fetch_assoc()['count'];
}

// Get recent votes
$recent_votes = "SELECT v.*, vt.voter_name as voter_name, c.candidate_name, p.position_name 
                FROM vote_table v 
                JOIN voter_table vt ON v.voter_id = vt.voter_id 
                JOIN candidate_table c ON v.candidate_id = c.candidate_id 
                JOIN position_table p ON c.position_id = p.position_id 
                ORDER BY v.vote_timestamp DESC LIMIT 5";
$recent_votes_result = $conn->query($recent_votes);

// Get position-wise vote counts
$position_votes = "SELECT p.position_name, COUNT(v.vote_id) as vote_count 
                  FROM position_table p 
                  LEFT JOIN candidate_table c ON p.position_id = c.position_id 
                  LEFT JOIN vote_table v ON c.candidate_id = v.candidate_id 
                  GROUP BY p.position_id, p.position_name";
$position_votes_result = $conn->query($position_votes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../css/global.css">
    
    <style>
        /* Dashboard specific styles */
        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .stat-card {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: white;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            border-left: 3px solid #ffc107;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }

        .vote-chart {
            height: 300px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4>BOTOmasino Elections</h4>
            <div class="text-light mt-2">
                <small>Welcome, <?= htmlspecialchars($fullname) ?></small>
            </div>
        </div>
        <a href="home.php" class="sidebar-item active"><i class="bi bi-house-fill"></i>Home</a>
        <div class="sidebar-category">User Management</div>
        <?php if (strtolower($role) !== 'organizer'): ?>
            <a href="users.php" class="sidebar-item"><i class="bi bi-people-fill"></i>Admin Users</a>
        <?php endif; ?>
        <a href="voters.php" class="sidebar-item"><i class="bi bi-person-check-fill"></i>Voter Accounts</a>
        
        <div class="sidebar-category">Election Management</div>
        <a href="candidates.php" class="sidebar-item"><i class="bi bi-person-badge-fill"></i>Candidate List</a>
        <a href="positions.php" class="sidebar-item"><i class="bi bi-briefcase-fill"></i>Position List</a>
        <a href="votes.php" class="sidebar-item"><i class="bi bi-box-seam"></i>Vote Records</a>
        
        <div class="sidebar-category">Reports</div>
        <a href="votecount.php" class="sidebar-item"><i class="bi bi-bar-chart-line-fill"></i>Vote Statistics</a>
        <?php if (strtolower($role) !== 'organizer'): ?>
            <a href="logs.php" class="sidebar-item"><i class="bi bi-journal-text"></i>Activity Logs</a>
        <?php endif; ?>

        <div class="mt-auto">
            <div class="sidebar-category">Account</div>
            <a href="logout.php" class="sidebar-item" style="color: #ffc107; background-color: #000;">
                <i class="bi bi-box-arrow-right"></i>Logout
            </a>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <h1 class="mb-4">Admin Dashboard</h1>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="stat-label">Total Voters</h6>
                                <h2 class="stat-number"><?= $counts['voter_table'] ?></h2>
                            </div>
                            <i class="bi bi-people-fill stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="stat-label">Total Positions</h6>
                                <h2 class="stat-number"><?= $counts['position_table'] ?></h2>
                            </div>
                            <i class="bi bi-briefcase-fill stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="stat-label">Total Candidates</h6>
                                <h2 class="stat-number"><?= $counts['candidate_table'] ?></h2>
                            </div>
                            <i class="bi bi-person-badge-fill stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="stat-label">Total Votes</h6>
                                <h2 class="stat-number"><?= $counts['vote_table'] ?></h2>
                            </div>
                            <i class="bi bi-check-circle-fill stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Recent Activity -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>Recent Votes
                        </h5>
                    </div>
                    <div class="card-body recent-activity">
                        <?php if ($recent_votes_result->num_rows > 0): ?>
                            <?php while ($vote = $recent_votes_result->fetch_assoc()): ?>
                                <div class="activity-item">
                                    <h6 class="mb-1"><?= htmlspecialchars($vote['voter_name']) ?></h6>
                                    <p class="mb-1 text-muted">
                                        Voted for <?= htmlspecialchars($vote['candidate_name']) ?> 
                                        (<?= htmlspecialchars($vote['position_name']) ?>)
                                    </p>
                                    <small class="text-muted">
                                        <?= date('M d, Y h:i A', strtotime($vote['vote_timestamp'])) ?>
                                    </small>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">No votes cast yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Position-wise Votes -->
            <div class="col-md-6 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart-fill me-2"></i>Votes by Position
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($position_votes_result->num_rows > 0): ?>
                            <?php while ($position = $position_votes_result->fetch_assoc()): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0"><?= htmlspecialchars($position['position_name']) ?></h6>
                                        <span class="badge bg-warning"><?= $position['vote_count'] ?> votes</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: <?= ($position['vote_count'] / $counts['voter_table']) * 100 ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">No positions found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
</body>
</html> 