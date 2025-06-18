<?php

// Database configuration
$host = "localhost"; 
$username = "root";  // Your database username
$password = "";      // Your database password
$database = "travel_lovindra"; // The name of your database

// Create database connection
$koneksi = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
