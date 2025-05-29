<?php
session_start();
require_once '../public/db_conn.php';

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $conn->begin_transaction();

        // First delete all votes
        $delete_votes = "DELETE FROM vote_table";
        $conn->query($delete_votes);

        // Then delete all candidates
        $delete_candidates = "DELETE FROM candidate_table";
        $conn->query($delete_candidates);

        // Finally delete all positions
        $delete_positions = "DELETE FROM position_table";
        $conn->query($delete_positions);

        // Commit transaction
        $conn->commit();
        
        echo "<script>
            alert('All positions and related data have been deleted successfully!');
            window.location.href='positions.php';
        </script>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>
            alert('Error deleting positions. Please try again.');
            window.location.href='positions.php';
        </script>";
    }
}
?> 