<?php
session_start();
require_once '../public/db_conn.php';
require_once '../public/functions.php';

// Check if user is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit();
}

// Check if results are available
$results_query = "SELECT * FROM settings_table WHERE setting_name = 'show_results'";
$results_setting = $conn->query($results_query);
$show_results = $results_setting->fetch_assoc()['setting_value'] ?? '0';

if ($show_results != '1') {
    header("Location: vote_success.php");
    exit();
}

// Get election results
$results_query = "SELECT 
    p.position_name,
    c.candidate_name,
    c.party_affiliation,
    c.college,
    c.img_path,
    COUNT(v.vote_id) as vote_count,
    (SELECT COUNT(DISTINCT voter_id) FROM vote_table) as total_voters
FROM position_table p
LEFT JOIN candidate_table c ON p.position_id = c.position_id
LEFT JOIN vote_table v ON c.candidate_id = v.candidate_id
GROUP BY p.position_id, c.candidate_id, c.candidate_name, c.party_affiliation, c.college, c.img_path
ORDER BY p.position_id, vote_count DESC";
$results_result = $conn->query($results_query);

$results = [];
while ($row = $results_result->fetch_assoc()) {
    $position_id = $row['position_name'];
    if (!isset($results[$position_id])) {
        $results[$position_id] = [];
    }
    $results[$position_id][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results - BOTOmasino Elections</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/global.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .results-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .position-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .position-title {
            color: #000;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ffc107;
        }

        .candidate-card {
            background: #fff;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .candidate-card:hover {
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

        .candidate-party {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .vote-count {
            font-size: 1.2rem;
            font-weight: 600;
            color: #ffc107;
        }

        .progress {
            height: 8px;
            margin-top: 0.5rem;
        }

        .progress-bar {
            background-color: #ffc107;
        }

        .back-button {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            margin-bottom: 2rem;
        }

        .back-button:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            color: #000;
        }

        .home-button {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            margin-top: 2rem;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .home-button:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="results-section">
        <a href="vote_success.php" class="btn btn-primary back-button">
            <i class="bi bi-arrow-left"></i> Back to Vote Summary
        </a>

        <h1 class="text-center mb-4">Election Results</h1>

        <?php foreach ($results as $position => $candidates): ?>
            <div class="position-card">
                <h2 class="position-title"><?= htmlspecialchars($position) ?></h2>
                
                <?php 
                $total_votes = array_sum(array_column($candidates, 'vote_count'));
                foreach ($candidates as $candidate): 
                    $percentage = $total_votes > 0 ? ($candidate['vote_count'] / $total_votes) * 100 : 0;
                ?>
                    <div class="candidate-card">
                        <div class="candidate-info">
                            <img src="<?= htmlspecialchars($candidate['img_path']) ?>" alt="Candidate" class="candidate-image">
                            <div class="candidate-details">
                                <div class="candidate-name"><?= htmlspecialchars($candidate['candidate_name']) ?></div>
                                <div class="candidate-party">
                                    <?= htmlspecialchars($candidate['party_affiliation']) ?> - 
                                    <?= htmlspecialchars($candidate['college']) ?>
                                </div>
                                <div class="vote-count">
                                    <?= $candidate['vote_count'] ?> votes
                                    <small class="text-muted">(<?= number_format($percentage, 1) ?>%)</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= $percentage ?>%" 
                                         aria-valuenow="<?= $percentage ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="text-center">
            <a href="home.php" class="btn btn-primary home-button">
                <i class="bi bi-house-door"></i> Back to Home
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 