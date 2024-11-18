<?php
// get_user_details.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "Sravya@1210";
$dbname = "wpl";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the user is already logged in and their ID is stored in the session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare and bind
$stmt = $conn->prepare("SELECT first_name, last_name, date_of_birth, gender, email, phone_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

// Execute statement
$stmt->execute();
$result = $stmt->get_result();

// Fetch result
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}

// Close connections
$stmt->close();
$conn->close();
?>
