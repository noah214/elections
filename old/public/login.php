<?php
    session_start();
    
require_once "../public/db_conn.php";

// Check if user is already logged in
if (isset($_SESSION['voter_id'])) {
    header("Location: home.php");
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voter_id = $_POST['voter_id'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM voter_table WHERE voter_id = '$voter_id'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $voter = $result->fetch_assoc();
        if (password_verify($password, $voter['password'])) {
            $_SESSION['voter_id'] = $voter['voter_id'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Voter ID not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    
    <style>
        /* Navbar styles */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            color: #000;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .nav-link {
            color: #666;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #ffc107;
        }
        
        .nav-link.active {
            color: #ffc107;
        }

        /* Login section styles */
        .login-section {
            padding: 4rem 0;
            margin-bottom: 4rem;
        }

        .login-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .login-title {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-label {
            color: #666;
            font-weight: 500;
        }

        .form-control {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 0.8rem 1rem;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .btn-login {
            background: #ffc107;
            color: #000;
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 10px;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background: #e0a800;
            color: #000;
        }

        /* Footer styles */
        .footer {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-top: auto;
        }
        
        .footer-content {
            text-align: center;
            color: #666;
        }
        
        .footer-content p {
            margin-bottom: 0.5rem;
        }
        
        .footer-content a {
            color: #ffc107;
            text-decoration: none;
        }
        
        .footer-content a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="home.php">BOTOmasino</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="candidates.php">Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="login-section">
        <div class="container">
            <div class="login-card">
                <h1 class="login-title">Login</h1>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="voter_id" class="form-label">Voter ID</label>
                        <input type="text" class="form-control" id="voter_id" name="voter_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-login">Login</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?= date('Y') ?> BOTOmasino Elections. All rights reserved.</p>
                <p>Designed and developed with <i class="bi bi-heart-fill text-danger"></i> for the community</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
</body>
</html>

