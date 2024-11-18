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

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'retrieveFlightsAdmin':
        $sql = "SELECT fb.FlightBookingID, f.FlightID, f.Origin, f.Destination, f.DepartureDate, f.ArrivalDate, f.DepartureTime, f.ArrivalTime, f.NumberOfAvailableSeats, f.Price, fb.TotalPrice
                FROM FlightBooking fb
                JOIN Flights f ON fb.FlightID = f.FlightID
                WHERE f.Origin IN ('Austin', 'Dallas', 'Houston', 'San Antonio') 
                AND f.DepartureDate BETWEEN '2024-09-01' AND '2024-10-31'";
        break;

    case 'retrieveHotelsAdmin':
        $sql = "SELECT hb.HotelBookingID, h.HotelID, h.HotelName, h.City, hb.CheckInDate, hb.CheckOutDate, hb.NumberOfRooms, hb.PricePerNight, hb.TotalPrice
                FROM HotelBooking hb
                JOIN Hotel h ON hb.HotelID = h.HotelID
                WHERE h.City IN ('Austin', 'Dallas', 'Houston', 'San Antonio')
                AND hb.CheckInDate BETWEEN '2024-09-01' AND '2024-10-31'";
        break;

    case 'retrieveMostExpensiveHotels':
        $sql = "SELECT hb.HotelBookingID, h.HotelID, h.HotelName, h.City, hb.CheckInDate, hb.CheckOutDate, hb.NumberOfRooms, hb.PricePerNight, hb.TotalPrice
                FROM HotelBooking hb
                JOIN Hotel h ON hb.HotelID = h.HotelID
                ORDER BY hb.TotalPrice DESC
                LIMIT 1";
        break;

    case 'retrieveFlightsWithInfant':
        $sql = "SELECT DISTINCT fb.FlightBookingID, f.FlightID, f.Origin, f.Destination, f.DepartureDate, f.ArrivalDate, f.DepartureTime, f.ArrivalTime, f.NumberOfAvailableSeats, f.Price, fb.TotalPrice
                FROM FlightBooking fb
                JOIN Tickets t ON fb.FlightBookingID = t.FlightBookingID
                JOIN Passenger p ON t.SSN = p.SSN
                JOIN Flights f ON fb.FlightID = f.FlightID
                WHERE p.Category = 'Infants'";
        break;

    case 'retrieveFlightsWithInfantAndChildren':
        $sql = "SELECT fb.FlightBookingID, f.FlightID, f.Origin, f.Destination, f.DepartureDate, f.ArrivalDate, f.DepartureTime, f.ArrivalTime, f.NumberOfAvailableSeats, f.Price, fb.TotalPrice
                FROM FlightBooking fb
                JOIN Tickets t ON fb.FlightBookingID = t.FlightBookingID
                JOIN Passenger p ON t.SSN = p.SSN
                JOIN Flights f ON fb.FlightID = f.FlightID
                WHERE p.Category = 'Children'
                GROUP BY fb.FlightBookingID
                HAVING COUNT(p.Category) >= 5";
        break;

    case 'retrieveMostExpensiveFlights':
        $sql = "SELECT fb.FlightBookingID, f.FlightID, f.Origin, f.Destination, f.DepartureDate, f.ArrivalDate, f.DepartureTime, f.ArrivalTime, f.NumberOfAvailableSeats, f.Price, fb.TotalPrice
                FROM FlightBooking fb
                JOIN Flights f ON fb.FlightID = f.FlightID
                ORDER BY fb.TotalPrice DESC
                LIMIT 1";
        break;

    case 'retrieveFlightsNoInfant':
        $sql = "SELECT fb.FlightBookingID, f.FlightID, f.Origin, f.Destination, f.DepartureDate, f.ArrivalDate, f.DepartureTime, f.ArrivalTime, f.NumberOfAvailableSeats, f.Price, fb.TotalPrice
                FROM FlightBooking fb
                JOIN Flights f ON fb.FlightID = f.FlightID
                LEFT JOIN Tickets t ON fb.FlightBookingID = t.FlightBookingID
                LEFT JOIN Passenger p ON t.SSN = p.SSN
                WHERE f.Origin IN ('Austin', 'Dallas', 'Houston', 'San Antonio')
                AND (p.Category IS NULL OR p.Category != 'Infants')
                AND f.DepartureDate BETWEEN '2024-09-01' AND '2024-10-31'";
        break;

    case 'retrieveFlightsArrivingCalifornia':
        $sql = "SELECT COUNT(*) AS flight_count
                FROM FlightBooking fb
                JOIN Flights f ON fb.FlightID = f.FlightID
                WHERE f.Destination IN ('Los Angeles', 'San Diego', 'San Francisco', 'San Jose')
                AND f.ArrivalDate BETWEEN '2024-09-01' AND '2024-10-31'";
        break;

    default:
        echo "Invalid action.";
        exit;
}

$result = $conn->query($sql);

if ($result) {
    if ($action == 'retrieveFlightsArrivingCalifornia') {
        $row = $result->fetch_assoc();
        echo "Number of booked flights arriving in California: " . $row['flight_count'];
    } else {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                echo ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "<br>";
            }
            echo "<hr>";
        }
    }
} else {
    echo "No results found.";
}

$conn->close();
?>
