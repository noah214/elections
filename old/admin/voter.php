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

    // delete voter stuff
    if (isset($_POST['delete_voter'])) {
        $id = $_POST['delete_voter_id'];
        
        // First get voter name to delete from user table
        $getVoterQuery = "SELECT voter_name FROM voter_table WHERE voter_id = $id";
        $voterResult = mysqli_query($conn, $getVoterQuery);
        $voterData = mysqli_fetch_assoc($voterResult);
        
        if ($voterData) {
            // Delete from user table first
            $deleteUserQuery = "DELETE FROM user_table WHERE full_name = '".$voterData['voter_name']."' AND role = 'Voter'";
            mysqli_query($conn, $deleteUserQuery);
            
            // Then delete from voter table
            $deleteVoterQuery = "DELETE FROM voter_table WHERE voter_id = $id";
            
            if (mysqli_query($conn, $deleteVoterQuery)) {
                echo "<script>alert('Voter deleted successfully!'); window.location.href=window.location.href;</script>";
            } else {
                echo "Error deleting record: " . mysqli_error($conn);
            }
        }
    }

    // add new voter to db
    if (isset($_POST['add_voter'])) {
        //User Table
        $userName = $_POST['add_name'];
        $userRole = "Voter";
        $userUsername = $_POST['add_username'];
        $userPassword = md5($_POST['add_password']); // hash it for security
        $userEmail = $_POST['add_email'];

        //Voter Table
        $voterName = $_POST['add_name'];
        $voterDate_birth = $_POST['date_birth'];
        $voterGender = $_POST['gender'];
        $voterContact = $_POST['contact'];
        $voterStu_id = $_POST['stu_id'];

        // insert user to user table
        $insertUser = "INSERT INTO user_table (full_name, role, username, password, email) 
                        VALUES ('$userName', '$userRole', '$userUsername', '$userPassword', '$userEmail')";
                        
        //Inserting data in VoterTable
        $insertVoter = "INSERT INTO voter_table (voter_name, date_of_birth, gender, contact_information, student_id) 
                        VALUES ('$voterName', '$voterDate_birth', '$voterGender', '$voterContact', '$voterStu_id')";
                        
        if (mysqli_query($conn, $insertUser) && mysqli_query($conn, $insertVoter)) {
            echo "<script>alert('Voter added successfully!');</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    // edit voter stuff
    if (isset($_POST['apply_edit'])) {
        // get form data
        $editVoterID = $_POST['edit_voter_id'];
        $editName = $_POST['edit_name'];
        $editBirth = $_POST['edit_birth'];
        $editGender = $_POST['edit_gender'];
        $editContact = $_POST['edit_contact'];
        $editStu_id = $_POST['edit_stu_id'];

        // Get old name first
        $getOldNameQuery = "SELECT voter_name FROM voter_table WHERE voter_id = $editVoterID";
        $oldNameResult = mysqli_query($conn, $getOldNameQuery);
        $oldNameData = mysqli_fetch_assoc($oldNameResult);
        $oldName = $oldNameData['voter_name'];

        // update in db
        $updateVoterQuery = "UPDATE voter_table 
                        SET voter_name='$editName', 
                            date_of_birth='$editBirth', 
                            gender='$editGender', 
                            contact_information='$editContact', 
                            student_id='$editStu_id'
                        WHERE voter_id=$editVoterID";

        $updateUserQuery = "UPDATE user_table 
                        SET full_name='$editName'
                        WHERE full_name='$oldName' AND role='Voter'";

                        
        if (mysqli_query($conn, $updateVoterQuery) && mysqli_query($conn, $updateUserQuery)) {
            echo "<script>alert('Voter updated successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }

    // search function - look everywhere lol
    if(isset($_POST['search'])){
        $usersearch = $_POST['searchinput'];

        // search in all fields - fixed the SQL syntax
        $selectsql = "SELECT * FROM voter_table WHERE 
                    voter_name LIKE '%".$usersearch."%' OR 
                    date_of_birth LIKE '%".$usersearch."%' OR 
                    gender LIKE '%".$usersearch."%' OR 
                    contact_information LIKE '%".$usersearch."%' OR
                    student_id LIKE '%".$usersearch."%'";
    } else {
        // show all voters if no search
        $selectsql = "SELECT * FROM voter_table";
    }

    // get results
    $result = mysqli_query($conn, $selectsql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Management - Dashboard</title>
    
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

        .voter-row {
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
                <a href="users.php"><i class="bi bi-people-fill"></i> Admin Users</a>
            <?php endif; ?>
            <a href="voter.php" class="sidebar-item active"><i class="bi bi-person-check-fill"></i> Voter Accounts</a>
            
            <div class="sidebar-category">Election Management</div>
            <a href="candidates.php"><i class="bi bi-person-badge-fill"></i> Candidates</a>
            <a href="positions.php"><i class="bi bi-briefcase-fill"></i> Positions</a>
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
                <h1 class="mb-4">Voter Account Management</h1>
                
                <!-- Search Section -->
                <form action="" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchinput" class="form-control" placeholder="Search voter accounts...">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="search" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVoterModal">
                                <i class="bi bi-person-plus-fill me-1"></i>Add New Voter
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
                                    <th>Voter ID</th>
                                    <th>Voter Name</th>
                                    <th>Date of Birth</th>
                                    <th>Gender</th>
                                    <th>Contact Information</th>
                                    <th>Student ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $fieldname) : ?>
                                    <tr class="voter-row" onclick="showVoterDetails('<?= $fieldname['voter_name']; ?>', '<?= $fieldname['date_of_birth']; ?>', '<?= $fieldname['gender']; ?>', '<?= $fieldname['contact_information']; ?>', '<?= $fieldname['student_id']; ?>')" style="cursor: pointer;">
                                        <td><?= $fieldname['voter_id']; ?></td>
                                        <td><?= $fieldname['voter_name']; ?></td>
                                        <td><?= $fieldname['date_of_birth']; ?></td>
                                        <td>
                                            <?php
                                            $genderClass = '';
                                            switch(strtolower($fieldname['gender'])) {
                                                case 'male':
                                                    $genderClass = 'bg-primary';
                                                    break;
                                                case 'female':
                                                    $genderClass = 'bg-danger';
                                                    break;
                                                default:
                                                    $genderClass = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?= $genderClass; ?>"><?= $fieldname['gender']; ?></span>
                                        </td>
                                        <td><?= $fieldname['contact_information']; ?></td>
                                        <td><?= $fieldname['student_id']; ?></td>
                                        <td class="action-buttons">
                                            <button class="btn btn-warning btn-sm" 
                                                onclick="editVoter('<?= $fieldname['voter_id']; ?>', '<?= $fieldname['voter_name']; ?>', '<?= $fieldname['date_of_birth']; ?>', '<?= $fieldname['gender']; ?>', '<?= $fieldname['contact_information']; ?>', '<?= $fieldname['student_id']; ?>'); event.stopPropagation();">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                onclick="deleteVoter('<?= $fieldname['voter_id']; ?>', '<?= $fieldname['voter_name']; ?>'); event.stopPropagation();">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else : ?>
                    <div class="alert alert-info">No voters found.</div>
                <?php endif; ?>
            </div>

            <!-- Edit Voter Modal -->
            <div class="modal fade" id="editVoterModal" tabindex="-1" aria-labelledby="editVoterModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="editVoterModalLabel">
                                <i class="bi bi-pencil-square me-2"></i>Edit Voter
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="edit_voter_id" id="editVoterId">
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_name" id="editName" placeholder="Voter Name" required>
                                            <label for="editName">Voter Name</label>
                                            <div class="invalid-feedback">Please enter the voter name.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="date" class="form-control" name="edit_birth" id="editBirth" required>
                                            <label for="editBirth">Date of Birth</label>
                                            <div class="invalid-feedback">Please select the date of birth.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="edit_gender" id="editGender" required>
                                                <option value="" disabled>Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <label for="editGender">Gender</label>
                                            <div class="invalid-feedback">Please select a gender.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_contact" id="editContact" placeholder="Contact Information" required>
                                            <label for="editContact">Contact Information</label>
                                            <div class="invalid-feedback">Please enter contact information.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_stu_id" id="editStuId" placeholder="Student ID" required>
                                            <label for="editStuId">Student ID</label>
                                            <div class="invalid-feedback">Please enter student ID.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="apply_edit" class="btn btn-warning">
                                        <i class="bi bi-save me-1"></i>Update Voter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Voter Modal -->
            <div class="modal fade" id="addVoterModal" tabindex="-1" aria-labelledby="addVoterModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addVoterModalLabel">
                                <i class="bi bi-person-plus-fill me-2"></i>Add New Voter
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="add_name" id="addName" placeholder="Voter Name" required>
                                            <label for="addName">Voter Name</label>
                                            <div class="invalid-feedback">Please enter the voter name.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="add_username" id="addUsername" placeholder="Username" required>
                                            <label for="addUsername">Username</label>
                                            <div class="invalid-feedback">Please enter a username.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" name="add_email" id="addEmail" placeholder="Email" required>
                                            <label for="addEmail">Email</label>
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="add_password" id="addPassword" placeholder="Password" required>
                                            <label for="addPassword">Password</label>
                                            <div class="invalid-feedback">Please enter a password.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="date" class="form-control" name="date_birth" id="addBirth" required>
                                            <label for="addBirth">Date of Birth</label>
                                            <div class="invalid-feedback">Please select the date of birth.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="gender" id="addGender" required>
                                                <option value="" selected disabled>Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <label for="addGender">Gender</label>
                                            <div class="invalid-feedback">Please select a gender.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="contact" id="addContact" placeholder="Contact Information" required>
                                            <label for="addContact">Contact Information</label>
                                            <div class="invalid-feedback">Please enter contact information.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="stu_id" id="addStuId" placeholder="Student ID" required>
                                            <label for="addStuId">Student ID</label>
                                            <div class="invalid-feedback">Please enter student ID.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="add_voter" class="btn btn-success">
                                        <i class="bi bi-person-plus-fill me-1"></i>Add Voter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voter Details Modal -->
            <div class="modal fade" id="voterDetailsModal" tabindex="-1" aria-labelledby="voterDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="voterDetailsModalLabel">
                                <i class="bi bi-person me-2"></i>Voter Details
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Voter Name</label>
                                <p id="detailName" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <p id="detailBirth" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gender</label>
                                <p id="detailGender" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contact Information</label>
                                <p id="detailContact" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Student ID</label>
                                <p id="detailStuId" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteVoterModal" tabindex="-1" aria-labelledby="deleteVoterModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteVoterModalLabel">
                                <i class="bi bi-trash me-2"></i>Confirm Voter Deletion?
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete voter: <span id="deleteVoterName" class="fw-bold"></span>?</p>
                            <p class="text-danger">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <form action="" method="POST">
                                <input type="hidden" name="delete_voter_id" id="deleteVoterId">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_voter" class="btn btn-danger">Delete Voter</button>
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

        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
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
    modal.addEventListener('hidden.bs.modal', function() {
        let form = this.querySelector('form');
        if (form) form.reset(); // just clear the form
    });
});

// show voter details
function showVoterDetails(name, birth, gender, contact, stuId) {
    // update modal content
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailBirth').textContent = birth;
    document.getElementById('detailGender').textContent = gender;
    document.getElementById('detailContact').textContent = contact;
    document.getElementById('detailStuId').textContent = stuId;
    
    // show modal
    new bootstrap.Modal(document.getElementById('voterDetailsModal')).show();
}

// edit voter
function editVoter(id, name, birth, gender, contact, stuId) {
    // fill form
    document.getElementById('editVoterId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editBirth').value = birth;
    document.getElementById('editGender').value = gender;
    document.getElementById('editContact').value = contact;
    document.getElementById('editStuId').value = stuId;
    
    // show modal
    new bootstrap.Modal(document.getElementById('editVoterModal')).show();
}

// delete voter
function deleteVoter(id, name) {
    document.getElementById('deleteVoterId').value = id;
    document.getElementById('deleteVoterName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteVoterModal')).show();
}
</script>
</body>
</html>
