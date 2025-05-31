<?php
    // start session n connect to db
    session_start();
    require_once "../php/db_conn.php";
    require_once "../php/add_logs.php";

    // Handle results visibility toggle
    if (isset($_POST['toggle_results'])) {
        $current_value = isset($_POST['current_value']) ? $_POST['current_value'] : '0';
        $new_value = $current_value === '1' ? '0' : '1';
        
        $update_query = "INSERT INTO settings_table (setting_name, setting_value) 
                        VALUES ('show_results', '$new_value') 
                        ON DUPLICATE KEY UPDATE setting_value = '$new_value'";
        
        if ($conn->query($update_query)) {
            $_SESSION['success_message'] = "Results visibility updated successfully";
        } else {
            $_SESSION['error_message'] = "Failed to update results visibility";
        }
        
        header("Location: votecount.php");
        exit();
    }

    // Get current results visibility setting
    $results_query = "SELECT setting_value FROM settings_table WHERE setting_name = 'show_results'";
    $results_setting = $conn->query($results_query);
    $show_results = $results_setting->fetch_assoc()['setting_value'] ?? '0';

    // get current user stuff
    $username = $_SESSION['username'];
    $fullname = $_SESSION['fullname'];
    $role = $_SESSION['role'];

    // check if db is working
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get total votes and voter count
    $total_stats_query = "SELECT 
        COUNT(DISTINCT v.voter_id) as total_voters,
        COUNT(v.vote_id) as total_votes
        FROM vote_table v";
    $total_stats_result = mysqli_query($conn, $total_stats_query);
    $total_stats = mysqli_fetch_assoc($total_stats_result);

    // Get position-wise vote counts
    $position_query = "SELECT 
        p.position_id,
        p.position_name,
        COUNT(DISTINCT v.voter_id) as voters_count,
        COUNT(v.vote_id) as votes_count
        FROM position_table p
        LEFT JOIN candidate_table c ON p.position_id = c.position_id
        LEFT JOIN vote_table v ON c.candidate_id = v.candidate_id
        GROUP BY p.position_id, p.position_name
        ORDER BY p.position_id";
    $position_result = mysqli_query($conn, $position_query);

    // Get candidate-wise vote counts
    $candidate_query = "SELECT 
        c.candidate_id,
        c.candidate_name,
        c.party_affiliation,
        p.position_name,
        COUNT(v.vote_id) as vote_count
        FROM candidate_table c
        JOIN position_table p ON c.position_id = p.position_id
        LEFT JOIN vote_table v ON c.candidate_id = v.candidate_id
        GROUP BY c.candidate_id, c.candidate_name, c.party_affiliation, p.position_name
        ORDER BY p.position_id, vote_count DESC";
    $candidate_result = mysqli_query($conn, $candidate_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Count - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/global.css">
    
    <style>
        body { 
            min-height: 100vh; 
            overflow-x: hidden;
        }
        
        main {
            margin-left: 16.66667%;
            width: 83.33333%;
            padding: 1rem;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: static;
                height: auto;
                width: 100%;
            }
            main {
                margin-left: 0;
                width: 100%;
            }
        }

        .stats-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-card h3 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stats-card .number {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
        }

        .position-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .position-card h2 {
            color: #000;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffc107;
        }

        .candidate-row {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .candidate-row:hover {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .candidate-info {
            flex: 1;
        }

        .candidate-name {
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .candidate-party {
            color: #666;
            font-size: 0.9rem;
        }

        .vote-count {
            font-size: 1.2rem;
            font-weight: 700;
            color: #198754;
            margin-left: 1rem;
        }

        .progress {
            height: 8px;
            margin-top: 0.5rem;
        }

        .progress-bar {
            background-color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-header">
                    <h4>BOTOmasino Elections</h4>
                    <div class="text-light mt-2">
                        <small>Welcome, <?= htmlspecialchars($fullname) ?></small>
                    </div>
                </div>
                <div class="sidebar-category">Admin Dashboard</div>
                <a href="home.php"><i class="bi bi-person-badge-fill"></i>Home</a>
                <div class="sidebar-category">User Management</div>
                <?php if (strtolower($role) !== 'organizer'): ?>
                    <a href="users.php"><i class="bi bi-people-fill"></i> Users</a>
                <?php endif; ?>
                <a href="voter.php"><i class="bi bi-person-check-fill"></i> Voter Accounts</a>
                
                <div class="sidebar-category">Election Management</div>
                <a href="candidates.php"><i class="bi bi-person-badge-fill"></i> Candidate List</a>
                <a href="positions.php"><i class="bi bi-briefcase-fill"></i> Position List</a>
                <a href="votes.php"><i class="bi bi-box-seam"></i> Vote Records</a>
                
                <div class="sidebar-category">Reports</div>
                <a href="votecount.php" class="sidebar-item active"><i class="bi bi-bar-chart-line-fill"></i> Vote Statistics</a>
                <?php if (strtolower($role) !== 'organizer'): ?>
                    <a href="logs.php"><i class="bi bi-journal-text"></i> Activity Logs</a>
                <?php endif; ?>

                <div class="mt-auto">
                    <div class="sidebar-category">Account</div>
                    <a href="logout.php" class="sidebar-item" style="color: #ffc107; background-color: #000;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 px-4 py-4">
                <div class="container-fluid">
                    <h1 class="mb-4">Vote Count</h1>
                    
                    <!-- Results Visibility Toggle -->
                    <div class="row mb-4">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="" class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-0">Election Results Visibility</h5>
                                            <p class="text-muted mb-0">Control whether voters can see the election results</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="current_value" value="<?= $show_results ?>">
                                            <input class="form-check-input" type="checkbox" role="switch" id="resultsToggle" 
                                                   <?= $show_results === '1' ? 'checked' : '' ?> 
                                                   onchange="this.form.submit()">
                                            <input type="hidden" name="toggle_results" value="1">
                                            <label class="form-check-label" for="resultsToggle">
                                                <?= $show_results === '1' ? 'Results are visible' : 'Results are hidden' ?>
                                            </label>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="stats-card">
                                <h3>Total Voters</h3>
                                <div class="number"><?= $total_stats['total_voters'] ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card">
                                <h3>Total Votes Cast</h3>
                                <div class="number"><?= $total_stats['total_votes'] ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Position-wise Results -->
                    <?php while ($position = mysqli_fetch_assoc($position_result)): ?>
                        <div class="position-card">
                            <h2><?= htmlspecialchars($position['position_name']) ?></h2>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <?= $position['voters_count'] ?> voters cast their votes
                                </small>
                            </div>
                            
                            <?php
                            // Get candidates for this position
                            $candidates = [];
                            while ($candidate = mysqli_fetch_assoc($candidate_result)) {
                                if ($candidate['position_name'] === $position['position_name']) {
                                    $candidates[] = $candidate;
                                }
                            }
                            // Reset the result pointer
                            mysqli_data_seek($candidate_result, 0);
                            
                            // Calculate total votes for this position
                            $position_total = array_sum(array_column($candidates, 'vote_count'));
                            
                            // Display candidates
                            foreach ($candidates as $candidate):
                                $percentage = $position_total > 0 ? ($candidate['vote_count'] / $position_total) * 100 : 0;
                            ?>
                                <div class="candidate-row">
                                    <div class="candidate-info">
                                        <div class="candidate-name"><?= htmlspecialchars($candidate['candidate_name']) ?></div>
                                        <div class="candidate-party"><?= htmlspecialchars($candidate['party_affiliation']) ?></div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $percentage ?>%" 
                                                 aria-valuenow="<?= $percentage ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="vote-count">
                                        <?= $candidate['vote_count'] ?> votes
                                        <small class="text-muted">(<?= number_format($percentage, 1) ?>%)</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "<?= $_SESSION['success_message'] ?>",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php unset($_SESSION['success_message']); endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
    <script>
        Swal.fire({
            position: "center",
            icon: "error",
            title: "<?= $_SESSION['error_message'] ?>",
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php unset($_SESSION['error_message']); endif; ?>
</body>
</html>