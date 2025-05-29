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

    // delete user stuff
    // delete user stuff
    if (isset($_POST['delete_user'])) {
        $id = $_POST['delete_user_id'];
        
        // First check if user is a voter and delete from voter table
        $checkVoterQuery = "SELECT full_name, role FROM user_table WHERE user_id = $id";
        $checkResult = mysqli_query($conn, $checkVoterQuery);
        $userData = mysqli_fetch_assoc($checkResult);
        
        if ($userData && strtolower($userData['role']) === 'voter') {
            // Delete from voter table first
            $deleteVoterQuery = "DELETE FROM voter_table WHERE voter_name = '".$userData['full_name']."'";
            mysqli_query($conn, $deleteVoterQuery);
        }
        
        // Then delete from user table
        $deleteQuery = "DELETE FROM user_table WHERE user_id = $id";
        
        if (mysqli_query($conn, $deleteQuery)) {
            echo "<script>alert('User deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    }

    // add new user to db
    if (isset($_POST['add_user'])) {
        // get form data
        $name = $_POST['add_name'];
        $role = $_POST['add_role'];
        $username = $_POST['add_username'];
        $password = md5($_POST['add_password']); // hash it for security
        $email = $_POST['add_email'];
        $status = 'Verified'; // default status


        // insert into user table
        $insertQuery = "INSERT INTO user_table (full_name, role, username, password, email, otp, status) 
                        VALUES ('$name', '$role', '$username', '$password', '$email', NULL, '$status')";
                        
        if (mysqli_query($conn, $insertQuery)) {
            // If user role is voter, also add to voter table with additional fields
            if (strtolower($role) === 'voter') {
                $voterDateBirth = $_POST['add_date_birth'] ?? '';
                $voterGender = isset($_POST['add_gender']) ? $_POST['add_gender'] : '';
                $voterContact = isset($_POST['add_contact']) ? $_POST['add_contact'] : '';
                $voterStuId = isset($_POST['add_stu_id']) ? $_POST['add_stu_id'] : '';
                
                $insertVoterQuery = "INSERT INTO voter_table (voter_name, date_of_birth, gender, contact_information, student_id) 
                                   VALUES ('$name', '$voterDateBirth', '$voterGender', '$voterContact', '$voterStuId')";
                mysqli_query($conn, $insertVoterQuery);
            }
            
            echo "<script>alert('User added successfully!');</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    // edit user stuff
    if (isset($_POST['apply_edit'])) {
        // get form data
        $id = $_POST['edit_user_id'];
        $name = $_POST['edit_name'];
        $newRole = $_POST['edit_role'];
        $username = $_POST['edit_username'];
        $email = $_POST['edit_email'];
        $password = md5($_POST['edit_password']); // hash it again

        // Get old user data
        $getOldDataQuery = "SELECT full_name, role FROM user_table WHERE user_id = $id";
        $oldDataResult = mysqli_query($conn, $getOldDataQuery);
        $oldData = mysqli_fetch_assoc($oldDataResult);
        $oldName = $oldData['full_name'];
        $oldRole = $oldData['role'];

        // update user table
        $updateQuery = "UPDATE user_table 
                        SET full_name='$name', 
                            role='$newRole', 
                            username='$username', 
                            email='$email', 
                            password='$password'
                        WHERE user_id=$id";
                        
        if (mysqli_query($conn, $updateQuery)) {
            // Handle voter table changes
            if (strtolower($oldRole) === 'voter' && strtolower($newRole) !== 'voter') {
                // User was voter but now isn't - remove from voter table
                $deleteVoterQuery = "DELETE FROM voter_table WHERE voter_name = '$oldName'";
                mysqli_query($conn, $deleteVoterQuery);
            } else if (strtolower($oldRole) !== 'voter' && strtolower($newRole) === 'voter') {
                // User wasn't voter but now is - add to voter table
                $voterDateBirth = isset($_POST['edit_date_birth']) ? $_POST['edit_date_birth'] : '';
                $voterGender = isset($_POST['edit_gender']) ? $_POST['edit_gender'] : '';
                $voterContact = isset($_POST['edit_contact']) ? $_POST['edit_contact'] : '';
                $voterStuId = isset($_POST['edit_stu_id']) ? $_POST['edit_stu_id'] : '';
                
                $insertVoterQuery = "INSERT INTO voter_table (voter_name, date_of_birth, gender, contact_information, student_id) 
                                   VALUES ('$name', '$voterDateBirth', '$voterGender', '$voterContact', '$voterStuId')";
                mysqli_query($conn, $insertVoterQuery);
            } else if (strtolower($oldRole) === 'voter' && strtolower($newRole) === 'voter') {
                // User was and still is voter - update voter table
                $voterDateBirth = isset($_POST['edit_date_birth']) ? $_POST['edit_date_birth'] : '';
                $voterGender = isset($_POST['edit_gender']) ? $_POST['edit_gender'] : '';
                $voterContact = isset($_POST['edit_contact']) ? $_POST['edit_contact'] : '';
                $voterStuId = isset($_POST['edit_stu_id']) ? $_POST['edit_stu_id'] : '';
                
                $updateVoterQuery = "UPDATE voter_table 
                                   SET voter_name='$name',
                                       date_of_birth='$voterDateBirth',
                                       gender='$voterGender',
                                       contact_information='$voterContact',
                                       student_id='$voterStuId'
                                   WHERE voter_name='$oldName'";
                mysqli_query($conn, $updateVoterQuery);
            }
            
            echo "<script>alert('User updated successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }

    // search function
    if(isset($_POST['search'])){
        $search = $_POST['searchinput'];
        
        if(!empty($search)) {
            $query = "SELECT v.*, c.candidate_name, c.party_affiliation, p.position_name, u.username
                     FROM vote_table v
                     JOIN candidate_table c ON v.candidate_id = c.candidate_id
                     JOIN position_table p ON c.position_id = p.position_id
                     JOIN user_table u ON v.voter_id = u.user_id
                     WHERE c.candidate_name LIKE '%$search%' 
                     OR p.position_name LIKE '%$search%'
                     OR u.username LIKE '%$search%'
                     ORDER BY v.vote_timestamp DESC";
        } else {
            $query = "SELECT v.*, c.candidate_name, c.party_affiliation, p.position_name, u.username
                     FROM vote_table v
                     JOIN candidate_table c ON v.candidate_id = c.candidate_id
                     JOIN position_table p ON c.position_id = p.position_id
                     JOIN user_table u ON v.voter_id = u.user_id
                     ORDER BY v.vote_timestamp DESC";
        }
    } else {
        $query = "SELECT v.*, c.candidate_name, c.party_affiliation, p.position_name, u.username
                 FROM vote_table v
                 JOIN candidate_table c ON v.candidate_id = c.candidate_id
                 JOIN position_table p ON c.position_id = p.position_id
                 JOIN user_table u ON v.voter_id = u.user_id
                 ORDER BY v.vote_timestamp DESC";
    }

    $result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votes</title>
    
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

        .user-row {
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
            <a href="votes.php" class="sidebar-item active"><i class="bi bi-box-seam"></i> Votes</a>
            
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
                <h1 class="mb-4">Votes</h1>
                
                <!-- Search Section -->
                <form action="" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchinput" class="form-control" placeholder="Search votes...">
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
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Vote ID</th>
                                    <th>Voter ID</th>
                                    <th>Candidate ID</th>
                                    <th>Voter</th>
                                    <th>Position</th>
                                    <th>Candidate</th>
                                    <th>Party</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td><?= $row['vote_id'] ?></td>
                                        <td><?= $row['voter_id'] ?></td>
                                        <td><?= $row['candidate_id'] ?></td>
                                        <td><?= $row['username'] ?></td>
                                        <td><?= $row['position_name'] ?></td>
                                        <td><?= $row['candidate_name'] ?></td>
                                        <td><?= $row['party_affiliation'] ?></td>
                                        <td><?= date('M d, Y h:i A', strtotime($row['vote_timestamp'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else : ?>
                    <div class="alert alert-info">No votes found.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
