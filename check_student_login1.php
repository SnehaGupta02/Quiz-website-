<?php
// Start the session
session_start();

// File path to store student information
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$database = "teachers_info"; // Change this to your MySQL database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare SQL statement to fetch student data
$stmt = $conn->prepare("SELECT * FROM students_info WHERE studentName = ?");
$stmt->bind_param("s", $username);

// Execute SQL statement
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Check if the result contains any rows
if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $row['password'])) {
        // Password is correct, set the username in the session
        $_SESSION['studentName'] = $username;
        
        // Send success response
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
