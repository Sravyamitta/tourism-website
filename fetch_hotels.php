<?php
header('Content-Type: application/json');

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpl";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Query to fetch hotel data
$sql = "SELECT hotel_id, hotel_name, city, price_per_night FROM hotels";
$result = $conn->query($sql);

$hotels = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hotels[] = [
            'hotel-id' => $row['hotel_id'],
            'hotel-name' => $row['hotel_name'],
            'city' => $row['city'],
            'price-per-night' => $row['price_per_night']
        ];
    }
} else {
    echo json_encode(['error' => 'No hotels found']);
    exit;
}

// Close the connection
$conn->close();

// Return the JSON data
echo json_encode(['hotels' => $hotels]);
?>
