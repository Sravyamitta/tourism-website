<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

    // Input validation
    if (empty($phone) || empty($password) || empty($confirm_password) || empty($first_name) || empty($last_name) || empty($dob) || empty($email)) {
        $error = "All fields except gender are required.";
    } elseif (!preg_match("/^\d{3}-\d{3}-\d{4}$/", $phone)) {
        $error = "Phone number must be formatted as ddd-ddd-dddd.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif (!preg_match("/^\d{2}-\d{2}-\d{4}$/", $dob)) {
        $error = "Date of birth must be formatted as mm-dd-yyyy.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if phone number already exists
        $sql = "SELECT * FROM Users WHERE PhoneNumber='$phone'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $error = "Phone number already in use.";
        } else {
            // Insert user data into database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Users (PhoneNumber, Password, FirstName, LastName, DateOfBirth, Gender, Email) VALUES ('$phone', '$hashedPassword', '$first_name', '$last_name', STR_TO_DATE('$dob', '%m-%d-%Y'), '$gender', '$email')";

            if ($conn->query($sql) === TRUE) {
                $success = "Registration successful. You can now log in.";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
    <h1>Register</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form id="registerForm" method="post" action="register.php">
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required pattern="\d{3}-\d{3}-\d{4}" placeholder="ddd-ddd-dddd"><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br>
        <label for="dob">Date of Birth:</label>
        <input type="text" id="dob" name="dob" required pattern="\d{2}-\d{2}-\d{4}" placeholder="mm-dd-yyyy"><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="gender">Gender:</label>
        <input type="radio" id="male" name="gender" value="M">
        <label for="male">Male</label>
        <input type="radio" id="female" name="gender" value="F">
        <label for="female">Female</label><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
