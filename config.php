<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'greenkartv2'; // Change to your actual database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Test query to check if users table exists
$testQuery = $conn->query("SHOW TABLES LIKE 'users'");
if ($testQuery && $testQuery->num_rows === 0) {
    die("Table 'users' does not exist in database '$database'.");
}

// Optional: Test insert (uncomment to test)
// $testInsert = $conn->query("INSERT INTO users (name, email, role, password) VALUES ('Test User', 'test@example.com', 'buyer', 'testpass')");
// if ($testInsert) { echo 'Test insert successful.'; } else { echo 'Test insert failed: ' . $conn->error; }
?>
