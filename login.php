<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Input validation
    if (empty($phone) || empty($password)) {
        $error = "Phone number and password are required.";
    } else {
        // Check if the user exists in the database
        $sql = "SELECT * FROM Users WHERE PhoneNumber='$phone'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $row['Password'])) {
                // Set session variables
                $_SESSION['phone'] = $row['PhoneNumber'];
                $_SESSION['first_name'] = $row['FirstName'];
                $_SESSION['last_name'] = $row['LastName'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that phone number.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
    <h1>Login</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form id="loginForm" method="post" action="login.php">
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
