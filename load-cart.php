<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo "Please login to view your cart.";
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

$sql = "SELECT * FROM cart WHERE user='$user'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Item: " . $row["item"]. " - Price: $" . $row["price"]. "<br>";
    }
} else {
    echo "Your cart is empty.";
}
$conn->close();
?>
