<?php
// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_rental_system";

// Create connection
$conn_temp = new mysqli($host, $user, $pass);
if ($conn_temp->connect_error) {
    die("Connection failed: " . $conn_temp->connect_error);
}

// Create database if it doesn't exist
$conn_temp->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn_temp->close();

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Delete problematic tables that are not in sqlCode.sql
// sqlCode.sql only creates: users, cars, rental_requests
@$conn->query("DROP TABLE IF EXISTS offers");
@$conn->query("DROP TABLE IF EXISTS role_change_requests");
@$conn->query("DROP TABLE IF EXISTS rating");
@$conn->query("DROP TABLE IF EXISTS contact_messages");

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to safely escape strings
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Function to check if user is admin (all logged-in users are treated as admin)
function isAdmin() {
    return isset($_SESSION['user']);
}

// Function to check if user is premium
function isPremium() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'premium';
}
?>
