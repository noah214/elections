<?php
// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="home.php">
            <i class="bi bi-shield-lock-fill me-2"></i>Admin Panel
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">
                        <i class="bi bi-house-fill me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="voters.php">
                        <i class="bi bi-people-fill me-1"></i>Voters
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="positions.php">
                        <i class="bi bi-briefcase-fill me-1"></i>Positions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="candidates.php">
                        <i class="bi bi-person-badge-fill me-1"></i>Candidates
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="votecount.php">
                        <i class="bi bi-bar-chart-fill me-1"></i>Vote Count
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav> 