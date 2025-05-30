<?php
function logActivity($conn, $username, $action_type, $description) {
    // Get user_id from username
    $getUserIdQuery = "SELECT user_id FROM user_table WHERE username = '$username'";
    $result = mysqli_query($conn, $getUserIdQuery);
    $userData = mysqli_fetch_assoc($result);
    
    if ($userData) {
        $user_id = $userData['user_id'];
        $timestamp = date('Y-m-d H:i:s');
        
        // log_id is auto-increment, so we only specify user_id, action, and DateTime
        $sql = "INSERT INTO logs_table (user_id, action, DateTime) 
                VALUES ('$user_id', '$action_type', '$timestamp')";
        
        if (!mysqli_query($conn, $sql)) {
            error_log("Error in logActivity: " . mysqli_error($conn));
            error_log("SQL Query: " . $sql);
        }
    }
}
?> 