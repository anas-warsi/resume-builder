<?php
require_once 'config.php';

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}
?>
