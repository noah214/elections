<?php

//sql db connection
$servername = "localhost";
$username = "root";
$password = "";
$dbase = "db_project";

$conn = new mysqli($servername, $username, $password, $dbase);

// Create settings table if not exists
$settings_table = "CREATE TABLE IF NOT EXISTS settings_table (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_name VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!$conn->query($settings_table)) {
    die("Error creating settings table: " . $conn->error);
}

?>