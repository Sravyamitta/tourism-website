<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Deals - Home</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div id="date-time"></div><br/>
        <?php if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
            <p>Welcome, <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name']; ?>!</p>
        <?php endif; ?>
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
            <section class="hero">
                <h1>Backpack Traveler</h1>
                <p>For real travel bloggers looking to share their adventure with the world.</p>
                <button class="cta-button">Purchase</button>
            </section>
            <section class="featured-deals">
                <div class="deal">
                    <img src="venice.jpg" alt="Deal 1">
                    <p>Venice - Meet the photographer who chases stars.</p>
                </div>
                <div class="deal">
                    <img src="paris.jpg" alt="Deal 2">
                    <p>Paris - How to spend a week in Paris.</p>
                </div>
                <div class="deal">
                    <img src="beach.jpg" alt="Deal 3">
                    <p>Discover amazing beach destinations at unbeatable prices.</p>
                </div>
            </section>
        </main>
    </div>
    <footer>
        <p>SRAVYA MEGHANA MITTA-smm230008</p>
        <p>ABHINAV SANTHOSH-axs230311</p>
        <p>CS 6314.0U1 - Web Programming Languages - Su24</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
