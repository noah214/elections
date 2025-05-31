<?php
    // Start session and connect to database
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

    // Get all positions for dropdown
    $positionsQuery = "SELECT position_id, position_name FROM position_table ORDER BY position_name";
    $positionsResult = mysqli_query($conn, $positionsQuery);
    $positions = array();
    while($row = mysqli_fetch_assoc($positionsResult)) {
        $positions[] = $row;
    }

    // Delete position stuff
    if (isset($_POST['delete_position'])) {
        $id = $_POST['delete_position_id'];
        
        // Check if position is being used by any candidates
        $checkQuery = "SELECT COUNT(*) as count FROM candidate_table WHERE position_id = $id";
        $checkResult = mysqli_query($conn, $checkQuery);
        $row = mysqli_fetch_assoc($checkResult);
        
        if($row['count'] > 0) {
            echo "<script>alert('Cannot delete position: It is being used by candidates!');</script>";
        } else {
            // Delete position from database
            $deleteQuery = "DELETE FROM position_table WHERE position_id = $id";
        
        if (mysqli_query($conn, $deleteQuery)) {
                echo "<script>alert('Position deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
            }
        }
    }

    // add new position to db
    if (isset($_POST['add_position'])) {
        // get form data
        $name = $_POST['add_name'];
        $description = $_POST['add_description'];

        // basic validation
        if(empty($name) || empty($description)) {
            echo "<script>alert('Please fill in all fields!');</script>";
        } else {
            // Check if position name already exists
            $checkQuery = "SELECT COUNT(*) as count FROM position_table WHERE position_name = '$name'";
            $checkResult = mysqli_query($conn, $checkQuery);
            $row = mysqli_fetch_assoc($checkResult);
            
            if($row['count'] > 0) {
                echo "<script>alert('Position name already exists!');</script>";
            } else {
                // insert into position table
                $insertQuery = "INSERT INTO position_table (position_name, position_description) 
                                VALUES ('$name', '$description')";
                        
        if (mysqli_query($conn, $insertQuery)) {
                    echo "<script>alert('Position added successfully!');</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
                }
            }
        }
    }

    // edit position stuff
    if (isset($_POST['apply_edit'])) {
        // get form data
        $id = $_POST['edit_position_id'];
        $name = $_POST['edit_name'];
        $description = $_POST['edit_description'];

        // basic validation
        if(empty($name) || empty($description)) {
            echo "<script>alert('Please fill in all fields!');</script>";
        } else {
            // Check if position name already exists (excluding current position)
            $checkQuery = "SELECT COUNT(*) as count FROM position_table WHERE position_name = '$name' AND position_id != $id";
            $checkResult = mysqli_query($conn, $checkQuery);
            $row = mysqli_fetch_assoc($checkResult);
            
            if($row['count'] > 0) {
                echo "<script>alert('Position name already exists!');</script>";
            } else {
                // update position table
                $updateQuery = "UPDATE position_table 
                                SET position_name='$name', 
                                    position_description='$description'
                                WHERE position_id=$id";
                        
        if (mysqli_query($conn, $updateQuery)) {
                    echo "<script>alert('Position updated successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
                }
            }
        }
    }

    // search function
    if(isset($_POST['search'])){
        $positionsearch = $_POST['searchinput'];
        
        // search in name and description with candidate info
        if(!empty($positionsearch)) {
            $selectsql = "SELECT p.*, 
                         GROUP_CONCAT(CONCAT(c.candidate_name, '|', c.party_affiliation, '|', c.college) SEPARATOR '||') as candidates
                         FROM position_table p 
                         LEFT JOIN candidate_table c ON p.position_id = c.position_id 
                         WHERE p.position_name LIKE '%$positionsearch%' OR p.position_description LIKE '%$positionsearch%'
                         GROUP BY p.position_id";
        } else {
            $selectsql = "SELECT p.*, 
                         GROUP_CONCAT(CONCAT(c.candidate_name, '|', c.party_affiliation, '|', c.college) SEPARATOR '||') as candidates
                         FROM position_table p 
                         LEFT JOIN candidate_table c ON p.position_id = c.position_id 
                         GROUP BY p.position_id";
        }
    } else {
        $selectsql = "SELECT p.*, 
                     GROUP_CONCAT(CONCAT(c.candidate_name, '|', c.party_affiliation, '|', c.college) SEPARATOR '||') as candidates
                     FROM position_table p 
                     LEFT JOIN candidate_table c ON p.position_id = c.position_id 
                     GROUP BY p.position_id";
    }

    // get results
    $result = mysqli_query($conn, $selectsql);

    // get positions for dropdown
    $positionsQuery = "SELECT position_id, position_name FROM position_table";
    $positionsResult = mysqli_query($conn, $positionsQuery);
    $positions = [];
    while($row = mysqli_fetch_assoc($positionsResult)) {
        $positions[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Position Management</title>
    
    <!-- css stuff -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/global.css">
    <style>
        /* basic stuff */
        body { 
            min-height: 100vh; 
            overflow-x: hidden;
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

        /* badges */
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        /* buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1.2rem;
            font-size: 0.9rem;
        }

        .action-buttons .btn {
            margin: 0 2px;
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }

        .candidate-row {
            cursor: pointer;
        }

        /* modal stuff */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
            padding: 1rem 1.5rem;
        }

        .modal-footer {
            padding: 0.85rem 1.5rem;
        }

        /* form stuff */
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }

        .form-floating > label {
            padding: 1rem 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .invalid-feedback {
            font-size: 0.875em;
        }

        /* alerts */
        .alert {
            border-radius: 8px;
            margin-top: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }
        
        .form-floating > label {
            padding: 1rem 0.75rem;
        }
        
        .card-title {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }

        /* SQL Theme Styles */
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            color: #fff;
        }
        
        .text-primary {
            color: #0d6efd !important;
        }
        
        .bg-primary {
            background-color: #0d6efd !important;
        }
        
        #sqlCommand:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .border-primary {
            border-color: #0d6efd !important;
        }

        .card.bg-light {
            background-color: #f8f9fa !important;
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
                <a href="users.php"><i class="bi bi-people-fill"></i> Admin Users</a>
            <?php endif; ?>
            <a href="voter.php"><i class="bi bi-person-check-fill"></i> Voter Accounts</a>
            
            <div class="sidebar-category">Election Management</div>
            <a href="candidates.php"><i class="bi bi-person-badge-fill"></i> Candidates</a>
            <a href="positions.php" class="sidebar-item active"><i class="bi bi-briefcase-fill"></i> Positions</a>
            <a href="votes.php"><i class="bi bi-box-seam"></i> Votes</a>
            
            <div class="sidebar-category">Reports</div>
            <a href="votecount.php"><i class="bi bi-bar-chart-line-fill"></i> Vote Count</a>
            <?php if (strtolower($role) !== 'organizer'): ?>
                <a href="logs.php"><i class="bi bi-journal-text"></i> Logs</a>
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
                <h1 class="mb-4">Position Management</h1>
                
                <!-- Search Section -->
                <form action="" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchinput" class="form-control" placeholder="Search positions...">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="search" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                                <i class="bi bi-plus-circle-fill me-1"></i>Add New Position
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sqlCommandModal">
                                <i class="bi bi-database-fill me-1"></i>SQL Commands
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (mysqli_num_rows($result) > 0) : ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Position ID</th>
                                    <th>Position Name</th>
                                    <th>Description</th>
                                    <th>Assigned Candidates</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td><?= $row['position_id'] ?></td>
                                        <td><?= htmlspecialchars($row['position_name']) ?></td>
                                        <td><?= htmlspecialchars($row['position_description']) ?></td>
                                        <td>
                                            <?php if (!empty($row['candidates'])) : ?>
                                                <ul class="candidate-list">
                                                    <?php 
                                                    $candidates = explode('||', $row['candidates']);
                                                    foreach($candidates as $candidate) {
                                                        list($name, $party, $college) = explode('|', $candidate);
                                                    ?>
                                                        <li class="candidate-item">
                                                            <div class="candidate-info">
                                                                <div class="candidate-name"><?= htmlspecialchars($name) ?></div>
                                                                <div class="candidate-details">
                                                                    <span class="party-badge"><?= htmlspecialchars($party) ?></span>
                                                                    <span class="college-badge"><?= htmlspecialchars($college) ?></span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php else : ?>
                                                <span class="text-muted">No candidates assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-buttons">
                                            <button class="btn btn-warning btn-sm" 
                                                onclick="editPosition('<?= $row['position_id'] ?>', '<?= htmlspecialchars($row['position_name']) ?>', '<?= htmlspecialchars($row['position_description']) ?>')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                onclick="deletePosition('<?= $row['position_id'] ?>', '<?= htmlspecialchars($row['position_name']) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else : ?>
                    <div class="alert alert-info">No positions found.</div>
                <?php endif; ?>
            </div>

            <!-- Position Details Modal -->
            <div class="modal fade" id="positionDetailsModal" tabindex="-1" aria-labelledby="positionDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="positionDetailsModalLabel">
                                <i class="bi bi-briefcase-fill me-2"></i>Position Details
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Position ID</label>
                                        <p id="detailId" class="form-control-plaintext"></p>
                                        </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Position Name</label>
                                        <p id="detailName" class="form-control-plaintext"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Position Description</label>
                                        <p id="detailDescription" class="form-control-plaintext"></p>
                                        </div>
                                    </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="bi bi-people-fill me-2"></i>Assigned Candidates
                                            </h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div id="candidateList" class="list-group list-group-flush">
                                                <!-- Candidates will be loaded here -->
                                                <div class="text-center p-3">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                    </div>   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Position Modal -->
            <div class="modal fade" id="editPositionModal" tabindex="-1" aria-labelledby="editPositionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="editPositionModalLabel">
                                <i class="bi bi-pencil-square me-2"></i>Edit Position
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="edit_position_id" id="editPositionId">
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_name" id="editName" placeholder="Position Name" required>
                                            <label for="editName">Position Name</label>
                                            <div class="invalid-feedback">Please enter the position name.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" name="edit_description" id="editDescription" placeholder="Position Description" style="height: 100px" required></textarea>
                                            <label for="editDescription">Position Description</label>
                                            <div class="invalid-feedback">Please enter the position description.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="apply_edit" class="btn btn-warning">
                                        <i class="bi bi-save me-1"></i>Update Position
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Position Modal -->
            <div class="modal fade" id="addPositionModal" tabindex="-1" aria-labelledby="addPositionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addPositionModalLabel">
                                <i class="bi bi-plus-circle-fill me-2"></i>Add New Position
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="card mb-4 border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title fw-bold text-primary mb-3">
                                                    <i class="bi bi-briefcase-fill me-2"></i>Position Information
                                                </h6>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="add_name" id="addName" placeholder="Position Name" required>
                                                    <label for="addName">Position Name</label>
                                                    <div class="invalid-feedback">Please enter the position name.</div>
                                                </div>

                                                <div class="form-floating mb-3">
                                                    <textarea class="form-control" name="add_description" id="addDescription" placeholder="Position Description" style="height: 100px" required></textarea>
                                                    <label for="addDescription">Position Description</label>
                                                    <div class="invalid-feedback">Please enter the position description.</div>
                                                </div>
                                                    </div>
                                                </div>   
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="add_position" class="btn btn-success">
                                        <i class="bi bi-plus-circle-fill me-1"></i>Add Position
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Position Modal -->
            <div class="modal fade" id="deletePositionModal" tabindex="-1" aria-labelledby="deletePositionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deletePositionModalLabel">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete position: <span id="deletePositionName" class="fw-bold"></span>?</p>
                            <p class="text-danger">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <form action="" method="POST">
                                <input type="hidden" name="delete_position_id" id="deletePositionId">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_position" class="btn btn-danger">Delete Position</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SQL Command Modal -->
            <div class="modal fade" id="sqlCommandModal" tabindex="-1" aria-labelledby="sqlCommandModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="sqlCommandModalLabel">
                                <i class="bi bi-database-fill me-2"></i>SQL Command Interface
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="card border-primary bg-light">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="sqlCommand" class="form-label fw-bold text-primary">
                                                <i class="bi bi-code-square me-2"></i>Enter SQL Command:
                                            </label>
                                            <textarea class="form-control" id="sqlCommand" name="sql_command" rows="5" required 
                                                placeholder="Enter your SQL command here..." 
                                                style="font-family: 'Consolas', monospace; background-color: #fff; border: 1px solid #0d6efd;"></textarea>
                                            <div class="form-text text-primary mt-2">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                Warning: Be careful with SQL commands. They can modify or delete data permanently.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top border-primary">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="execute_sql" class="btn btn-primary">
                                        <i class="bi bi-play-fill me-1"></i>Execute Command
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Handle SQL command execution
            if(isset($_POST['execute_sql'])) {
                $sql_command = $_POST['sql_command'];
                $result_sql = mysqli_query($conn, $sql_command);
                
                if($result_sql) {
                    echo "<script>alert('SQL command executed successfully!');</script>";
                } else {
                    echo "<script>alert('Error executing SQL command: " . mysqli_error($conn) . "');</script>";
                }
            }
            ?>

        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Show position details when clicked
function showPositionDetails(id, name, description) {
    // Update modal info
    document.getElementById('detailId').textContent = id;
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailDescription').textContent = description;
    
    // Get candidates for this position
    fetch('get_position_candidates.php?position_id=' + id)
        .then(response => response.json())
        .then(candidates => {
            let candidateList = document.getElementById('candidateList');
            candidateList.innerHTML = '';
            
            if (candidates.length > 0) {
                candidates.forEach(candidate => {
                    let item = document.createElement('div');
                    item.className = 'list-group-item';
                    item.innerHTML = `
                        <h6>${candidate.candidate_name}</h6>
                        <small>${candidate.party_affiliation} - ${candidate.college}</small>
                    `;
                    candidateList.appendChild(item);
                });
            } else {
                candidateList.innerHTML = '<div class="list-group-item">No candidates yet</div>';
            }
        });
    
    // Show the modal
    let modal = new bootstrap.Modal(document.getElementById('positionDetailsModal'));
    modal.show();
}

// Edit position
function editPosition(id, name, description) {
    document.getElementById('editPositionId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    
    let modal = new bootstrap.Modal(document.getElementById('editPositionModal'));
    modal.show();
}

// Delete position
function deletePosition(id, name) {
    document.getElementById('deletePositionId').value = id;
    document.getElementById('deletePositionName').textContent = name;
    
    let modal = new bootstrap.Modal(document.getElementById('deletePositionModal'));
    modal.show();
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Add validation to forms
    let forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Clear search box
    let searchInput = document.querySelector('input[name="searchinput"]');
    if (searchInput) {
        searchInput.value = '';
    }
});

// Clear forms when modals close
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', function() {
        let form = this.querySelector('form');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
        }
    });
});
</script>
</body>
</html>