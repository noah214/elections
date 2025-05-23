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
            align-items: center;
            justify-content: center;
            padding-top: 1rem;
        }
        .sidebar a {
            color: #ffc107;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            text-align: center;
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
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-custom px-5">
        <a class="navbar-brand" href="#">BOTOmasino Elections</a>
    </nav>
</header>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <h4 class="px-3">Menu</h4>
            <a href="admin_dashboard.php" class="sidebar-item active">
                <i class="bi bi-people-fill"></i> Users
            </a>
            <a href="#"><i class="bi bi-journal-text"></i> Logs</a>
            <a href="#"><i class="bi bi-person-check-fill"></i> Voters</a>
            <a href="#"><i class="bi bi-box-seam"></i> Votes</a>
            <a href="#"><i class="bi bi-person-badge-fill"></i> Candidates</a>
            <a href="#"><i class="bi bi-briefcase-fill"></i> Positions</a>
            <a href="#"><i class="bi bi-bar-chart-line-fill"></i> Vote Count</a>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 px-4 py-4">
            <?php
            require_once "dbaseconnection.php";

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
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

            <!-- Search Form -->
            <form action="" method="post">
                <div class="row g-5">
                    <div class="col-auto">
                        <input type="search" name="searchinput" placeholder="Search any keyword" class="form-control">
                    </div>
                    <div class="col-auto">
                        <input type="submit" name="search" value="Apply" class="btn border border-danger text-danger bg-white">
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
                </div>
            </form>

            <div class="row">
                <!-- Users Table -->
                <div class="col-md">
                    <div class="container p-3 bg-light border rounded d-flex justify-content-center">
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
                                    <tr>
                                        <td><?= $row['user_id']; ?></td>
                                        <td><?= $row['full_name']; ?></td>
                                        <td><?= $row['role']; ?></td>
                                        <td><?= $row['username']; ?></td>
                                        <td><?= $row['email']; ?></td>
                                        <td>
                                            <button 
                                                class="btn btn-warning edit-btn"
                                                data-id="<?= $row['user_id']; ?>"
                                                data-name="<?= $row['full_name']; ?>"
                                                data-role="<?= $row['role']; ?>"
                                                data-username="<?= $row['username']; ?>"
                                                data-email="<?= $row['email']; ?>"
                                            >Edit
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

        </main>
    </div>
</div>

<!-- Bootstrap JS + Edit Button Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Populate the modal fields
                document.getElementById('editUserId').value = button.dataset.id;
                document.getElementById('editName').value = button.dataset.name;
                document.getElementById('editUsername').value = button.dataset.username;
                document.getElementById('editEmail').value = button.dataset.email;
                
                // Fix role selection - handle case conversion
                const roleSelect = document.getElementById('editRole');
                const roleValue = button.dataset.role.toLowerCase();
                
                // Clear any previous selection
                roleSelect.selectedIndex = 0;
                
                // Find and select the matching option
                for (let i = 0; i < roleSelect.options.length; i++) {
                    if (roleSelect.options[i].value === roleValue) {
                        roleSelect.selectedIndex = i;
                        break;
                    }
                }
                
                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editModal.show();
            });
        });
    });
</script>
</body>
</html>
