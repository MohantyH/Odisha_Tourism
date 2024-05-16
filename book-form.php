<?php
// Redirect if accessed directly without POST method
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: book.php');
    exit();
}

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Handling HTML entities to prevent XSS attacks
    return $data;
}

// Sanitize form inputs
$name = sanitize_input($_POST['name']);
$email = sanitize_input($_POST['email']);
$phone = sanitize_input($_POST['phone']);
$address = sanitize_input($_POST['address']);
$location = sanitize_input($_POST['location']);
$guests = sanitize_input($_POST['guests']);
$arrivals = sanitize_input($_POST['arrivals']);
$leaving = sanitize_input($_POST['leaving']);

// Check if any field is empty
if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($location) || empty($guests) || empty($arrivals) || empty($leaving)) {
    echo "All fields are required!";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SELECT statement
$SELECT = "SELECT email FROM register WHERE email = ?";
$stmt = $conn->prepare($SELECT);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$rnum = $stmt->num_rows;

if ($rnum > 0) {
    echo "Someone already registered using this email";
    exit();
}

$stmt->close();

// Prepare and execute INSERT statement
$INSERT = "INSERT INTO register (name, email, phone, address, location, guests, arrivals, leaving) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($INSERT);
$stmt->bind_param("ssssssss", $name, $email, $phone, $address, $location, $guests, $arrivals, $leaving);

if ($stmt->execute()) {
    // Redirect after successful insertion
    header('Location: success.php');
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
