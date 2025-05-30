<?php
session_start();
require_once '../public/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['voter_id'])) {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Login Required',
            text: 'Please login to access the voting page.',
            confirmButtonColor: '#ffc107'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php';
            }
        });
    </script>";
    exit();
}

// Get voter information
$voter_id = $_SESSION['voter_id'];
$voter_query = "SELECT * FROM voter_table WHERE voter_id = '$voter_id'";
$voter_result = $conn->query($voter_query);
$voter = $voter_result->fetch_assoc();

// Check if voter has already voted
$check_vote = "SELECT * FROM vote_table WHERE voter_id = '$voter_id'";
$vote_result = $conn->query($check_vote);
$has_voted = $vote_result->num_rows > 0;

// Get all positions
$positions_query = "SELECT * FROM position_table ORDER BY position_id";
$positions = $conn->query($positions_query);
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
    <link rel="stylesheet" href="../css/global.css">
    
    <style>
        .vote-section { padding: 4rem 0; }
        
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
        }
        
        .candidate-option:hover {
            border-color: #ffc107;
            background-color: #fff9e6;
        }
        
        .candidate-option.selected {
            border-color: #198754;
            background-color: #f8fff9;
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
        
        .submit-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 1rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .submit-section .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .vote-summary {
            font-size: 1.1rem;
            color: #666;
        }
        
        .btn-submit {
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
                <?php foreach ($positions_array as $position): 
                    // Get candidates for this position
                    $candidates_query = "SELECT * FROM candidate_table WHERE position_id = '" . $position['position_id'] . "'";
                    $candidates = $conn->query($candidates_query);
                ?>
                    <div class="position-section mb-5">
                        <h2 class="position-title"><?= htmlspecialchars($position['position_name']) ?></h2>
                        <p class="text-muted mb-4"><?= htmlspecialchars($position['position_description']) ?></p>
                        
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
                    </div>
                <?php endforeach; ?>
                
                <div class="submit-section">
                    <div class="container">
                        <div class="vote-summary">
                            Selected: <span id="selectedCount">0</span> of <span id="totalPositions"><?= count($positions_array) ?></span> positions
                        </div>
                        <button type="submit" name="submit_votes" class="btn btn-success btn-submit">
                            Submit Votes<i class="bi bi-check-circle ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        // Add visual feedback for radio selection
        document.querySelectorAll('.candidate-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options in this position
                const positionSection = this.closest('.position-section');
                positionSection.querySelectorAll('.candidate-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                // Add selected class to this option
                this.classList.add('selected');
                updateSelectedCount();
            });
        });

        // Update selected count
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('input[type="radio"]:checked').length;
            const totalPositions = document.querySelectorAll('.position-section').length;
            document.getElementById('selectedCount').textContent = selectedCount;
            document.getElementById('totalPositions').textContent = totalPositions;
        }

        // Handle form submission
        document.getElementById('voteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if all positions with candidates have been voted for
            const allPositionsWithCandidatesVoted = Array.from(document.querySelectorAll('.position-section')).every(section => {
                const hasCandidates = section.querySelector('.candidate-option') !== null;
                if (!hasCandidates) return true; // Skip positions without candidates
                return section.querySelector('input[type="radio"]:checked') !== null;
            });
            
            if (!allPositionsWithCandidatesVoted) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Selection',
                    text: 'Please select a candidate for all positions that have candidates before submitting.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            // Confirm submission
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
                    this.submit();
                }
            });
        });
    </script>
</body>
</html>

