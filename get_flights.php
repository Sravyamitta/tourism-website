<?php
header('Content-Type: application/xml');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpl";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve all flights
$sql = "SELECT * FROM Flights";
$result = $conn->query($sql);

// Create a new XML document
$xml = new SimpleXMLElement('<flights/>');

// Check if there are any flights available
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add each flight as a child node in the XML document
        $flight = $xml->addChild('flight');
        $flight->addChild('flight-id', $row['FlightID']);
        $flight->addChild('origin', $row['Origin']);
        $flight->addChild('destination', $row['Destination']);
        $flight->addChild('departure-date', $row['DepartureDate']);
        $flight->addChild('arrival-date', $row['ArrivalDate']);
        $flight->addChild('departure-time', $row['DepartureTime']);
        $flight->addChild('arrival-time', $row['ArrivalTime']);
        $flight->addChild('available-seats', $row['NumberOfAvailableSeats']);
        $flight->addChild('price', $row['Price']);
    }
} else {
    echo "No flights available.";
}

// Close the database connection
$conn->close();

// Output the XML document
echo $xml->asXML();
?>
