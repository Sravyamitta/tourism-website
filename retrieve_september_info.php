<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpl";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the query to get booked flights and hotels for September 2024
$flight_query = "SELECT fb.FlightBookingID, f.FlightID, f.Origin, f.Destination, f.DepartureDate, f.ArrivalDate, f.DepartureTime, f.ArrivalTime, f.Price
                 FROM FlightBooking fb
                 JOIN Flights f ON fb.FlightID = f.FlightID
                 WHERE f.DepartureDate BETWEEN '2024-09-01' AND '2024-09-30'";

$hotel_query = "SELECT hb.HotelBookingID, h.HotelID, h.HotelName, h.City, hb.CheckInDate, hb.CheckOutDate, hb.NumberOfRooms, hb.PricePerNight, hb.TotalPrice
                FROM HotelBooking hb
                JOIN Hotel h ON hb.HotelID = h.HotelID
                WHERE hb.CheckInDate BETWEEN '2024-09-01' AND '2024-09-30'";

// Execute flight query
$flight_result = $conn->query($flight_query);

// Display flight bookings
echo "<h4>Flights Booked in September 2024</h4>";
if ($flight_result->num_rows > 0) {
    while ($row = $flight_result->fetch_assoc()) {
        echo "Flight Booking ID: " . $row['FlightBookingID'] . "<br>";
        echo "Flight ID: " . $row['FlightID'] . "<br>";
        echo "Origin: " . $row['Origin'] . "<br>";
        echo "Destination: " . $row['Destination'] . "<br>";
        echo "Departure Date: " . $row['DepartureDate'] . "<br>";
        echo "Arrival Date: " . $row['ArrivalDate'] . "<br>";
        echo "Departure Time: " . $row['DepartureTime'] . "<br>";
        echo "Arrival Time: " . $row['ArrivalTime'] . "<br>";
        echo "Price: $" . $row['Price'] . "<br><br>";
    }
} else {
    echo "No flights booked in September 2024.";
}

// Execute hotel query
$hotel_result = $conn->query($hotel_query);

// Display hotel bookings
echo "<h4>Hotels Booked in September 2024</h4>";
if ($hotel_result->num_rows > 0) {
    while ($row = $hotel_result->fetch_assoc()) {
        echo "Hotel Booking ID: " . $row['HotelBookingID'] . "<br>";
        echo "Hotel ID: " . $row['HotelID'] . "<br>";
        echo "Hotel Name: " . $row['HotelName'] . "<br>";
        echo "City: " . $row['City'] . "<br>";
        echo "Check-in Date: " . $row['CheckInDate'] . "<br>";
        echo "Check-out Date: " . $row['CheckOutDate'] . "<br>";
        echo "Number of Rooms: " . $row['NumberOfRooms'] . "<br>";
        echo "Price per Night: $" . $row['PricePerNight'] . "<br>";
        echo "Total Price: $" . $row['TotalPrice'] . "<br><br>";
    }
} else {
    echo "No hotels booked in September 2024.";
}

$conn->close();
?>
