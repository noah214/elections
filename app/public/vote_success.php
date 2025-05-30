<?php
session_start();
require_once '../public/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user has actually voted
$voter_id = $_SESSION['voter_id'];
$check_vote = "SELECT * FROM vote_table WHERE voter_id = '$voter_id'";
$vote_result = $conn->query($check_vote);

if ($vote_result->num_rows == 0) {
    header("Location: vote.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Success - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../css/global.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .success-section {
            padding: 4rem 0;
            position: relative;
            z-index: 1;
        }

        .success-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .success-icon {
            font-size: 5rem;
            color: #198754;
            margin-bottom: 1.5rem;
        }

        .success-title {
            color: #000;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .success-message {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-custom {
            padding: 0.8rem 2rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .custom-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .custom-navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light custom-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../assets/images/logo.png" alt="BOTOmasino Logo" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Success Section -->
    <section class="success-section">
        <div class="container">
            <div class="success-card">
                <i class="bi bi-check-circle-fill success-icon"></i>
                <h1 class="success-title">Thank You for Voting!</h1>
                <p class="success-message">
                    Your votes have been successfully recorded. Your participation in the BOTOmasino Elections is greatly appreciated.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="home.php" class="btn btn-primary btn-custom">
                        <i class="bi bi-house-door me-2"></i>Return to Home
                    </a>
                    <a href="../admin/logout.php" class="btn btn-outline-secondary btn-custom">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.custom-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Show success message
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Votes Recorded Successfully!",
            text: "Thank you for participating in the BOTOmasino Elections.",
            showConfirmButton: false,
            timer: 2000
        });
    </script>
</body>
</html> 