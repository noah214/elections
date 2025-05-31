<?php

session_start();
require_once "../php/db_conn.php";
require_once "../php/add_logs.php";

$user_id = $_SESSION['user_id'];
$voter_id = $_SESSION['voter_id'];

// Check if user has already voted
$check_vote_query = "SELECT COUNT(*) as vote_count FROM vote_table WHERE voter_id = '$voter_id'";
$vote_result = $conn->query($check_vote_query);
$vote_count = $vote_result->fetch_assoc()['vote_count'];

if ($vote_count > 0) {
    // If user has already voted, redirect to vote summary page
    header("Location: vote_success.php");
    exit();
}

// Initialize selected candidates in session if not exists
if (!isset($_SESSION['selected_candidates'])) {
    $_SESSION['selected_candidates'] = [];
}

// Get current position index from session or set to 0
$current_position_index = isset($_SESSION['current_position_index']) ? $_SESSION['current_position_index'] : 0;


// Get all positions
$positions_query = "SELECT * FROM position_table ORDER BY position_id";
$positions = $conn->query($positions_query);


$positions_array = [];
while ($position = $positions->fetch_assoc()) {
    $positions_array[] = $position;
}


// Handle candidate selection logic

if (isset($_POST['select_candidate'])) {
    $position_id = $_POST['position_id'];
    $candidate_id = $_POST['candidate_id'];
    
    // Store the selected positon


    $_SESSION['selected_candidates'][$position_id] = $candidate_id;
    
    // Move to next positionn
    if ($current_position_index < count($positions_array) - 1) {
        $_SESSION['current_position_index'] = $current_position_index + 1;
        header("Location: vote.php");
        exit();
    } else {
        // If last position, redirect to confirmation page
        header("Location: vote_confirm.php");
        exit();
    }
}

//goes back here when user is done with vote  confirmation
// Handle final vote submission
if (isset($_POST['submit_votes'])) {
    $success = true;
    $error_message = '';

    // Start transaction
    $conn->begin_transaction();

    try {
        foreach ($_SESSION['selected_candidates'] as $position_id => $candidate_id) {
            $insert_vote = "INSERT INTO vote_table (voter_id, candidate_id, vote_timestamp) 
                           VALUES ('$voter_id', '$candidate_id', NOW())";
            if (!$conn->query($insert_vote)) {
                throw new Exception("Error recording vote: " . $conn->error);
            }
        }
        
        // Log the voting activity
        add_logs($conn, $user_id, 'VOTE' );
        
        $conn->commit();
        
        // Clear voting session data
        unset($_SESSION['selected_candidates']);
        unset($_SESSION['current_position_index']);
        
        header("Location: vote_success.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error recording votes: " . $e->getMessage();
        error_log("Vote Error: " . $e->getMessage());
        echo "<script>alert('$error_message');</script>";
    }
}

// Get current position
$current_position = $positions_array[$current_position_index];

// Get candidates for every position
$candidates_query = "SELECT * FROM candidate_table WHERE position_id = '" . $current_position['position_id'] . "'";
$candidates = $conn->query($candidates_query);
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
            padding-top: 76px; /* Add padding for fixed navbar */
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

        .form-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            transition: all 0.3s ease;
        }

        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .position-title {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffc107;
        }

        .candidate-card {
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 0;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .candidate-card:hover {
            border-color: #ffc107;
            background-color: #fff9e6;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .candidate-card.selected {
            border-color: #ffc107;
            background-color: #fff9e6;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .candidate-image {
            width: 100%;
            aspect-ratio: 1;
            position: relative;
            overflow: hidden;
        }

        .candidate-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .candidate-info {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .candidate-name {
            color: #000;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .candidate-position {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .candidate-description {
            color: #666;
            font-size: 0.85rem;
            line-height: 1.2;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.4em;
        }

        .candidate-container {
            display: flex;
            justify-content: center;
            align-items: stretch;
            height: 100%;
            padding: 0.5rem;
        }

        @media (max-width: 1200px) {
            .candidate-container {
                width: 33.333%;
            }
        }

        @media (max-width: 992px) {
            .candidate-container {
                width: 50%;
            }
        }

        @media (max-width: 576px) {
            .candidate-container {
                width: 100%;
            }
        }

        .progress-container {
            margin-bottom: 2rem;
        }

        .progress {
            height: 10px;
            border-radius: 5px;
            background-color: #e9ecef;
        }

        .progress-bar {
            background-color: #ffc107;
            transition: width 0.3s ease;
        }

        .step-indicator {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .btn-primary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-primary:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            color: #000;
        }

        .btn-outline-secondary {
            color: #666;
            border-color: #666;
        }

        .btn-outline-secondary:hover {
            background-color: #666;
            border-color: #666;
            color: #fff;
        }

        .alert {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    
    <!--navbar-->
    <nav class="navbar navbar-expand-lg custom-navbar" id="mainNavbar">
        <div class="container-fluid px-5">
            <a class="navbar-brand d-flex align-items-center" href="home.php">
                <img src="../images/USTLogo.png" width="40" height="40" class="d-inline-block me-2" alt="SSC Logo">
                <span class="text-yellow">UST</span>&nbsp;Supreme Student Council
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <a class="nav-item nav-link" href="home.php" aria-current="page">Home</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="candidate.php">Candidates</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link active" href="vote.php">Vote</a>
                    <div class="vr mx-2 d-none d-lg-block"></div>
                    <a class="nav-item nav-link" href="Account.php">Account</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="vote-section">
        <div class="container">
            <div class="form-card">
                <div class="progress-container">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" 
                             style="width: <?= (($current_position_index + 1) / count($positions_array)) * 100 ?>%">
                        </div>
                    </div>
                    <div class="step-indicator">
                        Position <?= $current_position_index + 1 ?> of <?= count($positions_array) ?>
                    </div>
                </div>

                <h2 class="position-title text-center"><?= htmlspecialchars($current_position['position_name']) ?></h2>
                <p class="text-muted mb-4"><?= htmlspecialchars($current_position['position_description']) ?></p>

                <?php if ($candidates->num_rows > 0): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="position_id" value="<?= $current_position['position_id'] ?>">
                        <div class="row d-flex justify-content-center align-items-center g-2">
                            <?php while ($candidate = $candidates->fetch_assoc()): ?>
                                <div class="candidate-container col-3">
                                    <label class="candidate-card <?= isset($_SESSION['selected_candidates'][$current_position['position_id']]) && $_SESSION['selected_candidates'][$current_position['position_id']] == $candidate['candidate_id'] ? 'selected' : '' ?>">
                                        <input type="radio" 
                                               name="candidate_id" 
                                               value="<?= $candidate['candidate_id'] ?>" 
                                               required
                                               style="display: none;"
                                               <?= isset($_SESSION['selected_candidates'][$current_position['position_id']]) && $_SESSION['selected_candidates'][$current_position['position_id']] == $candidate['candidate_id'] ? 'checked' : '' ?>>
                                        <div class="candidate-image">
                                            <img src="../<?= htmlspecialchars($candidate['img_path']) ?>" 
                                                 alt="<?= htmlspecialchars($candidate['candidate_name']) ?>">
                                        </div>
                                        <div class="candidate-info">
                                            <h3 class="candidate-name"><?= htmlspecialchars($candidate['candidate_name']) ?></h3>
                                            <p class="candidate-position"><?= htmlspecialchars($candidate['party_affiliation']) ?></p>
                                            <p class="candidate-description"><?= htmlspecialchars($candidate['college']) ?></p>
                                        </div>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <?php if ($current_position_index > 0): ?>
                                <a href="?prev=1" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <button type="submit" name="select_candidate" class="btn btn-primary">
                                <?= $current_position_index < count($positions_array) - 1 ? 'Next' : 'Review Votes' ?>
                                <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No candidates have been registered for this position yet.
                    </div>
                <?php endif; ?>
            </div>
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
        // design for radio selection
    document.querySelectorAll('.candidate-card').forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            const form = this.closest('form'); 
            
            
            form.querySelectorAll('.candidate-card').forEach(c => {
                c.classList.remove('selected');
            });
            
            
            this.classList.add('selected');
            radio.checked = true;
        });
    });
    </script>
</body>
</html>

