<?php
session_start(); // Start the session

// Database connection parameters
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$database = "teachers_info"; // Change this to your MySQL database name

// Establish connection to MySQL database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username and password from the POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare SQL statement to retrieve teacher data by username
$stmt = $conn->prepare("SELECT * FROM teachers_info WHERE teacherName = ?");
$stmt->bind_param("s", $username);

// Execute SQL statement
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Check if there is a row with the provided username
if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $row['password'])) {
        // Password is correct, set the session variable
        $_SESSION['username'] = $username;
        $isValidLogin = true;
    } else {
        // Password is incorrect, send failure response
        $isValidLogin = false;
    }
} else {
    // No row with the provided username, send failure response
    $isValidLogin = false;
}

// Send response as JSON
header('Content-Type: application/json');
echo json_encode(['isValidLogin' => $isValidLogin]);

// Close database connection
$stmt->close();
$conn->close();
?>