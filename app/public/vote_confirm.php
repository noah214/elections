<?php

session_start();
require_once '../php/db_conn.php';
require_once '../php/add_logs.php';



// Check if user has selected candidates
if (!isset($_SESSION['selected_candidates']) || empty($_SESSION['selected_candidates'])) {
    header("Location: vote.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Get all positions and their candidates
$positions_query = "SELECT * FROM position_table ORDER BY position_id";
$positions = $conn->query($positions_query);
$positions_array = [];
while ($position = $positions->fetch_assoc()) {
    $positions_array[$position['position_id']] = $position;
}

// Get candidate details for selected candidates
$selected_candidates = [];
foreach ($_SESSION['selected_candidates'] as $position_id => $candidate_id) {
    $candidate_query = "SELECT * FROM candidate_table WHERE candidate_id = '$candidate_id'";
    $candidate_result = $conn->query($candidate_query);
    if ($candidate = $candidate_result->fetch_assoc()) {
        $selected_candidates[$position_id] = $candidate;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Votes - BOTOmasino Elections</title>
    
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

        .confirm-section { 
            padding: 4rem 0;
            position: relative;
            z-index: 1;
        }

        .confirm-section::before {
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

        .selected-candidate {
            background: #fff;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .selected-candidate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .candidate-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .candidate-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .candidate-details {
            flex: 1;
        }

        .candidate-name {
            color: #000;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .candidate-position {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .candidate-description {
            color: #666;
            font-size: 0.9rem;
        }

        .confirmation-actions {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .btn-success {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .btn-success:hover {
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
    

    <section class="confirm-section">
        <div class="container">
            <div class="form-card">
                <h2 class="text-center mb-4">Confirm Your Votes</h2>
                <p class="text-muted text-center mb-4">Please review your selections before submitting your votes.</p>

                <form method="POST" action="vote.php">
                    <?php foreach ($_SESSION['selected_candidates'] as $position_id => $candidate_id): ?>
                        <div class="mb-4">
                            <h3 class="position-title"><?= htmlspecialchars($positions_array[$position_id]['position_name']) ?></h3>
                            <div class="selected-candidate">
                                <div class="candidate-info">
                                    <img src="../<?= htmlspecialchars($selected_candidates[$position_id]['img_path']) ?>" 
                                         alt="<?= htmlspecialchars($selected_candidates[$position_id]['candidate_name']) ?>" 
                                         class="candidate-image">
                                    <div class="candidate-details">
                                        <h4 class="candidate-name"><?= htmlspecialchars($selected_candidates[$position_id]['candidate_name']) ?></h4>
                                        <p class="candidate-position"><?= htmlspecialchars($selected_candidates[$position_id]['party_affiliation']) ?></p>
                                        <p class="candidate-description"><?= htmlspecialchars($selected_candidates[$position_id]['college']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="confirmation-actions">
                        <div class="d-flex justify-content-between">
                            <a href="vote.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Voting
                            </a>
                            <button type="submit" name="submit_votes" class="btn btn-success">
                                Submit Votes<i class="bi bi-check-circle ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
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
</body>
</html> 