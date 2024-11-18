<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Deals - My Account</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Inline styles for simplicity */
        #loadFlightsMessage, #loadHotelsMessage, #bookingInfoMessage, #passengersInfoMessage, #septemberInfoMessage, #flightsBySSNMessage,
        #flightsAdminMessage, #hotelsAdminMessage, #mostExpensiveHotelsMessage, #flightsWithInfantMessage, #flightsWithInfantAndChildrenMessage,
        #mostExpensiveFlightsMessage, #flightsNoInfantMessage, #flightsArrivingCaliforniaMessage {
            margin-top: 10px;
        }
        #uploadStatus {
            margin-top: 10px;
            color: green;
        }
    </style>
</head>
<body>
    <!-- header -->
    <header class="row">
        <div class="header-data">
            <span>CS 6314</span><br>
            <span>Assignment #4</span>
        </div>
        <div id="datetime"></div>
        <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            echo "<div>Welcome, " . $_SESSION['firstname'] . " " . $_SESSION['lastname'] . "</div>";
        }
        ?>
    </header>
    
    <!-- navigation bar -->
    <div class="row navigations">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="stays.php">Stays</a></li>
                <li><a href="flights.php">Flights</a></li>
                <li><a href="cars.php">Cars</a></li>
                <li><a href="cruises.php">Cruises</a></li>
                <li><a href="contact-us.php">Contact Us</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="my-account.php">My Account</a></li>
            </ul>
        </nav>
    </div>

    <!-- side and main content -->
    <div class="row container">
        <section class="sidebar">
            <h2>Side Content</h2>
            <h4>Customization Controls :</h4>
            <div>
                <label for="fontsize">Font Size: </label>
                <input type="range" id="fontsize" min="5" max="50" value="16">
                <span id="fontsizeval">16px</span>
            </div>
            <div>
                <label for="bgColor">Background Color: </label>
                <input type="color" id="bgColor" value="#ffffff">
            </div><br>
        </section>
        <section class="mainContent">
            <h2>Main Content</h2>
            <p>This is some main content here.</p>
            <p>Along with some more main content.</p>

            <!-- My Account Section -->
            <h2>My Account</h2>
            <?php
            if (isset($_SESSION['phone'])) {
                echo "<h3>Welcome, " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "</h3>";

                // Define the phone number of the admin
                $admin_phone_number = '222-222-2222';

                if ($_SESSION['phone'] === $admin_phone_number) {
                    echo '<form id="loadFlightsForm">
                            <button type="button" id="loadFlightsButton">Load Flight Data</button>
                          </form>
                          <div id="loadFlightsMessage"></div>';
                    
                    echo '<form id="loadHotelsForm">
                            <button type="button" id="loadHotelsButton">Load Hotels Data</button>
                          </form>
                          <div id="loadHotelsMessage"></div>';

                    // Admin-specific forms
                    echo '<form id="retrieveFlightsAdminForm">
                            <h4>Admin: Retrieve Flights Departing from Texas (SEP-OCT 2024)</h4>
                            <button type="button" id="retrieveFlightsAdminButton">Retrieve Flights</button>
                          </form>
                          <div id="flightsAdminMessage"></div>';

                    echo '<form id="retrieveHotelsAdminForm">
                            <h4>Admin: Retrieve Hotels in Texas (SEP-OCT 2024)</h4>
                            <button type="button" id="retrieveHotelsAdminButton">Retrieve Hotels</button>
                          </form>
                          <div id="hotelsAdminMessage"></div>';

                    echo '<form id="retrieveMostExpensiveHotelsForm">
                            <h4>Admin: Retrieve Most Expensive Booked Hotels</h4>
                            <button type="button" id="retrieveMostExpensiveHotelsButton">Retrieve Hotels</button>
                          </form>
                          <div id="mostExpensiveHotelsMessage"></div>';

                    echo '<form id="retrieveFlightsWithInfantForm">
                            <h4>Admin: Retrieve Flights with Infant Passenger</h4>
                            <button type="button" id="retrieveFlightsWithInfantButton">Retrieve Flights</button>
                          </form>
                          <div id="flightsWithInfantMessage"></div>';

                    echo '<form id="retrieveFlightsWithInfantAndChildrenForm">
                            <h4>Admin: Retrieve Flights with Infant and 5 Children</h4>
                            <button type="button" id="retrieveFlightsWithInfantAndChildrenButton">Retrieve Flights</button>
                          </form>
                          <div id="flightsWithInfantAndChildrenMessage"></div>';

                    echo '<form id="retrieveMostExpensiveFlightsForm">
                            <h4>Admin: Retrieve Most Expensive Booked Flights</h4>
                            <button type="button" id="retrieveMostExpensiveFlightsButton">Retrieve Flights</button>
                          </form>
                          <div id="mostExpensiveFlightsMessage"></div>';

                    echo '<form id="retrieveFlightsNoInfantForm">
                            <h4>Admin: Retrieve Flights from Texas with No Infant Passenger</h4>
                            <button type="button" id="retrieveFlightsNoInfantButton">Retrieve Flights</button>
                          </form>
                          <div id="flightsNoInfantMessage"></div>';

                    echo '<form id="retrieveFlightsArrivingCaliforniaForm">
                            <h4>Admin: Retrieve Number of Flights Arriving in California (SEP-OCT 2024)</h4>
                            <button type="button" id="retrieveFlightsArrivingCaliforniaButton">Retrieve Flights</button>
                          </form>
                          <div id="flightsArrivingCaliforniaMessage"></div>';

                } else {
                    echo "<p>You are not an admin. Please contact the administrator for access.</p>";
                }

                echo '<form id="retrieveBookedInfoForm">
                        <h4>Retrieve Booked Information</h4>
                        <label for="hotelBookingId">Hotel Booking ID:</label>
                        <input type="text" id="hotelBookingId" name="hotelBookingId">
                        <label for="flightBookingId">Flight Booking ID:</label>
                        <input type="text" id="flightBookingId" name="flightBookingId">
                        <button type="button" id="retrieveBookingInfoButton">Retrieve Booking Information</button>
                      </form>
                      <div id="bookingInfoMessage"></div>';

                echo '<form id="retrievePassengersForm">
                        <h4>Retrieve Passengers in a Booked Flight</h4>
                        <label for="flightBookingIdPassengers">Flight Booking ID:</label>
                        <input type="text" id="flightBookingIdPassengers" name="flightBookingIdPassengers">
                        <button type="button" id="retrievePassengersButton">Retrieve Passengers</button>
                      </form>
                      <div id="passengersInfoMessage"></div>';

                echo '<form id="retrieveSeptemberInfoForm">
                        <h4>Retrieve Booked Information for SEP 2024</h4>
                        <button type="button" id="retrieveSeptemberInfoButton">Retrieve Information</button>
                      </form>
                      <div id="septemberInfoMessage"></div>';

                echo '<form id="retrieveFlightsBySSNForm">
                        <h4>Retrieve Flights for Specific SSN</h4>
                        <label for="ssn">SSN:</label>
                        <input type="text" id="ssn" name="ssn">
                        <button type="button" id="retrieveFlightsBySSNButton">Retrieve Flights</button>
                      </form>
                      <div id="flightsBySSNMessage"></div>';
                
            } else {
                echo "<p>Please log in to access this page.</p>";
            }
            ?>
        </section>
    </div>

    <!-- footer -->
    <footer class="row">
        <div>
        <p>SRAVYA MEGHANA MITTA-smm230008</p>
        <p>ABHINAV SANTHOSH-axs230311</p>
        <p>CS 6314.0U1 - Web Programming Languages - Su24</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            $('#loadFlightsButton').on('click', function() {
                $.ajax({
                    url: 'process_flights.php',
                    type: 'GET',
                    success: function(data) {
                        $('#loadFlightsMessage').html(data);
                    },
                    error: function() {
                        $('#loadFlightsMessage').html('Error loading flights.');
                    }
                });
            });

            $('#loadHotelsButton').on('click', function() {
                $.ajax({
                    url: 'load_hotels.php',
                    type: 'GET',
                    success: function(data) {
                        $('#loadHotelsMessage').html(data);
                    },
                    error: function() {
                        $('#loadHotelsMessage').html('Error loading hotels.');
                    }
                });
            });

            $('#retrieveFlightsAdminButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveFlightsAdmin' },
                    success: function(data) {
                        $('#flightsAdminMessage').html(data);
                    },
                    error: function() {
                        $('#flightsAdminMessage').html('Error retrieving flights.');
                    }
                });
            });

            $('#retrieveHotelsAdminButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveHotelsAdmin' },
                    success: function(data) {
                        $('#hotelsAdminMessage').html(data);
                    },
                    error: function() {
                        $('#hotelsAdminMessage').html('Error retrieving hotels.');
                    }
                });
            });

            $('#retrieveMostExpensiveHotelsButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveMostExpensiveHotels' },
                    success: function(data) {
                        $('#mostExpensiveHotelsMessage').html(data);
                    },
                    error: function() {
                        $('#mostExpensiveHotelsMessage').html('Error retrieving most expensive hotels.');
                    }
                });
            });

            $('#retrieveFlightsWithInfantButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveFlightsWithInfant' },
                    success: function(data) {
                        $('#flightsWithInfantMessage').html(data);
                    },
                    error: function() {
                        $('#flightsWithInfantMessage').html('Error retrieving flights with infants.');
                    }
                });
            });

            $('#retrieveFlightsWithInfantAndChildrenButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveFlightsWithInfantAndChildren' },
                    success: function(data) {
                        $('#flightsWithInfantAndChildrenMessage').html(data);
                    },
                    error: function() {
                        $('#flightsWithInfantAndChildrenMessage').html('Error retrieving flights with infants and children.');
                    }
                });
            });

            $('#retrieveMostExpensiveFlightsButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveMostExpensiveFlights' },
                    success: function(data) {
                        $('#mostExpensiveFlightsMessage').html(data);
                    },
                    error: function() {
                        $('#mostExpensiveFlightsMessage').html('Error retrieving most expensive flights.');
                    }
                });
            });

            $('#retrieveFlightsNoInfantButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveFlightsNoInfant' },                    
                    success: function(data) {
                        $('#flightsNoInfantMessage').html(data);
                    },
                    error: function() {
                        $('#flightsNoInfantMessage').html('Error retrieving flights with no infants.');
                    }
                });
            });

            $('#retrieveFlightsArrivingCaliforniaButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_flights_admin.php',
                    type: 'POST',
                    data: { action: 'retrieveFlightsArrivingCalifornia' },
                    success: function(data) {
                        $('#flightsArrivingCaliforniaMessage').html(data);
                    },
                    error: function() {
                        $('#flightsArrivingCaliforniaMessage').html('Error retrieving flights arriving in California.');
                    }
                });
            });

            $('#retrieveBookingInfoButton').on('click', function() {
                var hotelBookingId = $('#hotelBookingId').val();
                var flightBookingId = $('#flightBookingId').val();
                $.ajax({
                    url: 'retrieve_booking_info.php',
                    type: 'POST',
                    data: {
                        hotelBookingId: $('#hotelBookingId').val(),
                        flightBookingId: $('#flightBookingId').val()
                    },
                    success: function(data) {
                        $('#bookingInfoMessage').html(data);
                    },
                    error: function() {
                        $('#bookingInfoMessage').html('Error retrieving booking information.');
                    }
                });
            });

            $('#retrievePassengersButton').on('click', function() {
                var flightBookingIdPassengers = $('#flightBookingIdPassengers').val();
                $.ajax({
                    url: 'retrieve_passengers.php',
                    type: 'POST',
                    data: {
                        flightBookingIdPassengers: $('#flightBookingIdPassengers').val()
                    },
                    success: function(data) {
                        $('#passengersInfoMessage').html(data);
                    },
                    error: function() {
                        $('#passengersInfoMessage').html('Error retrieving passenger information.');
                    }
                });
            });

            $('#retrieveSeptemberInfoButton').on('click', function() {
                $.ajax({
                    url: 'retrieve_september_info.php',
                    type: 'POST',
                    success: function(data) {
                        $('#septemberInfoMessage').html(data);
                    },
                    error: function() {
                        $('#septemberInfoMessage').html('Error retrieving information for September 2024.');
                    }
                });
            });

            $('#retrieveFlightsBySSNButton').on('click', function() {
                var ssn = $('#ssn').val();
                $.ajax({
                    url: 'retrieve_flights_by_ssn.php',
                    type: 'POST',
                    data: {
                        ssn: $('#ssn').val()
                    },
                    success: function(data) {
                        $('#flightsBySSNMessage').html(data);
                    },
                    error: function() {
                        $('#flightsBySSNMessage').html('Error retrieving flights for SSN.');
                    }
                });
            });

            $('#fontsize').on('input', function() {
                var fontSize = $(this).val() + 'px';
                $('body').css('font-size', fontSize);
                $('#fontsizeval').text($(this).val() + 'px');
            });

            $('#bgColor').on('input', function() {
                var bgColor = $(this).val();
                $('body').css('background-color', bgColor);
            });
        });
    </script>
</body>
</html>
