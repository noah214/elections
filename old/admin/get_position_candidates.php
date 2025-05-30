<?php
session_start();
require_once "db_conn.php";

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get position ID from request
$position_id = isset($_GET['position_id']) ? intval($_GET['position_id']) : 0;

if ($position_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid position ID']);
    exit();
}

// Get candidates for this position
$query = "SELECT candidate_id, candidate_name, party_affiliation, college 
          FROM candidate_table 
          WHERE position_id = ? 
          ORDER BY candidate_name";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $position_id);
$stmt->execute();
$result = $stmt->get_result();

$candidates = [];
while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($candidates); 