<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_deals";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$phone = $_POST['phone'];
$password = $_POST['password'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$dob = $_POST['dob'];
$email = $_POST['email'];
$gender = $_POST['gender'];

// Check if phone number already exists
$sql = "SELECT * FROM users WHERE phone='$phone'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "Phone number already in use.";
    exit();
}

// Insert user data into database
$sql = "INSERT INTO users (phone, password, firstName, lastName, dob, email, gender)
VALUES ('$phone', '$password', '$firstName', '$lastName', '$dob', '$email', '$gender')";

if ($conn->query($sql) === TRUE) {
    echo "Registration successful!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
