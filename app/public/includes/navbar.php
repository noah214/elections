<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-check2-square me-2"></i>BOTOmasino Elections
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-house-fill me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'candidates.php' ? 'active' : '' ?>" href="candidates.php">
                        <i class="bi bi-person-badge-fill me-1"></i>Candidates
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'vote.php' ? 'active' : '' ?>" href="vote.php">
                        <i class="bi bi-check2-circle-fill me-1"></i>Vote
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'login.php' ? 'active' : '' ?>" href="login.php">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    background-color: #000 !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.navbar-brand {
    color: #ffc107 !important;
    font-weight: bold;
}

.nav-link {
    color: #ffc107 !important;
    transition: all 0.3s ease;
}

.nav-link:hover {
    color: #e0a800 !important;
    transform: translateY(-1px);
}

.nav-link.active {
    color: #fff !important;
    background-color: #ffc107 !important;
    border-radius: 4px;
}

.navbar-toggler {
    border-color: #ffc107;
}

.navbar-toggler:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
}

@media (max-width: 991.98px) {
    .navbar-collapse {
        background-color: #000;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 0.5rem;
    }
    
    .nav-link {
        padding: 0.5rem 1rem;
        border-radius: 4px;
    }
    
    .nav-link:hover {
        background-color: rgba(255, 193, 7, 0.1);
    }
}
</style> 