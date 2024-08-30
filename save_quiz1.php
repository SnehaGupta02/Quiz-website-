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

// Get the maximum quiz number for the current teacher
$teacherName = $_SESSION['username'];
$maxQuizNumberQuery = "SELECT MAX(quiz_number) AS max_quiz_number FROM quizzes WHERE teacher_name = ?";
$stmt = $conn->prepare($maxQuizNumberQuery);
$stmt->bind_param("s", $teacherName);
$stmt->execute();
$maxQuizNumberResult = $stmt->get_result();
$maxQuizNumberRow = $maxQuizNumberResult->fetch_assoc();
$maxQuizNumber = $maxQuizNumberRow['max_quiz_number'];
$stmt->close();

// Increment the maximum quiz number to get the next quiz number
$nextQuizNumber = $maxQuizNumber + 1;

// Get quiz data from the POST request
$quizData = json_decode($_POST['quizData'], true);

// Prepare SQL statement to insert quiz data into the database
$stmt = $conn->prepare("INSERT INTO quizzes (teacher_name, question, marks, options, correct_option, image_data, quiz_number) VALUES (?, ?, ?, ?, ?, ?, ?)");

// Bind parameters and execute the statement for each question
foreach ($quizData['quizData'] as $index => $question) {
    $questionText = $question['question'];
    $marks = $question['marks'];
    $options = json_encode($question['options']); // Convert options array to JSON string
    $correctOption = $question['correctOption'];
    $imageData = null; // Initialize image data to null
    
    // Check if image file is uploaded
    if (!empty($_FILES['pictures']['tmp_name'][$index])) {
        // Read image data from the uploaded file
        $imageData = file_get_contents($_FILES['pictures']['tmp_name'][$index]);
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("ssissbi", $teacherName, $questionText, $marks, $options, $correctOption, $imageData, $nextQuizNumber);
    $stmt->send_long_data(5, $imageData); // Send BLOB data separately
    $stmt->execute();
}

// Close statement and database connection
$stmt->close();
$conn->close();

echo "Quiz data stored successfully!";
?>
