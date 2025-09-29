<?php
// Database credentials
$servername = "localhost";    // WAMP me usually localhost
$username = "root";           // WAMP default
$password = "";               // WAMP default password
$dbname = "resume_builder";   // Aapka database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8");
?>
