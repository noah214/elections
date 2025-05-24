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
    if (isset($_POST['delete_user'])) {
        $id = $_POST['delete_user_id'];
        $deleteQuery = "DELETE FROM user_table WHERE user_id = $id";
        
        if (mysqli_query($conn, $deleteQuery)) {
            echo "<script>alert('User deleted successfully!'); window.location.href=window.location.href;</script>";
        }else{
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

        // insert into db
        $insertQuery = "INSERT INTO user_table (full_name, role, username, password, email) 
                        VALUES ('$name', '$role', '$username', '$password', '$email')";
                        
        if (mysqli_query($conn, $insertQuery)) {
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
        $role = $_POST['edit_role'];
        $username = $_POST['edit_username'];
        $email = $_POST['edit_email'];
        $password = md5($_POST['edit_password']); // hash it again

        // update in db
        $updateQuery = "UPDATE user_table 
                        SET full_name='$name', 
                            role='$role', 
                            username='$username', 
                            email='$email', 
                            password='$password'
                        WHERE user_id=$id";
                        
        if (mysqli_query($conn, $updateQuery)) {
            echo "<script>alert('User updated successfully!'); window.location.href=window.location.href;</script>";
        }else{
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
    
    <style>
        /* basic stuff */
        body { 
            min-height: 100vh; 
        }
        
        /* sidebar stuff */
        .sidebar {
            height: 100vh;
            background-color: #000;
            color: #ffc107;
            display: flex;
            flex-direction: column;
            padding-top: 1rem;
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
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-header">
                <h4>BOTOmasino Elections</h4>
            </div>
            
            <div class="sidebar-category">User Management</div>
            <a href="admin_dashboard.php" class="sidebar-item active">
                <i class="bi bi-people-fill"></i> Users
            </a>
            <a href="#"><i class="bi bi-person-check-fill"></i> Voters</a>
            
            <div class="sidebar-category">Election Management</div>
            <a href="#"><i class="bi bi-person-badge-fill"></i> Candidates</a>
            <a href="#"><i class="bi bi-briefcase-fill"></i> Positions</a>
            <a href="#"><i class="bi bi-box-seam"></i> Votes</a>
            
            <div class="sidebar-category">Reports</div>
            <a href="#"><i class="bi bi-bar-chart-line-fill"></i> Vote Count</a>
            <a href="#"><i class="bi bi-journal-text"></i> Logs</a>
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
                                            <input type="text" class="form-control" name="edit_name" id="editName" placeholder="Full Name" required>
                                            <label for="editName">Full Name</label>
                                            <div class="invalid-feedback">Please enter the full name.</div>
                                        </div>
                                    </div>
                                    
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
                                            <input type="text" class="form-control" name="add_name" id="addName" placeholder="Full Name" required>
                                            <label for="addName">Full Name</label>
                                            <div class="invalid-feedback">Please enter the full name.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="add_role" id="addRole" required>
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
                                <label class="form-label fw-bold">Password</label>
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
    document.getElementById('editPassword').value = password;
    
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
</script>
</body>
</html>
