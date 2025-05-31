<?php
    // start session n connect to db
    session_start();
    require_once "../php/db_conn.php";
    require_once "../php/add_logs.php";

    // get current user stuff
    $username = $_SESSION['username'];
    $fullname = $_SESSION['fullname'];
    $role = $_SESSION['role'];

    // check if db is working
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get action types for filter
    $action_types_query = "SELECT DISTINCT action FROM logs_table ORDER BY action";
    $action_types_result = mysqli_query($conn, $action_types_query);
    $action_types = [];
    while($row = mysqli_fetch_assoc($action_types_result)) {
        $action_types[] = $row['action'];
    }

    // Search and filter function
    if(isset($_POST['search']) || isset($_GET['type']) || isset($_GET['date'])) {
        $search = isset($_POST['searchinput']) ? mysqli_real_escape_string($conn, $_POST['searchinput']) : '';
        $type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : '';
        $date = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : '';
        
        $where_conditions = [];
        
        if(!empty($search)) {
            $where_conditions[] = "(u.username LIKE '%$search%' OR l.action LIKE '%$search%')";
        }
        
        if(!empty($type)) {
            $where_conditions[] = "l.action = '$type'";
        }
        
        if(!empty($date)) {
            $where_conditions[] = "DATE(l.DateTime) = '$date'";
        }
        
        $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
        
        $query = "SELECT l.*, u.username 
                 FROM logs_table l 
                 JOIN user_table u ON l.user_id = u.user_id 
                 $where_clause 
                 ORDER BY l.DateTime DESC";
    } else {
        $query = "SELECT l.*, u.username 
                 FROM logs_table l 
                 JOIN user_table u ON l.user_id = u.user_id 
                 ORDER BY l.DateTime DESC";
    }

    $result = mysqli_query($conn, $query);

    // Get statistics
    $stats_query = "SELECT 
        COUNT(*) as total_logs,
        COUNT(DISTINCT l.user_id) as unique_users,
        COUNT(DISTINCT l.action) as action_types,
        MAX(l.DateTime) as latest_activity
        FROM logs_table l";
    $stats_result = mysqli_query($conn, $stats_query);
    $stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
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

        .table-container {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-box {
            background: #fff;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-box .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }

        .search-box .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
        }

        .filter-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-badge:hover {
            opacity: 0.8;
        }

        .filter-badge.active {
            background-color: #198754;
            color: white;
        }

        .action-type {
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .action-type.create { background-color: #e8f5e9; color: #1b5e20; }
        .action-type.edit { background-color: #f3e5f5; color: #4a148c; }
        .action-type.delete { background-color: #ffebee; color: #b71c1c; }
        .action-type.verify { background-color: #e3f2fd; color: #0d47a1; }
        .action-type.vote { background-color: #e0f7fa; color: #006064; }
        .action-type.login { background-color: #fff3e0; color: #e65100; }
        .action-type.logout { background-color: #f5f5f5; color: #424242; }

        .logs-section {
            padding: 2rem 0;
        }

        .log-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }

        .log-card:hover {
            transform: translateY(-2px);
        }

        .log-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .log-body {
            padding: 1rem;
        }

        .log-footer {
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
        }

        .log-type {
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .log-type.vote { background: #e3f2fd; color: #0d47a1; }
        .log-type.account { background: #e8f5e9; color: #1b5e20; }
        .log-type.verify { background: #fff3e0; color: #e65100; }
        .log-type.delete { background: #ffebee; color: #b71c1c; }
        .log-type.modify { background: #f3e5f5; color: #4a148c; }

        .custom-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .custom-navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
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
                <a href="votecount.php"><i class="bi bi-bar-chart-line-fill"></i> Vote Statistics</a>
                <?php if (strtolower($role) !== 'organizer'): ?>
                    <a href="logs.php" class="sidebar-item active"><i class="bi bi-journal-text"></i> Activity Logs</a>
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
                    <h1 class="mb-4">System Logs</h1>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3>Total Logs</h3>
                                <div class="number"><?= $stats['total_logs'] ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3>Unique Users</h3>
                                <div class="number"><?= $stats['unique_users'] ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3>Action Types</h3>
                                <div class="number"><?= $stats['action_types'] ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3>Latest Activity</h3>
                                <div class="number" style="font-size: 1.2rem;">
                                    <?= date('M d, Y h:i A', strtotime($stats['latest_activity'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="search-box">
                        <form action="" method="post" class="row g-3 align-items-center">
                            <div class="col-md-8">
                                <input type="search" name="searchinput" class="form-control" 
                                       placeholder="Search by username or action...">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" name="search" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Search
                                </button>
                            </div>
                        </form>
                        
                        <!-- Action Type Filters -->
                        <div class="mt-3">
                            <h6 class="mb-2">Filter by Action Type:</h6>
                            <div>
                                <a href="?type=" class="badge bg-secondary filter-badge <?= empty($_GET['type']) ? 'active' : '' ?>">
                                    All
                                </a>
                                <?php foreach($action_types as $type): ?>
                                    <a href="?type=<?= urlencode($type) ?>" 
                                       class="badge bg-primary filter-badge <?= isset($_GET['type']) && $_GET['type'] === $type ? 'active' : '' ?>">
                                        <?= htmlspecialchars($type) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Date Filter -->
                        <div class="mt-3">
                            <h6 class="mb-2">Filter by Date:</h6>
                            <form action="" method="get" class="row g-3 align-items-center">
                                <?php if(isset($_GET['type'])): ?>
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($_GET['type']) ?>">
                                <?php endif; ?>
                                <div class="col-md-8">
                                    <input type="date" name="date" class="form-control" 
                                           value="<?= isset($_GET['date']) ? $_GET['date'] : '' ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Logs Section -->
                    <section class="logs-section">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <?php
                                    if ($result && $result->num_rows > 0) {
                                        while ($log = $result->fetch_assoc()) {
                                            $type_class = strtolower($log['action']);
                                            ?>
                                            <div class="log-card">
                                                <div class="log-header d-flex justify-content-between align-items-center">
                                                    <span class="log-type <?php echo $type_class; ?>">
                                                        <?php echo htmlspecialchars($log['action']); ?>
                                                    </span>
                                                    <small class="text-muted">
                                                        <?php echo date('F j, Y g:i A', strtotime($log['DateTime'])); ?>
                                                    </small>
                                                </div>
                                                <div class="log-footer">
                                                    <small>
                                                        <strong>User:</strong> <?php echo htmlspecialchars($log['username']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        echo '<div class="alert alert-info">No logs found.</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.custom-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>