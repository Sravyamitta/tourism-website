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

$ssn = $_POST['ssn'] ?? '';

$sql = "SELECT fb.FlightBookingID, f.Origin, f.Destination, f.DepartureDate, f.ArrivalDate, f.DepartureTime, f.ArrivalTime, f.Price AS FlightPrice, fb.TotalPrice AS BookingPrice 
        FROM Tickets t 
        JOIN FlightBooking fb ON t.FlightBookingID = fb.FlightBookingID 
        JOIN Flights f ON fb.FlightID = f.FlightID 
        WHERE t.SSN = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ssn);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h4>Flights booked by SSN: $ssn</h4>";
    while ($row = $result->fetch_assoc()) {
        echo "Flight Booking ID: " . $row['FlightBookingID'] . "<br>";
        echo "Origin: " . $row['Origin'] . "<br>";
        echo "Destination: " . $row['Destination'] . "<br>";
        echo "Departure Date: " . $row['DepartureDate'] . "<br>";
        echo "Arrival Date: " . $row['ArrivalDate'] . "<br>";
        echo "Departure Time: " . $row['DepartureTime'] . "<br>";
        echo "Arrival Time: " . $row['ArrivalTime'] . "<br>";
        echo "Flight Price: $" . $row['FlightPrice'] . "<br>";
        echo "Booking Price: $" . $row['BookingPrice'] . "<br><br>";
    }
} else {
    echo "No flights found for this SSN.";
}

$conn->close();
?>
