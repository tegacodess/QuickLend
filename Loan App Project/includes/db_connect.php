<?php
// db_connect.php

$host = "localhost";        // XAMPP uses localhost
$dbname = "loan_app_db";     // The name of your database
$username = "root";          // Default username in XAMPP
$password = "";              // No password for XAMPP by default

try {
    // Create a new PDO instance for connecting to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception for better error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully to the database.";  // For testing, you can remove this later
} catch (PDOException $e) {
    // Catch and display any connection errors
    echo "Database connection failed: " . $e->getMessage();
}
?>

