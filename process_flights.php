<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['phone']) || $_SESSION['phone'] !== "222-222-2222") {
    echo "Access denied.";
    exit;
}

// Path to the XML file
$xmlFilePath = 'flights.xml';

// Check if the XML file exists
if (!file_exists($xmlFilePath)) {
    echo "XML file not found.";
    exit;
}

// Load XML file
$xml = simplexml_load_file($xmlFilePath);
if ($xml === false) {
    echo "Error loading XML file.";
    exit;
}

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'wpl');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Clear existing data (optional)
$mysqli->query("DELETE FROM Flights");

// Prepare the SQL statement
$stmt = $mysqli->prepare("
    INSERT INTO Flights 
    (FlightID, Origin, Destination, DepartureDate, ArrivalDate, DepartureTime, ArrivalTime, NumberOfAvailableSeats, Price) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

foreach ($xml->Flight as $flight) {
    $flightID = (string) $flight->FlightID;
    $origin = (string) $flight->Origin;
    $destination = (string) $flight->Destination;
    $departureDate = (string) $flight->DepartureDate;
    $arrivalDate = (string) $flight->ArrivalDate;
    $departureTime = (string) $flight->DepartureTime;
    $arrivalTime = (string) $flight->ArrivalTime;
    $numberOfAvailableSeats = (int) $flight->AvailableSeats;
    $price = (float) $flight->Price;

    // Bind parameters and execute the query
    $stmt->bind_param(
        'sssssissi', 
        $flightID, 
        $origin, 
        $destination, 
        $departureDate, 
        $arrivalDate, 
        $departureTime, 
        $arrivalTime, 
        $numberOfAvailableSeats, 
        $price
    );

    if (!$stmt->execute()) {
        echo "Execute failed: " . $stmt->error;
        exit;
    }
}

// Close the statement and connection
$stmt->close();
$mysqli->close();

echo "Flights data has been successfully processed and uploaded.";
?>
