<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo "Please login to view your account information.";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_deals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_SESSION['user'];

$sql = "SELECT * FROM users WHERE phone='$user'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Name: " . $row["name"]. "<br>";
        echo "Email: " . $row["email"]. "<br>";
    }
} else {
    echo "No account information found.";
}
$conn->close();
?>
