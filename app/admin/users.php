<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            min-height: 100vh;
        }

        .sidebar {
            height: 100vh;
            background-color: #000;
            color: #ffc107;
            display: flex;
            flex-direction: column;
            padding-top: 1rem;
        }

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
        .navbar-custom {
            background-color: #001f3f;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #ffc107 !important;
        }

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

        .action-buttons .btn {
            margin: 0 2px;
        }

        .user-row {
            cursor: pointer;
        }

        .user-row:hover {
            background-color: #f8f9fa;
        }

        .search-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 600px;
            display: flex;
            gap: 0.5rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        .search-box input:focus {
            border-color: #001f3f;
            box-shadow: 0 0 0 4px rgba(0, 31, 63, 0.1);
            outline: none;
        }

        .search-box i {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-btn {
            background: #001f3f;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 31, 63, 0.2);
            white-space: nowrap;
        }

        .search-btn:hover {
            background: #003366;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 31, 63, 0.3);
        }

        .search-btn i {
            position: static;
            transform: none;
        }

        .add-user-btn {
            background: linear-gradient(45deg, #001f3f, #003366);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 31, 63, 0.2);
        }

        .add-user-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 31, 63, 0.3);
            background: linear-gradient(45deg, #003366, #001f3f);
        }

        .add-user-btn i {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .search-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: 100%;
            }
            
            .add-user-btn {
                justify-content: center;
            }
        }

        .sidebar-category {
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 1rem 1rem 0.5rem;
            margin-top: 1rem;
            border-bottom: 1px solid #2c3034;
        }

        .sidebar-category:first-child {
            margin-top: 0;
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
            <?php
            require_once "db_conn.php";

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Handle Delete User
            if (isset($_POST['delete_user'])) {
                $id = $_POST['delete_user_id'];
                $deleteQuery = "DELETE FROM user_table WHERE user_id = $id";
                if (mysqli_query($conn, $deleteQuery)) {
                    echo "<script>alert('User deleted successfully!'); window.location.href=window.location.href;</script>";
                } else {
                    echo "Error deleting record: " . mysqli_error($conn);
                }
            }

            // Handle Add User
            if (isset($_POST['add_user'])) {
                $name = mysqli_real_escape_string($conn, $_POST['add_name']);
                $role = mysqli_real_escape_string($conn, $_POST['add_role']);
                $username = mysqli_real_escape_string($conn, $_POST['add_username']);
                $password = md5(mysqli_real_escape_string($conn, $_POST['add_password']));
                $email = mysqli_real_escape_string($conn, $_POST['add_email']);

                $insertQuery = "INSERT INTO user_table (full_name, role, username, password, email) 
                                VALUES ('$name', '$role', '$username', '$password', '$email')";
                if (mysqli_query($conn, $insertQuery)) {
                    echo "<script>alert('User added successfully!');</script>";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            }

            // Handle Edit User
            if (isset($_POST['apply_edit'])) {
                $id = $_POST['edit_user_id'];
                $name = mysqli_real_escape_string($conn, $_POST['edit_name']);
                $role = mysqli_real_escape_string($conn, $_POST['edit_role']);
                $username = mysqli_real_escape_string($conn, $_POST['edit_username']);
                $email = mysqli_real_escape_string($conn, $_POST['edit_email']);
                $password = md5(mysqli_real_escape_string($conn, $_POST['edit_password']));

                $updateQuery = "UPDATE user_table 
                                SET full_name='$name', role='$role', username='$username', email='$email', password='$password'
                                WHERE user_id=$id";
                if (mysqli_query($conn, $updateQuery)) {
                    echo "<script>alert('User updated successfully!'); window.location.href=window.location.href;</script>";
                } else {
                    echo "Error updating record: " . mysqli_error($conn);
                }
            }

            // Search
            if (isset($_POST['search'])) {
                $usersearch = $_POST['searchinput'];
                $selectsql = "SELECT * FROM user_table WHERE full_name LIKE '%$usersearch%' 
                              OR username LIKE '%$usersearch%' OR role LIKE '%$usersearch%' OR email LIKE '%$usersearch%'";
            } else {
                $selectsql = "SELECT * FROM user_table";
            }

            $result = mysqli_query($conn, $selectsql);
            ?>

            <h1>Welcome</h1>
            <p>This is the table for users.</p>

            <!-- Search Section -->
            <div class="search-section">
                <form action="" method="post" class="search-box">
                    <div style="position: relative; flex: 1;">
                        <i class="bi bi-search"></i>
                        <input type="search" name="searchinput" placeholder="Search users by name, username, role, or email...">
                    </div>
                    <button type="submit" name="search" class="search-btn">
                        <i class="bi bi-search"></i>
                        Search
                    </button>
                </form>
                <button type="button" class="add-user-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill"></i>
                    Add New User
                </button>
            </div>

            <div class="row">
                <!-- Users Table -->
                <div class="col-md">
                    <div class="container p-3 bg-light border rounded d-flex justify-content-center position-relative">
                        <table class="table table-hover" id="userTable">
                            <thead>
                                <tr>
                                    <th class="text-secondary">USER ID</th>
                                    <th class="text-secondary">FULL NAME</th>
                                    <th class="text-secondary">ROLE</th>
                                    <th class="text-secondary">USERNAME</th>
                                    <th class="text-secondary">EMAIL</th>
                                    <th class="text-secondary">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr class="user-row" data-bs-toggle="modal" data-bs-target="#userDetailsModal"
                                        data-id="<?= $row['user_id']; ?>"
                                        data-name="<?= $row['full_name']; ?>"
                                        data-role="<?= $row['role']; ?>"
                                        data-username="<?= $row['username']; ?>"
                                        data-email="<?= $row['email']; ?>">
                                        <td><?= $row['user_id']; ?></td>
                                        <td><?= $row['full_name']; ?></td>
                                        <td><span class="badge bg-primary"><?= $row['role']; ?></span></td>
                                        <td><?= $row['username']; ?></td>
                                        <td><?= $row['email']; ?></td>
                                        <td class="action-buttons">
                                            <button class="btn btn-warning btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal"
                                                data-id="<?= $row['user_id']; ?>"
                                                data-name="<?= $row['full_name']; ?>"
                                                data-role="<?= $row['role']; ?>"
                                                data-username="<?= $row['username']; ?>"
                                                data-email="<?= $row['email']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteUserModal"
                                                data-id="<?= $row['user_id']; ?>"
                                                data-name="<?= $row['full_name']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <input type="hidden" name="edit_user_id" id="editUserId">
                                
                                <div class="mb-3">
                                    <label for="editName" class="form-label">Full Name</label>
                                    <input type="text" name="edit_name" id="editName" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Role</label>
                                    <select name="edit_role" class="form-select" id="editRole" required>
                                        <option disabled>Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="organizer">Organizer</option>
                                        <option value="voter">Voter</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editUsername" class="form-label">Username</label>
                                    <input type="text" name="edit_username" id="editUsername" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" name="edit_email" id="editEmail" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="editPassword" class="form-label">Password</label>
                                    <input type="password" name="edit_password" id="editPassword" class="form-control" required>
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="apply_edit" class="btn btn-success">Update User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="addName" class="form-label">Full Name</label>
                                    <input type="text" name="add_name" id="addName" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addRole" class="form-label">Role</label>
                                    <select name="add_role" class="form-select" id="addRole" required>
                                        <option disabled selected>Choose Role</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Organizer">Organizer</option>
                                        <option value="Voter">Voter</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="addUsername" class="form-label">Username</label>
                                    <input type="text" name="add_username" id="addUsername" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addEmail" class="form-label">Email</label>
                                    <input type="email" name="add_email" id="addEmail" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addPassword" class="form-label">Password</label>
                                    <input type="password" name="add_password" id="addPassword" class="form-control" required>
                                </div>
                                <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
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

<!-- Bootstrap JS + Edit Button Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle edit modal data
        const editUserModal = document.getElementById('editUserModal');
        editUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const role = button.getAttribute('data-role');
            const username = button.getAttribute('data-username');
            const email = button.getAttribute('data-email');

            document.getElementById('editUserId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            
            const roleSelect = document.getElementById('editRole');
            const roleValue = role.toLowerCase();
            roleSelect.selectedIndex = 0;
            
            for (let i = 0; i < roleSelect.options.length; i++) {
                if (roleSelect.options[i].value === roleValue) {
                    roleSelect.selectedIndex = i;
                    break;
                }
            }
        });

        // Handle delete modal data
        const deleteUserModal = document.getElementById('deleteUserModal');
        deleteUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteUserName').textContent = name;
        });

        // Handle user details modal data
        const userDetailsModal = document.getElementById('userDetailsModal');
        userDetailsModal.addEventListener('show.bs.modal', function (event) {
            const row = event.relatedTarget;
            document.getElementById('detailName').textContent = row.getAttribute('data-name');
            document.getElementById('detailRole').textContent = row.getAttribute('data-role');
            document.getElementById('detailUsername').textContent = row.getAttribute('data-username');
            document.getElementById('detailEmail').textContent = row.getAttribute('data-email');
        });

        // Handle modal closing
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function () {
                // Reset any form fields if needed
                const forms = this.querySelectorAll('form');
                forms.forEach(form => form.reset());
            });
        });
    });
</script>
</body>
</html>
