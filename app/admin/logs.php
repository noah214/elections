<?php
    // start session n connect to db
    session_start();
    require_once "db_conn.php";

    // get current user stuff
    $username = $_SESSION['username'];
    $fullname = $_SESSION['fullname'];
    $role = $_SESSION['role'];

    // check if db is working lol
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // search function
    if(isset($_POST['search'])){
        $search = $_POST['searchinput'];
        
        if(!empty($search)) {
            $query = "SELECT l.*, u.username, u.full_name 
                     FROM logs_table l
                     JOIN user_table u ON l.user_id = u.user_id
                     WHERE l.action LIKE '%$search%' 
                     OR u.username LIKE '%$search%'
                     OR u.full_name LIKE '%$search%'
                     ORDER BY l.DateTime DESC";
        } else {
            $query = "SELECT l.*, u.username, u.full_name 
                     FROM logs_table l
                     JOIN user_table u ON l.user_id = u.user_id
                     ORDER BY l.DateTime DESC";
        }
    } else {
        $query = "SELECT l.*, u.username, u.full_name 
                 FROM logs_table l
                 JOIN user_table u ON l.user_id = u.user_id
                 ORDER BY l.DateTime DESC";
    }

    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    
    <!-- css stuff -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* basic stuff */
        body { 
            min-height: 100vh; 
            overflow-x: hidden;
        }
        
        /* sidebar stuff */
        .sidebar {
            height: 100vh;
            background-color: #000;
            color: #ffc107;
            display: flex;
            flex-direction: column;
            padding-top: 1rem;
            position: fixed;
            top: 0;
            left: 0;
            width: inherit;
            max-width: inherit;
            z-index: 1000;
            overflow-y: auto;
        }

        /* Main content adjustment */
        main {
            margin-left: 16.66667%; /* This matches col-md-2 width */
            width: 83.33333%; /* This ensures main content takes remaining width */
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

        /* table container */
        .table-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            margin-top: 1rem;
        }

        .table-container::-webkit-scrollbar {
            width: 10px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #000;
            border-radius: 5px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #ffc107;
            border-radius: 5px;
            border: 2px solid #000;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #e0a800;
        }

        /* sidebar scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #000;
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #ffc107;
            border-radius: 4px;
            border: 2px solid #000;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #e0a800;
        }

        /* For Firefox */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: #ffc107 #000;
        }

        /* header thing */
        .sidebar-header {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #ffc107;
            margin-bottom: 1rem;
        }

        .sidebar-header h4 {
            color: #ffc107;
            margin: 0;
            font-size: 1.5rem;
        }

        /* links n stuff */
        .sidebar a {
            color: #ffc107;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .sidebar a i {
            margin-right: 8px;
        }

        .sidebar a:hover {
            background-color: #212529;
        }

        /* categories */
        .sidebar-category {
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 1rem 1rem 0.5rem;
            margin-top: 1rem;
            border-bottom: 1px solid #2c3034;
        }

        /* active stuff */
        .sidebar .sidebar-item {
            color: #efb409;
            background: transparent;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar .sidebar-item.active {
            background: #efb409;
            color: #212529;
            font-weight: bold;
        }

        .sidebar .sidebar-item.active i {
            color: #212529;
        }

        .sidebar .sidebar-item i {
            color: #efb409;
            margin-right: 10px;
        }

        /* table stuff */
        .table-responsive {
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }

        /* action colors */
        .action-create {
            color: #198754;
        }
        
        .action-edit {
            color: #0d6efd;
        }
        
        .action-delete {
            color: #dc3545;
        }
        
        .action-verify {
            color: #6f42c1;
        }
        
        .action-vote {
            color: #fd7e14;
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
            
            <div class="sidebar-category">User Management</div>
            <?php if (strtolower($role) !== 'organizer'): ?>
                <a href="users.php"><i class="bi bi-people-fill"></i> Admin Users</a>
            <?php endif; ?>
            <a href="voter.php"><i class="bi bi-person-check-fill"></i> Voter Accounts</a>
            
            <div class="sidebar-category">Election Management</div>
            <a href="candidates.php"><i class="bi bi-person-badge-fill"></i> Candidates</a>
            <a href="positions.php"><i class="bi bi-briefcase-fill"></i> Positions</a>
            <a href="votes.php"><i class="bi bi-box-seam"></i> Votes</a>
            
            <div class="sidebar-category">Reports</div>
            <a href="votecount.php"><i class="bi bi-bar-chart-line-fill"></i> Vote Count</a>
            <?php if (strtolower($role) !== 'organizer'): ?>
                <a href="logs.php" class="sidebar-item active"><i class="bi bi-journal-text"></i> Logs</a>
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
            <div class="container p-5 bg-light">
                <h1 class="mb-4">System Logs</h1>
                
                <!-- Search Section -->
                <form action="" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchinput" class="form-control" placeholder="Search logs...">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="search" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (mysqli_num_rows($result) > 0) : ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Log ID</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) : 
                                    // Determine action class based on action type
                                    $actionClass = '';
                                    if (strpos(strtolower($row['action']), 'create') !== false) {
                                        $actionClass = 'action-create';
                                    } elseif (strpos(strtolower($row['action']), 'edit') !== false) {
                                        $actionClass = 'action-edit';
                                    } elseif (strpos(strtolower($row['action']), 'delete') !== false) {
                                        $actionClass = 'action-delete';
                                    } elseif (strpos(strtolower($row['action']), 'verify') !== false) {
                                        $actionClass = 'action-verify';
                                    } elseif (strpos(strtolower($row['action']), 'vote') !== false) {
                                        $actionClass = 'action-vote';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $row['log_id'] ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?> (<?= htmlspecialchars($row['full_name']) ?>)</td>
                                        <td class="<?= $actionClass ?>"><?= htmlspecialchars($row['action']) ?></td>
                                        <td><?= date('M d, Y h:i A', strtotime($row['DateTime'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else : ?>
                    <div class="alert alert-info">No logs found.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>