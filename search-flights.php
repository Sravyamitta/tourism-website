<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_deals";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tripType = $_POST['tripType'];
$origin = $_POST['origin'];
$destination = $_POST['destination'];
$departureDate = $_POST['departureDate'];
$returnDate = $_POST['returnDate'];
$adults = $_POST['adults'];
$children = $_POST['children'];
$infants = $_POST['infants'];

$sql = "SELECT * FROM flights WHERE origin='$origin' AND destination='$destination' AND departureDate='$departureDate' AND adults>='$adults' AND children>='$children' AND infants>='$infants'";
if ($tripType === 'roundTrip') {
    $sql .= " AND returnDate='$returnDate'";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Flight: " . $row["flightNumber"]. " - Price: $" . $row["price"]. "<br>";
    }
} else {
    echo "No flights found";
}
$conn->close();
?>
