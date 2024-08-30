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

// Retrieve teacher name and quiz number from query parameters
$teacherName = $_GET['teacher'];
$quizNumber = $_GET['quiz'];

// Prepare SQL query to fetch quiz data including image data
$sql = "SELECT * FROM quizzes WHERE teacher_name = ? AND quiz_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $teacherName, $quizNumber);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Array to store quiz data
$quizData = array();

// Fetch quiz data and store it in an array
while ($row = $result->fetch_assoc()) {
    // Convert image data to Base64 encoding
    $imageData = base64_encode($row['image_data']);
    // Build the quiz data including the image data
    $question = array(
        'question' => $row['question'],
        'marks' => $row['marks'],
        'options' => json_decode($row['options'], true), // Decode JSON string to array
        'correctOption' => $row['correct_option'],
        'imageData' => $imageData // Add Base64-encoded image data to the quiz data
    );
    $quizData[] = $question;
}

// Close statement and database connection
$stmt->close();
$conn->close();

// Return quiz data as JSON
echo json_encode($quizData);
?>
