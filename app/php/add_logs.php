<?php
require_once 'db_conn.php';

//add logs to the logs_table
function add_logs($conn, $user_id, $action)  {
        $insert_sql = "INSERT INTO logs_table (user_id, action, datetime) values ('$user_id','$action',NOW())";
        $conn->query($insert_sql);
}

?>