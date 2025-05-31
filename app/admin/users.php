<?php
    // start session n connect to db
    session_start();
    require_once "db_conn.php";
    require_once "../public/functions.php";

    // get current user stuff
    $username = $_SESSION['username'];
    $fullname = $_SESSION['fullname'];
    $role = $_SESSION['role'];

    // check if db is working lol
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // delete user stuff
    if (isset($_POST['delete_user'])) {
        $id = $_POST['delete_user_id'];
        
        // First check if user is a voter and delete from voter table
        $checkVoterQuery = "SELECT full_name, role FROM user_table WHERE user_id = $id";
        $checkResult = mysqli_query($conn, $checkVoterQuery);
        $userData = mysqli_fetch_assoc($checkResult);
        
        if ($userData && strtolower($userData['role']) === 'voter') {
            // Get voter_id from voter_table
            $getVoterIdQuery = "SELECT voter_id FROM voter_table WHERE voter_name = '".$userData['full_name']."'";
            $voterIdResult = mysqli_query($conn, $getVoterIdQuery);
            $voterData = mysqli_fetch_assoc($voterIdResult);
            
            if ($voterData) {
                // First delete votes associated with this voter
                $deleteVotesQuery = "DELETE FROM vote_table WHERE voter_id = '".$voterData['voter_id']."'";
                mysqli_query($conn, $deleteVotesQuery);
                
                // Then delete from voter table
                $deleteVoterQuery = "DELETE FROM voter_table WHERE voter_id = '".$voterData['voter_id']."'";
                mysqli_query($conn, $deleteVoterQuery);
            }
        }
        
        // Finally delete from user table
        $deleteQuery = "DELETE FROM user_table WHERE user_id = $id";
        
        if (mysqli_query($conn, $deleteQuery)) {
            // Log before deletion only if we have user data
            if ($userData) {
                $description = "User deleted: " . $userData['full_name'];
                logActivity($conn, $_SESSION['username'], 'DELETE', $description);
            }
            
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
            
            // Log the modification
            $description = "User modified: $username (Role: $newRole)";
            logActivity($conn, $_SESSION['username'], 'MODIFY', $description);
            
            echo "<script>alert('User updated successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }

    // search function - look everywhere lol
    if(isset($_POST['search'])){
        $usersearch = $_POST['searchinput'];
        
        // search in all fields
        $selectsql = "Select * from user_table where 
                    full_name like '%".$usersearch."%' or 
                    username like '%".$usersearch."%' or 
                    role like '%".$usersearch."%' or 
                    email like '%".$usersearch."%'";
    }else{
        // show all users if no search
        $selectsql = "Select * from user_table";
    }

    // get results
    $result = mysqli_query($conn, $selectsql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
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
                <a href="users.php" class="sidebar-item active"><i class="bi bi-people-fill"></i>Users</a>
            <?php endif; ?>
            <a href="voter.php"><i class="bi bi-person-check-fill"></i> Voter Accounts</a>
            
            <div class="sidebar-category">Election Management</div>
            <a href="candidates.php"><i class="bi bi-person-badge-fill"></i> Candidate List</a>
            <a href="positions.php"><i class="bi bi-briefcase-fill"></i> Position List</a>
            <a href="votes.php"><i class="bi bi-box-seam"></i> Vote Records</a>
            
            <div class="sidebar-category">Reports</div>
            <a href="votecount.php"><i class="bi bi-bar-chart-line-fill"></i> Vote Statistics</a>
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
            <div class="container p-5 bg-light">
                <h1 class="mb-4">User Management</h1>
                
                <!-- Search Section -->
                <form action="" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchinput" class="form-control" placeholder="Search users...">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="search" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-person-plus-fill me-1"></i>Add New User
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
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>User ID</th>
                                <th>Full Name</th>
                                <th>Role</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>One-Time-Password</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $fieldname) : ?>
                                <tr class="user-row" onclick="showUserDetails('<?= $fieldname['full_name']; ?>', '<?= $fieldname['role']; ?>', '<?= $fieldname['username']; ?>', '<?= $fieldname['email']; ?>', '<?= $fieldname['password']; ?>')" style="cursor: pointer;">
                                    <td><?= $fieldname['user_id']; ?></td>
                                    <td><?= $fieldname['full_name']; ?></td>
                                    <td>
                                        <?php
                                        $roleClass = '';
                                        switch(strtolower($fieldname['role'])) {
                                            case 'admin':
                                                $roleClass = 'bg-danger';
                                                break;
                                            case 'organizer':
                                                $roleClass = 'bg-success';
                                                break;
                                            case 'voter':
                                                $roleClass = 'bg-primary';
                                                break;
                                            default:
                                                $roleClass = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?= $roleClass; ?>"><?= $fieldname['role']; ?></span>
                                    </td>
                                    <td><?= $fieldname['username']; ?></td>
                                    <td><?= $fieldname['email']; ?></td>
                                    <td><?= $fieldname['otp'] ?? 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        $status = $fieldname['status'] ?? 'Pending';
                                        $statusClass = $status === 'Verified' ? 'bg-success' : 'bg-warning';
                                        ?>
                                        <span class="badge <?= $statusClass; ?>"><?= $status; ?></span>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="btn btn-warning btn-sm" 
                                            onclick="editUser('<?= $fieldname['user_id']; ?>', '<?= $fieldname['full_name']; ?>', '<?= $fieldname['role']; ?>', '<?= $fieldname['username']; ?>', '<?= $fieldname['email']; ?>', '<?= $fieldname['password']; ?>'); event.stopPropagation();">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" 
                                            onclick="deleteUser('<?= $fieldname['user_id']; ?>', '<?= $fieldname['full_name']; ?>'); event.stopPropagation();">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else : ?>
                    <div class="alert alert-info">No users found.</div>
                <?php endif; ?>
            </div>

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="editUserModalLabel">
                                <i class="bi bi-pencil-square me-2"></i>Edit User
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="edit_user_id" id="editUserId">
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="edit_role" id="editRole" required>
                                                <option value="" disabled>Select Role</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Organizer">Organizer</option>
                                                <option value="Voter">Voter</option>
                                            </select>
                                            <label for="editRole">Role</label>
                                            <div class="invalid-feedback">Please select a role.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_name" id="editName" placeholder="Full Name" required>
                                            <label for="editName">Full Name</label>
                                            <div class="invalid-feedback">Please enter the full name.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_username" id="editUsername" placeholder="Username" required>
                                            <label for="editUsername">Username</label>
                                            <div class="invalid-feedback">Please enter a username.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" name="edit_email" id="editEmail" placeholder="Email" required>
                                            <label for="editEmail">Email</label>
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3 position-relative">
                                            <input type="password" class="form-control" name="edit_password" id="editPassword" placeholder="Password" required>
                                            <label for="editPassword">New Password</label>
                                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <div class="invalid-feedback">Please enter a new password.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="apply_edit" class="btn btn-warning">
                                        <i class="bi bi-save me-1"></i>Update User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addUserModalLabel">
                                <i class="bi bi-person-plus-fill me-2"></i>Add New User
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="add_role" id="addRole" required onchange="toggleVoterFields()">
                                                <option value="" selected disabled>Select Role</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Organizer">Organizer</option>
                                                <option value="Voter">Voter</option>
                                            </select>
                                            <label for="addRole">Role</label>
                                            <div class="invalid-feedback">Please select a role.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="card mb-4 border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title fw-bold text-primary mb-3">
                                                    <i class="bi bi-person-fill me-2"></i>User Information
                                                </h6>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="add_name" id="addName" placeholder="Full Name" required>
                                                    <label for="addName">Full Name</label>
                                                    <div class="invalid-feedback">Please enter the full name.</div>
                                                </div>

                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="add_username" id="addUsername" placeholder="Username" required>
                                                    <label for="addUsername">Username</label>
                                                    <div class="invalid-feedback">Please enter a username.</div>
                                                </div>

                                                <div class="form-floating mb-3">
                                                    <input type="email" class="form-control" name="add_email" id="addEmail" placeholder="Email" required>
                                                    <label for="addEmail">Email</label>
                                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                                </div>

                                                <div class="form-floating mb-3">
                                                    <input type="password" class="form-control" name="add_password" id="addPassword" placeholder="Password" required>
                                                    <label for="addPassword">Password</label>
                                                    <div class="invalid-feedback">Please enter a password.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Voter-specific fields (initially hidden) -->
                                    <div id="voterFields" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="card mb-4 border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold text-success mb-3">
                                                        <i class="bi bi-person-check-fill me-2"></i>Voter Information
                                                    </h6>
                                                    <div class="form-floating mb-3">
                                                        <input type="date" class="form-control" name="add_date_birth" id="addDateBirth" placeholder="Date of Birth">
                                                        <label for="addDateBirth">Date of Birth</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <select class="form-select" name="add_gender" id="addGender">
                                                            <option value="" selected disabled>Select Gender</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                        <label for="addGender">Gender</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="tel" class="form-control" name="add_contact" id="addContact" placeholder="Contact Information">
                                                        <label for="addContact">Contact Information</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" name="add_stu_id" id="addStuId" placeholder="Student ID">
                                                        <label for="addStuId">Student ID</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="add_user" class="btn btn-success">
                                        <i class="bi bi-person-plus-fill me-1"></i>Add User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details Modal -->
            <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <p id="detailName" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <p id="detailRole" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Username</label>
                                <p id="detailUsername" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <p id="detailEmail" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password (Hashed)</label>
                                <p id="detailPassword" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete user: <span id="deleteUserName" class="fw-bold"></span>?</p>
                            <p class="text-danger">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <form action="" method="POST">
                                <input type="hidden" name="delete_user_id" id="deleteUserId">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_user" class="btn btn-danger">Delete User</button>
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
                $result = mysqli_query($conn, $sql_command);
                
                if($result) {
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
// simple password toggle
document.getElementById('togglePassword').onclick = function() {
    let pass = document.getElementById('editPassword');
    let icon = this.querySelector('i');
    
    if (pass.type === 'password') {
        pass.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pass.type = 'password';
        icon.className = 'bi bi-eye';
    }
};

// check forms before submit
document.querySelectorAll('form').forEach(form => {
    form.onsubmit = function() {
        if (!this.checkValidity()) {
            return false; // stop if form is invalid
        }
    };
});

// clear forms when modals close
document.querySelectorAll('.modal').forEach(modal => {
    modal.onhidden = function() {
        let form = this.querySelector('form');
        if (form) form.reset(); // just clear the form
    };
});

// show user details
function showUserDetails(name, role, username, email, password) {
    // update modal content
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailRole').textContent = role;
    document.getElementById('detailUsername').textContent = username;
    document.getElementById('detailEmail').textContent = email;
    document.getElementById('detailPassword').textContent = password;
    
    // show modal
    new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
}

// edit user
function editUser(id, name, role, username, email, password) {
    // fill form
    document.getElementById('editUserId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editUsername').value = username;
    document.getElementById('editEmail').value = email;
    
    // set role
    let roleSelect = document.getElementById('editRole');
    roleSelect.value = role;
    
    // show modal
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

// delete user
function deleteUser(id, name) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUserName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}

// Toggle voter fields based on role selection
function toggleVoterFields() {
    const roleSelect = document.getElementById('addRole');
    const voterFields = document.getElementById('voterFields');
    const voterInputs = voterFields.getElementsByTagName('input');
    const voterSelects = voterFields.getElementsByTagName('select');
    
    if (roleSelect.value === 'Voter') {
        voterFields.style.display = 'block';
        // Make fields required
        Array.from(voterInputs).forEach(input => input.required = true);
        Array.from(voterSelects).forEach(select => select.required = true);
    } else {
        voterFields.style.display = 'none';
        // Remove required attribute
        Array.from(voterInputs).forEach(input => input.required = false);
        Array.from(voterSelects).forEach(select => select.required = false);
    }
}

// Clear voter fields when modal is closed
document.getElementById('addUserModal').addEventListener('hidden.bs.modal', function () {
    const voterFields = document.getElementById('voterFields');
    voterFields.style.display = 'none';
    const voterInputs = voterFields.getElementsByTagName('input');
    const voterSelects = voterFields.getElementsByTagName('select');
    Array.from(voterInputs).forEach(input => {
        input.required = false;
        input.value = '';
    });
    Array.from(voterSelects).forEach(select => {
        select.required = false;
        select.value = '';
    });
});
</script>
</body>
</html>
