<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../public/db_conn.php';
require_once '../public/functions.php';

// Check if user is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Debug: Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if voter has already voted
$check_vote = "SELECT * FROM vote_table WHERE voter_id = '$voter_id'";
$vote_result = $conn->query($check_vote);
$has_voted = $vote_result->num_rows > 0;

// Get all positions
$positions_query = "SELECT * FROM position_table ORDER BY position_id";
$positions = $conn->query($positions_query);

// Debug: Check if positions query worked
if (!$positions) {
    die("Error in positions query: " . $conn->error);
}

// Debug: Check number of positions
$positions_count = $positions->num_rows;
echo "<!-- Debug: Number of positions found: " . $positions_count . " -->";

$positions_array = [];
while ($position = $positions->fetch_assoc()) {
    $positions_array[] = $position;
}

// Debug: Print positions array
echo "<!-- Debug: Positions array: " . print_r($positions_array, true) . " -->";

// Handle vote submission
if (isset($_POST['submit_votes']) && !$has_voted) {
    $votes = $_POST['votes'];
    $success = true;

    // Start transaction
    $conn->begin_transaction();

    try {
        foreach ($votes as $position_id => $candidate_id) {
            $insert_vote = "INSERT INTO vote_table (voter_id, candidate_id, vote_timestamp) 
                           VALUES ('$voter_id', '$candidate_id', NOW())";
            if (!$conn->query($insert_vote)) {
                throw new Exception("Error recording vote: " . $conn->error);
            }
        }
        
        // Log the voting activity
        $description = "Voter ID: $voter_id submitted votes for " . count($votes) . " positions";
        logActivity($conn, $voter_id, 'VOTE', $description);
        
        $conn->commit();
        header("Location: vote_success.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error recording votes. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote - BOTOmasino Elections</title>
    
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

        .vote-section { 
            padding: 4rem 0;
            position: relative;
            z-index: 1;
        }

        .vote-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            z-index: -1;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .position-title {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffc107;
        }
        
        .candidate-option {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .candidate-option:hover {
            border-color: #ffc107;
            background-color: #fff9e6;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .candidate-option.selected {
            border-color: #198754;
            background-color: #f8fff9;
            box-shadow: 0 6px 12px rgba(25, 135, 84, 0.1);
        }
        
        .candidate-option input[type="radio"] {
            margin-right: 1rem;
        }
        
        .candidate-info {
            display: flex;
            align-items: center;
        }
        
        .candidate-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .candidate-details {
            flex: 1;
        }
        
        .candidate-name {
            color: #000;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
        }
        
        .candidate-position {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.2rem;
        }
        
        .candidate-description {
            color: #666;
            font-size: 0.9rem;
        }

        .step {
            display: none;
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .step.active {
            display: block;
        }

        .progress {
            height: 10px;
            margin-bottom: 2rem;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar {
            background-color: #ffc107;
            transition: width 0.3s ease;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .nav-buttons .btn {
            padding: 0.8rem 2rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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

        .footer {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar" id="mainNavbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">
                <img src="../assets/images/logo.png" width="30" height="30" class="d-inline-block align-top me-2" alt="SSC Logo">
                UST Supreme Student Council
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-item nav-link" href="home.php">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link active" href="vote.php" aria-current="page">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="vote-section">
        <div class="container">
            <?php if ($has_voted): ?>
                <div class="alert alert-warning text-center">
                    <h4 class="alert-heading">You have already voted!</h4>
                    <p>You cannot vote again. Your previous votes have been recorded.</p>
                    <hr>
                    <p class="mb-0">
                        <a href="home.php" class="btn btn-primary">Return to Home</a>
                    </p>
                </div>
            <?php else: ?>
                <form id="voteForm" method="POST" action="">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>

                    <?php foreach ($positions_array as $index => $position): 
                        // Get candidates for this position
                        $candidates_query = "SELECT * FROM candidate_table WHERE position_id = '" . $position['position_id'] . "'";
                        $candidates = $conn->query($candidates_query);
                        $has_candidates = $candidates->num_rows > 0;
                    ?>
                        <div class="step <?= $index === 0 ? 'active' : '' ?>" id="step-<?= $index + 1 ?>">
                            <h2 class="position-title"><?= htmlspecialchars($position['position_name']) ?></h2>
                            <p class="text-muted mb-4"><?= htmlspecialchars($position['position_description']) ?></p>
                            
                            <?php if ($has_candidates): ?>
                                <div class="candidates-list">
                                    <?php while ($candidate = $candidates->fetch_assoc()): ?>
                                        <label class="candidate-option">
                                            <input type="radio" 
                                                   name="votes[<?= $position['position_id'] ?>]" 
                                                   value="<?= $candidate['candidate_id'] ?>" 
                                                   required>
                                            <div class="candidate-info">
                                                <img src="<?= htmlspecialchars($candidate['img_path']) ?>" 
                                                     alt="<?= htmlspecialchars($candidate['candidate_name']) ?>" 
                                                     class="candidate-image">
                                                <div class="candidate-details">
                                                    <h3 class="candidate-name"><?= htmlspecialchars($candidate['candidate_name']) ?></h3>
                                                    <p class="candidate-position"><?= htmlspecialchars($candidate['party_affiliation']) ?></p>
                                                    <p class="candidate-description"><?= htmlspecialchars($candidate['college']) ?></p>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No candidates have been registered for this position yet.
                                </div>
                            <?php endif; ?>

                            <div class="nav-buttons">
                                <?php if ($index > 0): ?>
                                    <button type="button" class="btn btn-secondary" onclick="prevStep()">Previous</button>
                                <?php else: ?>
                                    <div></div>
                                <?php endif; ?>

                                <?php if ($index < count($positions_array) - 1): ?>
                                    <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                                <?php else: ?>
                                    <button type="submit" name="submit_votes" class="btn btn-success">
                                        Submit Votes<i class="bi bi-check-circle ms-2"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© 2024 UST Supreme Student Council. All rights reserved.</span>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = <?= count($positions_array) ?>;
        const selectedVotes = {};

        function updateProgress() {
            const progress = (Object.keys(selectedVotes).length / totalSteps) * 100;
            document.querySelector('.progress-bar').style.width = `${progress}%`;
        }

        function showStep(step) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById(`step-${step}`).classList.add('active');
            currentStep = step;
        }

        function nextStep() {
            const currentStepElement = document.getElementById(`step-${currentStep}`);
            const hasCandidates = currentStepElement.querySelector('.candidates-list') !== null;
            
            if (hasCandidates) {
                const currentPosition = currentStepElement.querySelector('input[type="radio"]:checked');
                if (!currentPosition) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select a candidate before proceeding.',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }
                selectedVotes[currentPosition.name] = currentPosition.value;
            }
            
            updateProgress();
            showStep(currentStep + 1);
        }

        function prevStep() {
            showStep(currentStep - 1);
        }

        // Add visual feedback for radio selection
        document.querySelectorAll('.candidate-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options in this step
                const step = this.closest('.step');
                step.querySelectorAll('.candidate-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                // Add selected class to this option
                this.classList.add('selected');
            });
        });

        // Form validation
        document.getElementById('voteForm').addEventListener('submit', function(e) {
            const selectedCount = Object.keys(selectedVotes).length;
            const totalPositionsWithCandidates = document.querySelectorAll('.candidates-list').length;
            
            if (selectedCount !== totalPositionsWithCandidates) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Votes',
                    text: 'Please select a candidate for each position that has candidates.',
                    confirmButtonColor: '#ffc107'
                });
            }
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            const scrolled = window.pageYOffset;
            
            if (scrolled > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>

