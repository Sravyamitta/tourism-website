<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Deals - Stays</title>
    <link rel="stylesheet" href="index.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <header>
        <div id="date-time"></div><br/>
        <?php
        if (isset($_SESSION['phone'])) {
            echo "<div>Welcome, " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "</div>";
        } else {
            echo '<a href="login.php">Login</a>';
        }
        ?>
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
            <h1>Stays</h1>
            <form id="staysForm" onsubmit="return validateStaysForm(event)">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br>

                <label for="checkinDate">Check-in Date:</label>
                <input type="date" id="checkinDate" name="checkinDate" required><br>

                <label for="checkoutDate">Check-out Date:</label>
                <input type="date" id="checkoutDate" name="checkoutDate" required><br>

                <label for="adults">Adults/Children (above 5):</label>
                <input type="number" id="adults" name="adults" min="1" max="4" value="1" required><br>

                <label for="infants">Infants (under 5):</label>
                <input type="number" id="infants" name="infants" min="0" max="4" value="0" required><br>

                <input type="submit" value="Submit">
            </form>
            <div id="hotels"></div>
        </main>
    </div>

    <footer>
        <p>SRAVYA MEGHANA MITTA - smm230008</p>
        <p>ABHINAV SANTHOSH - axs230311</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('hotels.php')
                .then(response => response.json())
                .then(data => {
                    const hotelsDiv = document.getElementById('hotels');
                    hotelsDiv.innerHTML = ''; // Clear previous data
                    if (data && data.hotels) {
                        data.hotels.forEach(hotel => {
                            const hotelDiv = document.createElement('div');
                            hotelDiv.classList.add('hotel');
                            hotelDiv.innerHTML = `
                                <h3>${hotel.name}</h3>
                                <p>City: ${hotel.city}</p>
                                <p>Price per Night: $${hotel.price}</p>
                                <button onclick="addToCart('${hotel.id}', '${hotel.name}', '${hotel.city}', ${hotel.price})">Add to Cart</button>
                            `;
                            hotelsDiv.appendChild(hotelDiv);
                        });
                    } else {
                        hotelsDiv.innerHTML = '<p>No hotels available.</p>';
                    }
                })
                .catch(error => console.error("Error fetching hotels:", error));
        });

        function validateStaysForm(event) {
            event.preventDefault();

            let city = document.getElementById("city").value.trim();
            let checkinDate = document.getElementById("checkinDate").value;
            let checkoutDate = document.getElementById("checkoutDate").value;
            let adults = parseInt(document.getElementById("adults").value);
            let infants = parseInt(document.getElementById("infants").value);

            const validCities = ["dallas", "houston", "austin", "san antonio", "galveston", "los angeles", "san francisco", "san diego", "san jose", "pasadena", "del mar", "beverly hills"];
            const checkinDateObj = new Date(checkinDate);
            const checkoutDateObj = new Date(checkoutDate);
            const minDate = new Date("2024-09-01");
            const maxDate = new Date("2024-12-01");

            let cityLower = city.toLowerCase();

            let valid = true;

            // Clear previous error messages
            // (Assuming you want to show errors dynamically, implement this in your code.)

            if (!validCities.includes(cityLower)) {
                alert("City must be a city in Texas or California.");
                valid = false;
            }

            if (checkinDate === "" || checkinDateObj < minDate || checkinDateObj > maxDate) {
                alert("Check-in date must be between Sep 1, 2024, and Dec 1, 2024.");
                valid = false;
            }

            if (checkoutDate === "" || checkoutDateObj < minDate || checkoutDateObj > maxDate) {
                alert("Check-out date must be between Sep 1, 2024, and Dec 1, 2024.");
                valid = false;
            }

            if (checkinDateObj > checkoutDateObj) {
                alert("Check-in date should be less than or equal to check-out date.");
                valid = false;
            }

            if (valid) {
                let numberOfRooms = Math.ceil(adults / 2) + Math.ceil(infants / 2);
                alert(`City: ${city}, Check-In Date: ${checkinDate}, Check-Out Date: ${checkoutDate}, Adults: ${adults}, Infants: ${infants}, Rooms Required: ${numberOfRooms}`);
                fetchFilteredHotels(city, checkinDate, checkoutDate, numberOfRooms);
            }

            return valid;
        }

        function fetchFilteredHotels(city, checkinDate, checkoutDate, numberOfRooms) {
            fetch('get_hotels.php')
                .then(response => response.json())
                .then(data => {
                    let hotelList = document.getElementById('hotels');
                    hotelList.innerHTML = "<h3>Available Hotels</h3>";

                    if (data.error) {
                        hotelList.innerHTML += `<p>${data.error}</p>`;
                        return;
                    }

                    let filteredHotels = data.hotels.filter(hotel => hotel.city.toLowerCase() === city.toLowerCase());

                    filteredHotels.forEach(hotel => {
                        let hotelItem = document.createElement('div');
                        hotelItem.innerHTML = `
                            <strong>${hotel['hotel-name']}</strong><br>
                            City: ${hotel.city}<br>
                            Price per Night (per room): $${hotel['price-per-night']}<br>
                            <button onclick="addToCart('${hotel['hotel-id']}', '${hotel['hotel-name']}', '${hotel.city}', ${hotel['price-per-night']})">Add to Cart</button>
                            <hr>`;
                        hotelList.appendChild(hotelItem);
                    });
                })
                .catch(error => console.error('Error fetching hotels:', error));
        }

        function addToCart(hotelId, hotelName, city, pricePerNight) {
            let cartItem = {
                hotelId: hotelId,
                hotelName: hotelName,
                city: city,
                pricePerNight: pricePerNight,
                checkInDate: document.getElementById("checkinDate").value,
                checkOutDate: document.getElementById("checkoutDate").value,
                numberOfRooms: Math.ceil(parseInt(document.getElementById("adults").value) / 2) + Math.ceil(parseInt(document.getElementById("infants").value) / 2),
                adults: document.getElementById("adults").value,
                infants: document.getElementById("infants").value
            };

            let cart = JSON.parse(localStorage.getItem('cart')) || { stays: [] };
            cart.stays.push(cartItem);
            localStorage.setItem('cart', JSON.stringify(cart));

            window.location.href = "cart.php";
        }
    </script>
</body>

</html>
