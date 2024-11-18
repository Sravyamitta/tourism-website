<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpl";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$flightBookingId = $_POST['flightBookingIdPassengers'] ?? '';

$sql = "SELECT p.SSN, p.FirstName, p.LastName, p.DateOfBirth, p.Category 
        FROM Tickets t 
        JOIN Passenger p ON t.SSN = p.SSN 
        JOIN FlightBooking fb ON t.FlightBookingID = fb.FlightBookingID
        WHERE fb.FlightBookingID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $flightBookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h4>Passengers for Flight Booking ID: $flightBookingId</h4>";
    while ($row = $result->fetch_assoc()) {
        echo "SSN: " . $row['SSN'] . "<br>";
        echo "First Name: " . $row['FirstName'] . "<br>";
        echo "Last Name: " . $row['LastName'] . "<br>";
        echo "Date of Birth: " . $row['DateOfBirth'] . "<br>";
        echo "Category: " . $row['Category'] . "<br><br>";
    }
} else {
    echo "No passengers found for this flight booking ID.";
}

$conn->close();
?>
