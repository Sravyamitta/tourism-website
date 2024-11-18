<?php
session_start();

if ($_SESSION['phone'] !== '222-222-2222') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpl";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$jsonData = file_get_contents('hotels.json');
$hotels = json_decode($jsonData, true)['hotels'];

try {
    foreach ($hotels as $hotel) {
        $stmt = $conn->prepare("INSERT INTO hotel (HotelID, HotelName, City, PricePerNight) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $hotel['hotel-id'], $hotel['hotel-name'], $hotel['city'], $hotel['price-per-night']);
        $stmt->execute();
    }
    echo json_encode(['status' => 'success', 'message' => 'Hotels data loaded successfully.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error loading hotels data.', 'error' => $e->getMessage()]);
}

$conn->close();
?>
