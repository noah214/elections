<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit();
}

// Get voter information
$voter_id = $_SESSION['voter_id'];
$stmt = $conn->prepare("SELECT * FROM voters WHERE id = ?");
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$result = $stmt->get_result();
$voter = $result->fetch_assoc();

// Check if voter has voted
$stmt = $conn->prepare("SELECT * FROM votes WHERE voter_id = ?");
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$result = $stmt->get_result();
$has_voted = $result->num_rows > 0;

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (password_verify($current_password, $voter['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE voters SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $voter_id);
            
            if ($stmt->execute()) {
                $success = "Password updated successfully";
            } else {
                $error = "Failed to update password";
            }
        } else {
            $error = "New passwords do not match";
        }
    } else {
        $error = "Current password is incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        .account-card {
            transition: all 0.3s ease;
        }
        
        .account-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <main>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card account-card">
                        <div class="card-header bg-dark text-warning">
                            <h4 class="mb-0">My Account</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>
                            
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            
                            <div class="mb-4">
                                <h5>Account Information</h5>
                                <p><strong>Student ID:</strong> <?= htmlspecialchars($voter['student_id']) ?></p>
                                <p><strong>Name:</strong> <?= htmlspecialchars($voter['name']) ?></p>
                                <p><strong>Voting Status:</strong> 
                                    <?php if ($has_voted): ?>
                                        <span class="badge bg-success">Voted</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Not Voted</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <hr>
                            
                            <h5 class="mb-3">Change Password</h5>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-key me-2"></i>Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
