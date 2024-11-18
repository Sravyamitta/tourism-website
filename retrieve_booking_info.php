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

$hotelBookingId = $_POST['hotelBookingId'] ?? '';
$flightBookingId = $_POST['flightBookingId'] ?? '';

if ($hotelBookingId) {
    $sqlHotel = "SELECT * FROM HotelBooking WHERE HotelBookingID = ?";
    $stmt = $conn->prepare($sqlHotel);
    $stmt->bind_param("s", $hotelBookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Hotel Booking ID: " . $row['HotelBookingID'] . "<br>";
            echo "Hotel ID: " . $row['HotelID'] . "<br>";
            echo "Check-In Date: " . $row['CheckInDate'] . "<br>";
            echo "Check-Out Date: " . $row['CheckOutDate'] . "<br>";
            echo "Number of Rooms: " . $row['NumberOfRooms'] . "<br>";
            echo "Price Per Night: " . $row['PricePerNight'] . "<br>";
            echo "Total Price: " . $row['TotalPrice'] . "<br>";
        }
    } else {
        echo "No hotel booking found.";
    }
}

if ($flightBookingId) {
    $sqlFlight = "SELECT * FROM FlightBooking WHERE FlightBookingID = ?";
    $stmt = $conn->prepare($sqlFlight);
    $stmt->bind_param("s", $flightBookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Flight Booking ID: " . $row['FlightBookingID'] . "<br>";
            echo "Flight ID: " . $row['FlightID'] . "<br>";
            echo "Total Price: " . $row['TotalPrice'] . "<br>";
        }
    } else {
        echo "No flight booking found.";
    }
}

$conn->close();
?>
