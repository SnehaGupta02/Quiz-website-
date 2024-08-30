<?php
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

// Get data from the POST request
$studentName = isset($_POST['studentName']) ? $_POST['studentName'] : null;
$contact = isset($_POST['contact']) ? $_POST['contact'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

// Hash the password for security (you should use a stronger hashing method in production)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL statement to insert student information into the database
$stmt = $conn->prepare("INSERT INTO students_info (studentName, contact, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $studentName, $contact, $hashedPassword);

// Execute the statement
if ($stmt->execute()) {
    echo 'Student information submitted successfully!';
} else {
    echo 'Error: ' . $stmt->error;
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>
