<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wpl";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['phone'])) {
    header("location: login.php");
    exit;
}

function validateInputs($data) {
    $errors = [];

    $tripType = $data['tripType'];
    $origin = $data['origin'];
    $destination = $data['destination'];
    $departureDate = $data['departureDate'];
    $returnDate = isset($data['returnDate']) ? $data['returnDate'] : null;
    $adults = $data['adults'];
    $children = $data['children'];
    $infants = $data['infants'];

    if (!in_array($tripType, ['oneWay', 'roundTrip'])) {
        $errors[] = "Invalid trip type.";
    }

    if (!in_array($origin, ['Dallas', 'Houston', 'Austin', 'San Francisco', 'Los Angeles', 'San Diego'])) {
        $errors[] = "Origin must be a city in Texas or California.";
    }

    if (!in_array($destination, ['Dallas', 'Houston', 'Austin', 'San Francisco', 'Los Angeles', 'San Diego'])) {
        $errors[] = "Destination must be a city in Texas or California.";
    }

    $departureDateObj = new DateTime($departureDate);
    $startDate = new DateTime("2024-09-01");
    $endDate = new DateTime("2024-12-01");

    if ($departureDateObj < $startDate || $departureDateObj > $endDate) {
        $errors[] = "Departure date must be between Sep 1, 2024 and Dec 1, 2024.";
    }

    if ($tripType == 'roundTrip' && $returnDate) {
        $returnDateObj = new DateTime($returnDate);
        if ($returnDateObj < $startDate || $returnDateObj > $endDate) {
            $errors[] = "Return date must be between Sep 1, 2024 and Dec 1, 2024.";
        }
    }

    if ($adults < 1 || $adults > 4) {
        $errors[] = "Number of adults must be between 1 and 4.";
    }

    if ($children < 0 || $children > 4) {
        $errors[] = "Number of children must be between 0 and 4.";
    }

    if ($infants < 0 || $infants > 4) {
        $errors[] = "Number of infants must be between 0 and 4.";
    }

    return $errors;
}

function getAvailableFlights($origin, $destination, $date, $passengers) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM Flights WHERE Origin = ? AND Destination = ? AND DepartureDate = ? AND NumberOfAvailableSeats >= ?");
    $stmt->bind_param("sssi", $origin, $destination, $date, $passengers);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $dateObj = new DateTime($date);
        $dates = [
            $dateObj->modify('-3 days')->format('Y-m-d'),
            $dateObj->modify('+1 day')->format('Y-m-d'),
            $dateObj->modify('+1 day')->format('Y-m-d'),
            $dateObj->modify('+1 day')->format('Y-m-d')
        ];

        $stmt = $conn->prepare("SELECT * FROM Flights WHERE Origin = ? AND Destination = ? AND (DepartureDate IN (?, ?, ?, ?)) AND NumberOfAvailableSeats >= ?");
        $stmt->bind_param("sssssi", $origin, $destination, $dates[0], $dates[1], $dates[2], $dates[3], $passengers);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    $flights = [];
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }

    return $flights;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = validateInputs($_POST);

    if (empty($errors)) {
        $tripType = $_POST['tripType'];
        $origin = $_POST['origin'];
        $destination = $_POST['destination'];
        $departureDate = $_POST['departureDate'];
        $returnDate = isset($_POST['returnDate']) ? $_POST['returnDate'] : null;
        $adults = $_POST['adults'];
        $children = $_POST['children'];
        $infants = $_POST['infants'];
        $totalPassengers = $adults + $children + $infants;

        $availableFlights = getAvailableFlights($origin, $destination, $departureDate, $totalPassengers);

        if ($tripType == 'roundTrip' && $returnDate) {
            $returnFlights = getAvailableFlights($destination, $origin, $returnDate, $totalPassengers);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Deals - Flights</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div id="date-time"></div><br/>
    </header>

    <nav>
        <ul>
        <li><a href="index.php">Home</a></li>
            <li><a href="stays.php">Stays</a></li>
            <li><a href="flights.php">Flights</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="my_account.php">My Account</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="sidebar">
            <h2>Settings</h2>
            <ul>
                <li><label for="fontSize">Font Size:</label></li>
                <li><input type="range" id="fontSize" min="10" max="30" value="16"></li>
                <li><label for="bgColor">Background Color:</label></li>
                <li><input type="color" id="bgColor" value="#ffffff"></li>
            </ul>
        </div>
        <main id="mainContent">
            <h1>Flights</h1>
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="flightForm" method="post" action="flights.php">
                <label for="tripType">Trip Type:</label>
                <select id="tripType" name="tripType" onchange="toggleReturnDate()">
                    <option value="oneWay" <?php echo isset($_POST['tripType']) && $_POST['tripType'] == 'oneWay' ? 'selected' : ''; ?>>One Way</option>
                    <option value="roundTrip" <?php echo isset($_POST['tripType']) && $_POST['tripType'] == 'roundTrip' ? 'selected' : ''; ?>>Round Trip</option>
                </select><br>

                <label for="origin">Origin:</label>
                <input type="text" id="origin" name="origin" required value="<?php echo isset($_POST['origin']) ? $_POST['origin'] : ''; ?>"><br>

                <label for="destination">Destination:</label>
                <input type="text" id="destination" name="destination" required value="<?php echo isset($_POST['destination']) ? $_POST['destination'] : ''; ?>"><br>

                <label for="departureDate">Departure Date:</label>
                <input type="date" id="departureDate" name="departureDate" required value="<?php echo isset($_POST['departureDate']) ? $_POST['departureDate'] : ''; ?>"><br>

                <label for="returnDate" id="returnDateLabel" style="display: none;">Return Date:</label>
                <input type="date" id="returnDate" name="returnDate" style="display: none;" value="<?php echo isset($_POST['returnDate']) ? $_POST['returnDate'] : ''; ?>"><br>

                <label for="adults">Adults (1-4):</label>
                <input type="number" id="adults" name="adults" min="1" max="4" required value="<?php echo isset($_POST['adults']) ? $_POST['adults'] : 1; ?>"><br>

                <label for="children">Children (0-4):</label>
                <input type="number" id="children" name="children" min="0" max="4" value="<?php echo isset($_POST['children']) ? $_POST['children'] : 0; ?>"><br>

                <label for="infants">Infants (0-4):</label>
                <input type="number" id="infants" name="infants" min="0" max="4" value="<?php echo isset($_POST['infants']) ? $_POST['infants'] : 0; ?>"><br>

                <button type="submit">Search Flights</button>
            </form>

            <?php if (isset($availableFlights) && !empty($availableFlights)): ?>
                <h2>Available Flights</h2>
                <table>
                    <tr>
                        <th>Flight ID</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Date</th>
                        <th>Arrival Date</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Available Seats</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($availableFlights as $flight): ?>
                        <tr>
                            <td><?php echo $flight['FlightID']; ?></td>
                            <td><?php echo $flight['Origin']; ?></td>
                            <td><?php echo $flight['Destination']; ?></td>
                            <td><?php echo $flight['DepartureDate']; ?></td>
                            <td><?php echo $flight['ArrivalDate']; ?></td>
                            <td><?php echo $flight['DepartureTime']; ?></td>
                            <td><?php echo $flight['ArrivalTime']; ?></td>
                            <td><?php echo $flight['NumberOfAvailableSeats']; ?></td>
                            <td><?php echo $flight['Price']; ?></td>
                            <td><button onclick="addToCart('<?= $flight['FlightID']; ?>')">Add to Cart</button></td>                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php elseif (isset($availableFlights)): ?>
                <p>No flights available on the selected date. Showing flights within ±3 days.</p>
                <table>
                    <tr>
                        <th>Flight ID</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Date</th>
                        <th>Arrival Date</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Available Seats</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($availableFlights as $flight): ?>
                        <tr>
                            <td><?php echo $flight['FlightID']; ?></td>
                            <td><?php echo $flight['Origin']; ?></td>
                            <td><?php echo $flight['Destination']; ?></td>
                            <td><?php echo $flight['DepartureDate']; ?></td>
                            <td><?php echo $flight['ArrivalDate']; ?></td>
                            <td><?php echo $flight['DepartureTime']; ?></td>
                            <td><?php echo $flight['ArrivalTime']; ?></td>
                            <td><?php echo $flight['NumberOfAvailableSeats']; ?></td>
                            <td><?php echo $flight['Price']; ?></td>
                            <td><button onclick="addToCart('<?= $flight['FlightID']; ?>')">Add to Cart</button></td>                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <?php if (isset($returnFlights) && !empty($returnFlights)): ?>
                <h2>Return Flights</h2>
                <table>
                    <tr>
                        <th>Flight ID</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Departure Date</th>
                        <th>Arrival Date</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                        <th>Available Seats</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($returnFlights as $flight): ?>
                        <tr>
                            <td><?php echo $flight['FlightID']; ?></td>
                            <td><?php echo $flight['Origin']; ?></td>
                            <td><?php echo $flight['Destination']; ?></td>
                            <td><?php echo $flight['DepartureDate']; ?></td>
                            <td><?php echo $flight['ArrivalDate']; ?></td>
                            <td><?php echo $flight['DepartureTime']; ?></td>
                            <td><?php echo $flight['ArrivalTime']; ?></td>
                            <td><?php echo $flight['NumberOfAvailableSeats']; ?></td>
                            <td><?php echo $flight['Price']; ?></td>
                            <td><button onclick="addToCartRet('<?= $flight['FlightID']; ?>')">Add to Cart</button></td>                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function toggleReturnDate() {
            const tripType = document.getElementById('tripType').value;
            const returnDateLabel = document.getElementById('returnDateLabel');
            const returnDate = document.getElementById('returnDate');
            
            if (tripType === 'roundTrip') {
                returnDateLabel.style.display = 'block';
                returnDate.style.display = 'block';
            } else {
                returnDateLabel.style.display = 'none';
                returnDate.style.display = 'none';
            }
        }

        function addToCart(flightId) {
            let tripType = document.getElementById("tripType").value;

            fetch('get_flights.php')
                .then(response => response.text())
                .then(data => {
                    let parser = new DOMParser();
                    let xml = parser.parseFromString(data, "application/xml");
                    let flights = xml.getElementsByTagName('flight');

                    let adults = parseInt(document.getElementById("adults").value) || 0;
                    let children = parseInt(document.getElementById("children").value) || 0;
                    let infants = parseInt(document.getElementById("infants").value) || 0;

                    let cart = JSON.parse(localStorage.getItem('cart')) || { departingFlight: null, returningFlight: null, stays: [] };
                    console.log(flightId);
                    for (let flight of flights) {
                        if (flight.getElementsByTagName('flight-id')[0].textContent == flightId) {
                            let adultTicketPrice = parseFloat(flight.getElementsByTagName('price')[0].textContent);
                            let totalPrice = calculateTotalPrice(adultTicketPrice, adults, children, infants);
                            console.log(tripType);
                            let selectedFlight = {
                                flightId: flightId,
                                origin: flight.getElementsByTagName('origin')[0].textContent,
                                destination: flight.getElementsByTagName('destination')[0].textContent,
                                depDate: flight.getElementsByTagName('departure-date')[0].textContent,
                                arrDate: flight.getElementsByTagName('arrival-date')[0].textContent,
                                depTime: flight.getElementsByTagName('departure-time')[0].textContent,
                                arrTime: flight.getElementsByTagName('arrival-time')[0].textContent,
                                seats: flight.getElementsByTagName('available-seats')[0].textContent,
                                adults: adults,
                                children: children,
                                infants: infants,
                                totalPrice: totalPrice,
                                adultTicketPrice: adultTicketPrice
                            };

                            cart.departingFlight = selectedFlight;

                            break;
                        }
                    }
                    alert(cart);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    window.location.href = 'cart.php';  // Redirect to cart page
                })
                .catch(error => console.error('Error fetching the XML file:', error));
        }

        function addToCartRet(flightId) {
            let tripType = document.getElementById("tripType").value;

            fetch('get_flights.php')
                .then(response => response.text())
                .then(data => {
                    let parser = new DOMParser();
                    let xml = parser.parseFromString(data, "application/xml");
                    let flights = xml.getElementsByTagName('flight');

                    let adults = parseInt(document.getElementById("adults").value) || 0;
                    let children = parseInt(document.getElementById("children").value) || 0;
                    let infants = parseInt(document.getElementById("infants").value) || 0;

                    let cart = JSON.parse(localStorage.getItem('cart')) || { departingFlight: null, returningFlight: null, stays: [] };
                    console.log(flightId);
                    for (let flight of flights) {
                        if (flight.getElementsByTagName('flight-id')[0].textContent == flightId) {
                            let adultTicketPrice = parseFloat(flight.getElementsByTagName('price')[0].textContent);
                            let totalPrice = calculateTotalPrice(adultTicketPrice, adults, children, infants);
                            console.log(tripType);
                            let selectedFlight = {
                                flightId: flightId,
                                origin: flight.getElementsByTagName('origin')[0].textContent,
                                destination: flight.getElementsByTagName('destination')[0].textContent,
                                depDate: flight.getElementsByTagName('departure-date')[0].textContent,
                                arrDate: flight.getElementsByTagName('arrival-date')[0].textContent,
                                depTime: flight.getElementsByTagName('departure-time')[0].textContent,
                                arrTime: flight.getElementsByTagName('arrival-time')[0].textContent,
                                seats: flight.getElementsByTagName('available-seats')[0].textContent,
                                adults: adults,
                                children: children,
                                infants: infants,
                                totalPrice: totalPrice,
                                adultTicketPrice: adultTicketPrice
                            };

                            cart.returningFlight = selectedFlight;

                            break;
                        }
                    }
                    alert(cart);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    window.location.href = 'cart.php';  // Redirect to cart page
                })
                .catch(error => console.error('Error fetching the XML file:', error));
        }

        function calculateTotalPrice(adultTicketPrice, adults, children, infants) {
            let totalPrice = (adults + children + infants) * adultTicketPrice;
            return totalPrice;
        }

        window.onload = function () {
            const fontSizeInput = document.getElementById('fontSize');
            const bgColorInput = document.getElementById('bgColor');
            const mainContent = document.getElementById('mainContent');

            fontSizeInput.addEventListener('input', function () {
                mainContent.style.fontSize = fontSizeInput.value + 'px';
            });

            bgColorInput.addEventListener('input', function () {
                mainContent.style.backgroundColor = bgColorInput.value;
            });
        };
    </script>
</body>
</html>
