<?php
session_start();

if (!isset($_SESSION['phone'])) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];

    // Validate user input
    if (empty($firstName) || empty($lastName) || empty($phone) || empty($gender) || empty($email) || empty($comment)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($comment) < 10) {
        $error = "Comment must be at least 10 characters.";
    } else {
        // Generate unique contact-id
        $contactId = uniqid('contact_');

        // Create a new XML document and store the contact information
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('Contacts');
        $dom->appendChild($root);

        $contact = $dom->createElement('Contact');
        $root->appendChild($contact);

        $contact->appendChild($dom->createElement('ContactId', $contactId));
        $contact->appendChild($dom->createElement('PhoneNumber', $phone));
        $contact->appendChild($dom->createElement('FirstName', $firstName));
        $contact->appendChild($dom->createElement('LastName', $lastName));
        $contact->appendChild($dom->createElement('Gender', $gender));
        $contact->appendChild($dom->createElement('Email', $email));
        $contact->appendChild($dom->createElement('Comment', $comment));

        $xmlFilePath = 'contacts.xml';

        if (file_exists($xmlFilePath)) {
            $existingXml = new DOMDocument();
            $existingXml->load($xmlFilePath);
            $rootNode = $existingXml->getElementsByTagName('Contacts')->item(0);
            $rootNode->appendChild($existingXml->importNode($contact, true));
            $existingXml->save($xmlFilePath);
        } else {
            $dom->save($xmlFilePath);
        }

        $success = "Your comment has been submitted successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Deals - Flights</title>
    <link rel="stylesheet" href="style.css">
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
            <h1>Contact Us</h1>
            <?php if (isset($error)): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p style="color:green;"><?php echo $success; ?></p>
            <?php endif; ?>
            <form id="contactForm" method="post" action="contact.php">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required><br>

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required><br>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required><br>

                <label>Gender:</label>
                <input type="radio" id="male" name="gender" value="male">
                <label for="male">Male</label>
                <input type="radio" id="female" name="gender" value="female">
                <label for="female">Female</label><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="comment">Comment:</label>
                <textarea id="comment" name="comment" required></textarea><br>

                <input type="submit" value="Submit">
            </form>
        </main>
    </div>

    <footer>
        <p>SRAVYA MEGHANA MITTA-smm230008</p>
        <p>ABHINAV SANTHOSH-axs230311</p>
        <p>CS 6314.0U1 - Web Programming Languages - Su24</p>
    </footer>

    <script src="script.js" defer></script>
    <script src="contact.js" defer></script>
</body>

</html>
