<?php
    // Start session and connect to database
    session_start();
    require_once "db_conn.php";

    // get current user stuff
    $username = $_SESSION['username'];
    $fullname = $_SESSION['fullname'];
    $role = $_SESSION['role'];

    // check if db is working lol
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Delete candidate stuff
    if (isset($_POST['delete_candidate'])) {
        $id = $_POST['delete_candidate_id'];
        
        $deleteQuery = "DELETE FROM candidate_table WHERE candidate_id = $id";
        
        if (mysqli_query($conn, $deleteQuery)) {
            echo "<script>alert('Candidate deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    }

    // add new candidate to db
    if (isset($_POST['add_candidate'])) {
        // get form data
        $name = $_POST['add_name'];
        $party = $_POST['add_party'];
        $college = $_POST['add_college'];
        $position_id = $_POST['add_position'];

        $imagepath = "../candidate_imgs/".basename($_FILES["upload_img"]["name"]);
        move_uploaded_file($_FILES['upload_img']['tmp_name'], $imagepath);

        // insert into candidate table
        $insertQuery = "INSERT INTO candidate_table (candidate_name, party_affiliation, college, img_path, position_id) 
                        VALUES ('$name', '$party', '$college', '$imagepath', '$position_id')";
                        
        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('Candidate added successfully!');</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    // edit candidate stuff
    if (isset($_POST['apply_edit'])) {
        // get form data
        $id = $_POST['edit_candidate_id'];
        $name = $_POST['edit_name'];
        $party = $_POST['edit_party'];
        $college = $_POST['edit_college'];
        $position_id = $_POST['edit_position'];

        $imagepath = "candidate_imgs/".basename($_FILES["edit_img"]["name"]);
        move_uploaded_file($_FILES['edit_img']['tmp_name'], $imagepath);

        // update candidate table
        $updateQuery = "UPDATE candidate_table 
                        SET candidate_name='$name', 
                            party_affiliation='$party', 
                            college='$college',
                            img_path='$imagepath',
                            position_id='$position_id'
                        WHERE candidate_id=$id";
                        
        if (mysqli_query($conn, $updateQuery)) {
            echo "<script>alert('Candidate updated successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }

    // search function - look everywhere lol
    if(isset($_POST['search'])){
        $candidatesearch = $_POST['searchinput'];
        
        // search in all fields with position name join
        $selectsql = "SELECT c.*, p.position_name 
                     FROM candidate_table c 
                     LEFT JOIN position_table p ON c.position_id = p.position_id 
                     WHERE c.candidate_name LIKE '%".$candidatesearch."%' 
                     OR c.party_affiliation LIKE '%".$candidatesearch."%'
                     OR c.college LIKE '%".$candidatesearch."%' 
                     OR p.position_name LIKE '%".$candidatesearch."%'";
    }else{
        // show all candidates if no search with position name join
        $selectsql = "SELECT c.*, p.position_name 
                     FROM candidate_table c 
                     LEFT JOIN position_table p ON c.position_id = p.position_id";
    }

    // get results
    $result = mysqli_query($conn, $selectsql);

    // get positions for dropdown
    $positionsQuery = "SELECT position_id, position_name FROM position_table";
    $positionsResult = mysqli_query($conn, $positionsQuery);
    $positions = [];
    while($row = mysqli_fetch_assoc($positionsResult)) {
        $positions[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Management</title>
    
    <!-- css stuff -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* basic stuff */
        body { 
            min-height: 100vh; 
            overflow-x: hidden;
        }
        
        /* sidebar stuff */
        .sidebar {
            height: 100vh;
            background-color: #000;
            color: #ffc107;
            display: flex;
            flex-direction: column;
            padding-top: 1rem;
            position: fixed;
            top: 0;
            left: 0;
            width: inherit;
            max-width: inherit;
            z-index: 1000;
            overflow-y: auto;
        }

        /* Main content adjustment */
        main {
            margin-left: 16.66667%; /* This matches col-md-2 width */
            width: 83.33333%; /* This ensures main content takes remaining width */
            padding: 1rem;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: static;
                height: auto;
                width: 100%;
            }
            main {
                margin-left: 0;
                width: 100%;
            }
        }

        /* table container */
        .table-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            margin-top: 1rem;
        }

        .table-container::-webkit-scrollbar {
            width: 10px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #000;
            border-radius: 5px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #ffc107;
            border-radius: 5px;
            border: 2px solid #000;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #e0a800;
        }

        /* sidebar scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #000;
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #ffc107;
            border-radius: 4px;
            border: 2px solid #000;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #e0a800;
        }

        /* For Firefox */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: #ffc107 #000;
        }

        /* header thing */
        .sidebar-header {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #ffc107;
            margin-bottom: 1rem;
        }

        .sidebar-header h4 {
            color: #ffc107;
            margin: 0;
            font-size: 1.5rem;
        }

        /* links n stuff */
        .sidebar a {
            color: #ffc107;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .sidebar a i {
            margin-right: 8px;
        }

        .sidebar a:hover {
            background-color: #212529;
        }

        /* categories */
        .sidebar-category {
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 1rem 1rem 0.5rem;
            margin-top: 1rem;
            border-bottom: 1px solid #2c3034;
        }

        /* active stuff */
        .sidebar .sidebar-item {
            color: #efb409;
            background: transparent;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar .sidebar-item.active {
            background: #efb409;
            color: #212529;
            font-weight: bold;
        }

        .sidebar .sidebar-item.active i {
            color: #212529;
        }

        .sidebar .sidebar-item i {
            color: #efb409;
            margin-right: 10px;
        }

        /* table stuff */
        .table-responsive {
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }

        /* badges */
        .badge {
            font-size: 0.85rem;
            padding: 0.5em 0.8em;
            white-space: normal;
            text-align: left;
            line-height: 1.2;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1.2rem;
            font-size: 0.9rem;
        }

        .action-buttons .btn {
            margin: 0 2px;
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }

        .candidate-row {
            cursor: pointer;
        }

        /* modal stuff */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
            padding: 1rem 1.5rem;
        }

        .modal-footer {
            padding: 0.85rem 1.5rem;
        }

        /* form stuff */
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }

        .form-floating > label {
            padding: 1rem 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .invalid-feedback {
            font-size: 0.875em;
        }

        /* alerts */
        .alert {
            border-radius: 8px;
            margin-top: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3.5rem + 2px);
            line-height: 1.25;
        }
        
        .form-floating > label {
            padding: 1rem 0.75rem;
        }
        
        .card-title {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }

        /* SQL Theme Styles */
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            color: #fff;
        }
        
        .text-primary {
            color: #0d6efd !important;
        }
        
        .bg-primary {
            background-color: #0d6efd !important;
        }
        
        #sqlCommand:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .border-primary {
            border-color: #0d6efd !important;
        }

        .card.bg-light {
            background-color: #f8f9fa !important;
        }

        /* College icon styles */
        .college-icon {
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 2px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Update table cell styles */
        .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-header">
                <h4>BOTOmasino Elections</h4>
                <div class="text-light mt-2">
                    <small>Welcome, <?= htmlspecialchars($fullname) ?></small>
                </div>
            </div>
            
            <div class="sidebar-category">User Management</div>
            <?php if (strtolower($role) !== 'organizer'): ?>
                <a href="users.php"><i class="bi bi-people-fill"></i> Admin Users</a>
            <?php endif; ?>
            <a href="voter.php"><i class="bi bi-person-check-fill"></i> Voter Accounts</a>
            
            <div class="sidebar-category">Election Management</div>
            <a href="candidates.php" class="sidebar-item active">
                <i class="bi bi-person-badge-fill"></i> Candidates
            </a>
            <a href="positions.php"><i class="bi bi-briefcase-fill"></i> Positions</a>
            <a href="votes.php"><i class="bi bi-box-seam"></i> Votes</a>
            
            <div class="sidebar-category">Reports</div>
            <a href="votecount.php"><i class="bi bi-bar-chart-line-fill"></i> Vote Count</a>
            <?php if (strtolower($role) !== 'organizer'): ?>
                <a href="logs.php"><i class="bi bi-journal-text"></i> Logs</a>
            <?php endif; ?>

            <div class="mt-auto">
                <div class="sidebar-category">Account</div>
                <a href="../admin/logout.php" class="sidebar-item" style="color: #ffc107; background-color: #000;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 px-4 py-4">
            <div class="container p-5 bg-light">
                <h1 class="mb-4">Candidate Management</h1>
                
                <!-- Search Section -->
                <form action="" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchinput" class="form-control" placeholder="Search candidates...">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="search" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
                                <i class="bi bi-person-plus-fill me-1"></i>Add New Candidate
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sqlCommandModal">
                                <i class="bi bi-database-fill me-1"></i>SQL Commands
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (mysqli_num_rows($result) > 0) : ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Candidate ID</th>
                                    <th>Candidate Name</th>
                                    <th>Party Affiliation</th>
                                    <th>College</th>
                                    <th>Candidate Photo</th>
                                    <th>Position</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $fieldname) : ?>
                                    <tr class="candidate-row" data-id="<?= $fieldname['candidate_id']; ?>" 
                                        data-name="<?= htmlspecialchars($fieldname['candidate_name']); ?>" 
                                        data-party="<?= htmlspecialchars($fieldname['party_affiliation']); ?>" 
                                        data-college="<?= htmlspecialchars($fieldname['college']); ?>" 
                                        data-img="<?= htmlspecialchars($fieldname['img_path']); ?>" 
                                        data-position="<?= htmlspecialchars($fieldname['position_name'] ?? 'No Position'); ?>"
                                        onclick="showCandidateDetails(
                                            '<?= $fieldname['candidate_id']; ?>', 
                                            '<?= htmlspecialchars($fieldname['candidate_name']); ?>', 
                                            '<?= htmlspecialchars($fieldname['party_affiliation']); ?>', 
                                            '<?= htmlspecialchars($fieldname['college']); ?>', 
                                            '<?= htmlspecialchars($fieldname['img_path']); ?>', 
                                            '<?= htmlspecialchars($fieldname['position_name'] ?? 'No Position'); ?>'
                                        )">
                                        <td><?= $fieldname['candidate_id']; ?></td>
                                        <td><?= $fieldname['candidate_name']; ?></td>
                                        <td><?= $fieldname['party_affiliation']; ?></td>
                                        <td>
                                            <?php
                                            $collegeClass = '';
                                            $collegeImage = '';
                                            switch(strtolower($fieldname['college'])) {
                                                case 'college of accountancy':
                                                    $collegeClass = 'bg-danger';
                                                    $collegeImage = '../colleges/accountancy.png';
                                                    break;
                                                case 'college of architecture':
                                                    $collegeClass = 'bg-success';
                                                    $collegeImage = '../colleges/architecture.png';
                                                    break;
                                                case 'faculty of arts and letters':
                                                    $collegeClass = 'bg-info';
                                                    $collegeImage = '../colleges/artlets.png';
                                                    break;
                                                case 'faculty of civil law':
                                                    $collegeClass = 'bg-warning';
                                                    $collegeImage = '../colleges/civillaw.png';
                                                    break;
                                                case 'college of commerce and business administration':
                                                    $collegeClass = 'bg-primary';
                                                    $collegeImage = '../colleges/commerce.png';
                                                    break;
                                                case 'college of education':
                                                    $collegeClass = 'bg-danger';
                                                    $collegeImage = '../colleges/education.png';
                                                    break;
                                                case 'faculty of engineering':
                                                    $collegeClass = 'bg-success';
                                                    $collegeImage = '../colleges/engineering.png';
                                                    break;
                                                case 'college of fine arts and design':
                                                    $collegeClass = 'bg-info';
                                                    $collegeImage = '../colleges/finearts.png';
                                                    break;
                                                case 'college of information and computing sciences':
                                                    $collegeClass = 'bg-warning';
                                                    $collegeImage = '../colleges/cics.png';
                                                    break;
                                                case 'faculty of medicine and surgery':
                                                    $collegeClass = 'bg-primary';
                                                    $collegeImage = '../colleges/medicine.png';
                                                    break;
                                                case 'conservatory of music':
                                                    $collegeClass = 'bg-danger';
                                                    $collegeImage = '../colleges/music.png';
                                                    break;
                                                case 'college of nursing':
                                                    $collegeClass = 'bg-success';
                                                    $collegeImage = '../colleges/nursing.png';
                                                    break;
                                                case 'faculty of pharmacy':
                                                    $collegeClass = 'bg-info';
                                                    $collegeImage = '../colleges/pharmacy.png';
                                                    break;
                                                case 'institute of physical education and athletics':
                                                    $collegeClass = 'bg-warning';
                                                    $collegeImage = '../colleges/ipea.png';
                                                    break;
                                                case 'college of rehabilitation sciences':
                                                    $collegeClass = 'bg-primary';
                                                    $collegeImage = '../colleges/rehab.png';
                                                    break;
                                                case 'college of science':
                                                    $collegeClass = 'bg-danger';
                                                    $collegeImage = '../colleges/science.png';
                                                    break;
                                                case 'college of tourism and hospitality management':
                                                    $collegeClass = 'bg-success';
                                                    $collegeImage = '../colleges/tourism.png';
                                                    break;
                                                case 'faculty of philosophy':
                                                    $collegeClass = 'bg-info';
                                                    $collegeImage = '../colleges/philosophy.png';
                                                    break;
                                                case 'faculty of sacred theology':
                                                    $collegeClass = 'bg-warning';
                                                    $collegeImage = '../colleges/theology.png';
                                                    break;
                                                default:
                                                    $collegeClass = 'bg-secondary';
                                                    $collegeImage = '';
                                            }
                                            ?>
                                            <span class="badge <?= $collegeClass; ?> d-inline-flex align-items-center">
                                                <?php if ($collegeImage && file_exists($collegeImage)): ?>
                                                    <img src="<?= $collegeImage ?>" alt="<?= $fieldname['college'] ?>" class="college-icon me-2" style="width: 20px; height: 20px; object-fit: contain;">
                                                <?php endif; ?>
                                                <?= $fieldname['college']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($fieldname['img_path']) && file_exists($fieldname['img_path'])): ?>
                                                <span class="badge bg-success">Photo Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No Photo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $fieldname['position_name'] ?? 'No Position'; ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <button class="btn btn-warning btn-sm" 
                                                onclick="editCandidate('<?= $fieldname['candidate_id']; ?>', '<?= $fieldname['candidate_name']; ?>', '<?= $fieldname['party_affiliation']; ?>', '<?= $fieldname['college']; ?>', '<?= $fieldname['img_path']; ?>', '<?= $fieldname['position_id']; ?>'); event.stopPropagation();">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                onclick="deleteCandidate('<?= $fieldname['candidate_id']; ?>', '<?= $fieldname['candidate_name']; ?>'); event.stopPropagation();">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else : ?>
                    <div class="alert alert-info">No candidates found.</div>
                <?php endif; ?>
            </div>

            <!-- Edit Candidate Modal -->
            <div class="modal fade" id="editCandidateModal" tabindex="-1" aria-labelledby="editCandidateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="editCandidateModalLabel">
                                <i class="bi bi-pencil-square me-2"></i>Edit Candidate
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <input type="hidden" name="edit_candidate_id" id="editCandidateId">
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_name" id="editName" placeholder="Candidate Name" required>
                                            <label for="editName">Candidate Name</label>
                                            <div class="invalid-feedback">Please enter the candidate name.</div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="edit_party" id="editParty" placeholder="Party Affiliation" required>
                                            <label for="editParty">Party Affiliation</label>
                                            <div class="invalid-feedback">Please enter the party affiliation.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="edit_college" id="editCollege" required>
                                                <option value="" disabled>Select College</option>
                                                <option value="College of Accountancy">College of Accountancy</option>
                                                <option value="College of Architecture">College of Architecture</option>
                                                <option value="Faculty of Arts and Letters">Faculty of Arts and Letters</option>\
                                                <option value="Faculty of Civil Law">Faculty of Civil Law</option>
                                                <option value="College of Commerce and Business Administration">College of Commerce and Business Administration</option>
                                                <option value="College of Education">College of Education</option>
                                                <option value="Faculty of Engineering">Faculty of Engineering</option>
                                                <option value="College of Fine Arts and Design">College of Fine Arts and Design</option>
                                                <option value="College of Information and Computing Sciences">College of Information and Computing Sciences</option>
                                                <option value="Faculty of Medicine and Surgery">Faculty of Medicine and Surgery</option>
                                                <option value="Conservatory of Music">Conservatory of Music</option>
                                                <option value="College of Nursing">College of Nursing</option>
                                                <option value="Faculty of Pharmacy">Faculty of Pharmacy</option>
                                                <option value="Institute of Physical Education and Athletics">Institute of Physical Education and Athletics</option>
                                                <option value="College of Rehabilitation Sciences">College of Rehabilitation Sciences</option>
                                                <option value="College of Science">College of Science</option>
                                                <option value="College of Tourism and Hospitality management">College of Tourism and Hospitality Management</option>
                                                <option value="Faculty of Philosophy">Faculty of Philosophy</option>
                                                <option value="Faculty of Sacred theology">Faculty of Sacred Theology</option>
                                            </select>
                                            <label for="editCollege">College</label>
                                            <div class="invalid-feedback">Please select a college.</div>
                                        </div>
                                    </div>   
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <div class="text-center">
                                                <img id="editPreviewImage" src="" alt="Preview" class="img-thumbnail mb-2" style="max-width: 200px; max-height: 200px;">
                                            </div>
                                            <input type="file" name="edit_img" class="form-control" accept="image/*" onchange="previewImage(event, 'editPreviewImage')">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="edit_position" id="editPosition" required>
                                                <option value="" disabled>Select Position</option>
                                                <?php foreach($positions as $position): ?>
                                                    <option value="<?= $position['position_id']; ?>">
                                                        <?= $position['position_name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="editPosition">Position</label>
                                            <div class="invalid-feedback">Please select a position.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="apply_edit" class="btn btn-warning">
                                        <i class="bi bi-save me-1"></i>Update Candidate
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Candidate Modal -->
            <div class="modal fade" id="addCandidateModal" tabindex="-1" aria-labelledby="addCandidateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addCandidateModalLabel">
                                <i class="bi bi-person-plus-fill me-2"></i>Add New Candidate
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="card mb-4 border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title fw-bold text-primary mb-3">
                                                    <i class="bi bi-person-badge-fill me-2"></i>Candidate Information
                                                </h6>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="add_name" id="addName" placeholder="Candidate Name" required>
                                                    <label for="addName">Candidate Name</label>
                                                    <div class="invalid-feedback">Please enter the candidate name.</div>
                                                </div>

                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="add_party" id="addParty" placeholder="Party Affiliation" required>
                                                    <label for="addParty">Party Affiliation</label>
                                                    <div class="invalid-feedback">Please enter the party affiliation.</div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <select class="form-select" name="add_college" id="addCollege" required>
                                                            <option value="" disabled>Select College</option>
                                                            <option value="College of Accountancy">College of Accountancy</option>
                                                            <option value="College of Architecture">College of Architecture</option>
                                                            <option value="Faculty of Arts and Letters">Faculty of Arts and Letters</option>\
                                                            <option value="Faculty of Civil Law">Faculty of Civil Law</option>
                                                            <option value="College of Commerce and Business Administration">College of Commerce and Business Administration</option>
                                                            <option value="College of Education">College of Education</option>
                                                            <option value="Faculty of Engineering">Faculty of Engineering</option>
                                                            <option value="College of Fine Arts and Design">College of Fine Arts and Design</option>
                                                            <option value="College of Information and Computing Sciences">College of Information and Computing Sciences</option>
                                                            <option value="Faculty of Medicine and Surgery">Faculty of Medicine and Surgery</option>
                                                            <option value="Conservatory of Music">Conservatory of Music</option>
                                                            <option value="College of Nursing">College of Nursing</option>
                                                            <option value="Faculty of Pharmacy">Faculty of Pharmacy</option>
                                                            <option value="Institute of Physical Education and Athletics">Institute of Physical Education and Athletics</option>
                                                            <option value="College of Rehabilitation Sciences">College of Rehabilitation Sciences</option>
                                                            <option value="College of Science">College of Science</option>
                                                            <option value="College of Tourism and Hospitality management">College of Tourism and Hospitality Management</option>
                                                            <option value="Faculty of Philosophy">Faculty of Philosophy</option>
                                                            <option value="Faculty of Sacred theology">Faculty of Sacred Theology</option>
                                                        </select>
                                                        <label for="editCollege">College</label>
                                                        <div class="invalid-feedback">Please select a college.</div>
                                                    </div>
                                                </div>   
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <div class="text-center">
                                                            <img id="addPreviewImage" src="" alt="Preview" class="img-thumbnail mb-2" style="max-width: 200px; max-height: 200px;">
                                                        </div>
                                                        <input type="file" name="upload_img" class="form-control" accept="image/*" onchange="previewImage(event, 'addPreviewImage')">
                                                    </div>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <select class="form-select" name="add_position" id="addPosition" required>
                                                        <option value="" selected disabled>Select Position</option>
                                                        <?php foreach($positions as $position): ?>
                                                            <option value="<?= $position['position_id']; ?>">
                                                                <?= $position['position_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="addPosition">Position</label>
                                                    <div class="invalid-feedback">Please select a position.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="add_candidate" class="btn btn-success">
                                        <i class="bi bi-person-plus-fill me-1"></i>Add Candidate
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Candidate Details Modal -->
            <div class="modal fade" id="candidateDetailsModal" tabindex="-1" aria-labelledby="candidateDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="candidateDetailsModalLabel">
                                <i class="bi bi-person-badge-fill me-2"></i>Candidate Details
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Candidate ID</label>
                                <p id="detailId" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Candidate Name</label>
                                <p id="detailName" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Party Affiliation</label>
                                <p id="detailParty" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">College</label>
                                <p id="detailCollege" class="form-control-plaintext"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Candidate Photo</label>
                                <div class="text-center">
                                    <img id="detailImage" src="" alt="Candidate Photo" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Position</label>
                                <p id="detailPosition" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteCandidateModal" tabindex="-1" aria-labelledby="deleteCandidateModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteCandidateModalLabel">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete candidate: <span id="deleteCandidateName" class="fw-bold"></span>?</p>
                            <p class="text-danger">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <form action="" method="POST">
                                <input type="hidden" name="delete_candidate_id" id="deleteCandidateId">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="delete_candidate" class="btn btn-danger">Delete Candidate</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SQL Command Modal -->
            <div class="modal fade" id="sqlCommandModal" tabindex="-1" aria-labelledby="sqlCommandModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="sqlCommandModalLabel">
                                <i class="bi bi-database-fill me-2"></i>SQL Command Interface
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="card border-primary bg-light">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="sqlCommand" class="form-label fw-bold text-primary">
                                                <i class="bi bi-code-square me-2"></i>Enter SQL Command:
                                            </label>
                                            <textarea class="form-control" id="sqlCommand" name="sql_command" rows="5" required 
                                                placeholder="Enter your SQL command here..." 
                                                style="font-family: 'Consolas', monospace; background-color: #fff; border: 1px solid #0d6efd;"></textarea>
                                            <div class="form-text text-primary mt-2">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                Warning: Be careful with SQL commands. They can modify or delete data permanently.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top border-primary">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" name="execute_sql" class="btn btn-primary">
                                        <i class="bi bi-play-fill me-1"></i>Execute Command
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Handle SQL command execution
            if(isset($_POST['execute_sql'])) {
                $sql_command = $_POST['sql_command'];
                $result_sql = mysqli_query($conn, $sql_command);
                
                if($result_sql) {
                    echo "<script>alert('SQL command executed successfully!');</script>";
                } else {
                    echo "<script>alert('Error executing SQL command: " . mysqli_error($conn) . "');</script>";
                }
            }
            ?>

        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

function previewImage(event, previewId) {
    const image = document.getElementById(previewId);
    if (event.target.files && event.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            image.src = e.target.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Form validation - check forms before submit
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            this.classList.add('was-validated');
        }
    });
});

// Clear forms when modals are hidden
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', function() {
        const form = this.querySelector('form');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
        }
    });
});

// Show candidate details in modal
function showCandidateDetails(id, name, party, college, imgPath, position) {
    document.getElementById('detailId').textContent = id;
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailParty').textContent = party;
    document.getElementById('detailCollege').textContent = college;
    document.getElementById('detailPosition').textContent = position;
    
    // Handle image display
    const detailImage = document.getElementById('detailImage');
    if (imgPath && imgPath !== 'null' && imgPath !== 'undefined') {
        detailImage.src = imgPath;
        detailImage.style.display = 'block';
    } else {
        detailImage.src = '';
        detailImage.style.display = 'none';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('candidateDetailsModal'));
    modal.show();
}

// Edit candidate - populate edit modal with candidate data
function editCandidate(id, name, party, college, imgPath, positionId) {
    document.getElementById('editCandidateId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editParty').value = party;
    document.getElementById('editCollege').value = college;
    document.getElementById('editPosition').value = positionId;
    
    // Handle image preview
    const editPreviewImage = document.getElementById('editPreviewImage');
    if (imgPath && imgPath !== 'null' && imgPath !== 'undefined') {
        editPreviewImage.src = imgPath;
        editPreviewImage.style.display = 'block';
    } else {
        editPreviewImage.src = '';
        editPreviewImage.style.display = 'none';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('editCandidateModal'));
    modal.show();
}

// Delete candidate - show confirmation modal
function deleteCandidate(id, name) {
    document.getElementById('deleteCandidateId').value = id;
    document.getElementById('deleteCandidateName').textContent = name;
    
    // Show the delete confirmation modal
    const modal = new bootstrap.Modal(document.getElementById('deleteCandidateModal'));
    modal.show();
}

// Enhanced form validation with Bootstrap classes
document.addEventListener('DOMContentLoaded', function() {
    // Add validation styling to all forms with .needs-validation class
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Real-time validation feedback
    const inputs = document.querySelectorAll('input[required], select[required], textarea[required]');
    inputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});

// Clear search input when page loads (optional)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="searchinput"]');
    if (searchInput && !searchInput.value) {
        // Only clear if there's no existing search value
        searchInput.value = '';
    }
});

// Add confirmation for SQL command execution
document.querySelector('form[name="execute_sql"], form:has(button[name="execute_sql"])')?.addEventListener('submit', function(event) {
    const sqlCommand = document.getElementById('sqlCommand').value.trim().toLowerCase();
    
    // Check for potentially dangerous SQL commands
    const dangerousCommands = ['drop', 'delete', 'truncate', 'alter', 'update'];
    const isDangerous = dangerousCommands.some(cmd => sqlCommand.includes(cmd));
    
    if (isDangerous) {
        const confirmed = confirm('This SQL command may modify or delete data. Are you sure you want to execute it?');
        if (!confirmed) {
            event.preventDefault();
        }
    }
});

// Enhance table row interactions
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('.candidate-row');
    
    tableRows.forEach(function(row) {
        // Add hover effect class
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});

// Auto-focus on modal inputs when modals are shown
document.getElementById('addCandidateModal')?.addEventListener('shown.bs.modal', function() {
    document.getElementById('addName').focus();
});

document.getElementById('editCandidateModal')?.addEventListener('shown.bs.modal', function() {
    document.getElementById('editName').focus();
});

document.getElementById('sqlCommandModal')?.addEventListener('shown.bs.modal', function() {
    document.getElementById('sqlCommand').focus();
});

// Prevent form submission on Enter key in search input (optional)
document.querySelector('input[name="searchinput"]')?.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        // Let the form submit naturally - this is just here if you want to add custom behavior
    }
});
</script>
</body>
</html>