<?php
require_once "db_conn.php";

header('Content-Type: application/json');

if (!isset($_GET['name'])) {
    echo json_encode(['success' => false, 'message' => 'Name parameter is required']);
    exit;
}

$name = mysqli_real_escape_string($conn, $_GET['name']);

// Query to get voter information
$query = "SELECT * FROM voter_table WHERE voter_name = '$name'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $voter_data = mysqli_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'date_of_birth' => $voter_data['date_of_birth'],
        'gender' => $voter_data['gender'],
        'contact_information' => $voter_data['contact_information'],
        'student_id' => $voter_data['student_id']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Voter information not found']);
}
?> 