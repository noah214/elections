<?php
session_start();
require_once '../public/db_conn.php';

// Get voter ID
$voter_id = $_SESSION['voter_id'];

// Check if voter has already voted
$check_vote = "SELECT * FROM vote_table WHERE voter_id = ?";
$stmt = $conn->prepare($check_vote);
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Already Voted',
            text: 'You have already submitted your votes!',
            confirmButtonColor: '#ffc107'
        }).then((result) => {
            window.location.href='home.php';
        });
    </script>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_votes'])) {
    $success = true;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        foreach ($_POST['votes'] as $position_id => $candidate_id) {
            // Insert vote
            $insert_vote = "INSERT INTO vote_table (voter_id, candidate_id, vote_timestamp) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($insert_vote);
            $stmt->bind_param("ii", $voter_id, $candidate_id);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Votes Submitted!',
                text: 'Your votes have been recorded successfully.',
                confirmButtonColor: '#198754'
            }).then((result) => {
                window.location.href='home.php';
            });
        </script>";
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error submitting vote: " . $e->getMessage() . "',
                confirmButtonColor: '#dc3545'
            });
        </script>";
    }
}

// Get all positions
$positions_query = "SELECT * FROM position_table ORDER BY position_id";
$positions = $conn->query($positions_query);
$total_positions = $positions->num_rows;

// Store positions in an array to prevent repetition
$positions_array = [];
while ($position = $positions->fetch_assoc()) {
    $positions_array[] = $position;
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
    
    <style>
        .vote-section {
            padding: 4rem 0;
        }

        .position-title {
            color: #000;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .position-description {
            color: #666;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            font-style: italic;
        }

        .candidate-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            height: 100%;
            border: 2px solid #ffc107;
            max-width: 320px;
            margin: 0 auto;
            cursor: pointer;
        }

        .candidate-card:hover {
            transform: translateY(-5px);
        }

        .candidate-card.selected {
            border-color: #198754;
            background-color: #f8fff9;
        }

        .candidate-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border: none;
            border-radius: 0;
        }

        .candidate-info {
            padding: 1.2rem;
        }

        .candidate-name {
            color: #000;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .candidate-position {
            color: #ffc107;
            font-weight: 500;
            margin-bottom: 0.4rem;
            font-size: 1rem;
        }

        .candidate-description {
            color: #666;
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
        }

        .progress {
            height: 10px;
            margin-bottom: 2rem;
        }

        .progress-bar {
            background-color: #ffc107;
        }

        .vote-step {
            display: none;
        }

        .vote-step.active {
            display: block;
        }

        .navigation-buttons {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
        }

        .btn-vote {
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <section class="vote-section">
        <div class="container">
            <form id="voteForm" method="POST" action="">
                <?php 
                $position_number = 1;
                foreach ($positions_array as $position): 
                    // Get candidates for this position
                    $candidates_query = "SELECT * FROM candidate_table WHERE position_id = ?";
                    $stmt = $conn->prepare($candidates_query);
                    $stmt->bind_param("i", $position['position_id']);
                    $stmt->execute();
                    $candidates = $stmt->get_result();
                ?>
                    <div class="vote-step <?= $position_number === 1 ? 'active' : '' ?>" data-position-id="<?= $position['position_id'] ?>">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?= ($position_number / $total_positions) * 100 ?>%"></div>
                        </div>
                        
                        <h2 class="position-title"><?= htmlspecialchars($position['position_name']) ?></h2>
                        <p class="position-description"><?= htmlspecialchars($position['position_description']) ?></p>
                        
                        <div class="row">
                            <?php while ($candidate = $candidates->fetch_assoc()): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="candidate-card" onclick="selectCandidate(this, <?= $position['position_id'] ?>, <?= $candidate['candidate_id'] ?>)">
                                        <img src="<?= htmlspecialchars($candidate['img_path']) ?>" 
                                             alt="<?= htmlspecialchars($candidate['candidate_name']) ?>" 
                                             class="candidate-image">
                                        <div class="candidate-info">
                                            <h3 class="candidate-name"><?= htmlspecialchars($candidate['candidate_name']) ?></h3>
                                            <p class="candidate-description"><?= htmlspecialchars($candidate['college']) ?></p>
                                            <p class="candidate-position"><?= htmlspecialchars($candidate['party_affiliation']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="navigation-buttons">
                            <?php if ($position_number > 1): ?>
                                <button type="button" class="btn btn-secondary btn-vote" onclick="previousStep()">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </button>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <?php if ($position_number < $total_positions): ?>
                                <button type="button" class="btn btn-warning btn-vote" onclick="nextStep()">
                                    Next<i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            <?php else: ?>
                                <button type="submit" name="submit_votes" class="btn btn-success btn-vote">
                                    Submit Votes<i class="bi bi-check-circle ms-2"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    $position_number++;
                endforeach; 
                ?>
            </form>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        let selectedCandidates = {};

        function selectCandidate(card, positionId, candidateId) {
            // Remove selected class from all cards in this position
            const step = card.closest('.vote-step');
            step.querySelectorAll('.candidate-card').forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            card.classList.add('selected');
            
            // Store the selection
            selectedCandidates[positionId] = candidateId;
        }

        function nextStep() {
            const currentStep = document.querySelector('.vote-step.active');
            const currentPositionId = currentStep.dataset.positionId;
            
            // Check if there are any candidate cards in this step
            const hasCandidates = currentStep.querySelector('.candidate-card') !== null;
            
            // Only check for selection if there are candidates
            if (hasCandidates && !selectedCandidates[currentPositionId]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select a candidate before proceeding.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }
            
            // Hide current step
            currentStep.classList.remove('active');
            
            // Show next step
            const nextStep = currentStep.nextElementSibling;
            if (nextStep) {
                nextStep.classList.add('active');
            }
        }

        function previousStep() {
            const currentStep = document.querySelector('.vote-step.active');
            
            // Hide current step
            currentStep.classList.remove('active');
            
            // Show previous step
            const previousStep = currentStep.previousElementSibling;
            if (previousStep) {
                previousStep.classList.add('active');
            }
        }

        // Handle form submission
        document.getElementById('voteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get all positions that have candidates
            const positionsWithCandidates = Array.from(document.querySelectorAll('.vote-step')).filter(step => 
                step.querySelector('.candidate-card') !== null
            ).map(step => step.dataset.positionId);
            
            // Check if all positions with candidates have a selection
            const allPositionsWithCandidatesSelected = positionsWithCandidates.every(positionId => 
                selectedCandidates[positionId]
            );
            
            if (!allPositionsWithCandidatesSelected) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Selection',
                    text: 'Please select a candidate for all positions that have candidates before submitting.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Your Votes',
                text: 'Are you sure you want to submit your votes? This action cannot be undone.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, submit my votes',
                cancelButtonText: 'No, review again'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add hidden inputs for each vote
                    for (const [positionId, candidateId] of Object.entries(selectedCandidates)) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `votes[${positionId}]`;
                        input.value = candidateId;
                        this.appendChild(input);
                    }
                    
                    // Submit the form
                    this.submit();
                }
            });
        });
    </script>
</body>
</html>

