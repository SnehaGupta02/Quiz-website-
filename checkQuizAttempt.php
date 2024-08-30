<?php
session_start();

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

// Get the student's username from the session (replace with your session logic)
$studentName = $_SESSION['studentName']; // Replace with your actual session variable

// Get the quiz number from the request (assuming it's passed via GET or POST)
$quizNumber = $_GET['quizNumber']; // Change this based on your implementation

// Check if the student has already attempted the quiz
$stmt = $conn->prepare("SELECT * FROM quiz_attempts WHERE student_name = ? AND quiz_number = ?");
$stmt->bind_param("si", $studentName, $quizNumber);
$stmt->execute();
$result = $stmt->get_result();

// Return true if the student has already attempted the quiz, false otherwise
if ($result->num_rows > 0) {
    echo json_encode(array("attempted" => true));
} else {
    echo json_encode(array("attempted" => false));
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>
