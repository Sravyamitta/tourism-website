<?php
$servername = "localhost";
$username = "root";
$password = "Sravya@1210";
$dbname = "wpl";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$city = $_POST['city'];
$checkInDate = $_POST['checkInDate'];
$checkOutDate = $_POST['checkOutDate'];
$adults = $_POST['adults'];
$children = $_POST['children'];
$infants = $_POST['infants'];

$sql = "SELECT * FROM stays WHERE city='$city' AND checkInDate='$checkInDate' AND checkOutDate='$checkOutDate' AND adults>='$adults' AND children>='$children' AND infants>='$infants'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Hotel: " . $row["hotelName"]. " - Price: $" . $row["price"]. "<br>";
    }
} else {
    echo "No stays found";
}
$conn->close();
?>
