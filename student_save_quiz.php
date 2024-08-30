<?php
// Start the session
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

// Retrieve quiz attempt data from POST parameters
$quizNumber = $_POST['quizNumber'];
$totalMarks = $_POST['totalMarks'];
$totalMarksObtained = $_POST['totalMarksObtained'];
$teacherName = $_POST['teacherName'];

// Retrieve student name from session
if(isset($_SESSION['studentName'])) {
    $studentName = $_SESSION['studentName'];
} else {
    // If the student name is not set in the session, return an error message
    echo json_encode(['error' => "Student name not found in session."]);
    exit();
}

// Check if the attempt already exists
$sql = "SELECT * FROM quiz_attempts WHERE quiz_number = ? AND teacher_name = ? AND student_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $quizNumber, $teacherName, $studentName);
$stmt->execute();
$result = $stmt->get_result();

// If no attempt exists, insert a new attempt
if ($result->num_rows === 0) {
    $insertSql = "INSERT INTO quiz_attempts (quiz_number, teacher_name, student_name, total_marks, marks_obtained) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    // Fix the data types in the bind_param method
    $insertStmt->bind_param("isssi", $quizNumber, $teacherName, $studentName, $totalMarks, $totalMarksObtained);
    $insertStmt->execute();
    $insertStmt->close();
    echo json_encode(['message' => "Quiz attempt recorded successfully!"]);
} else {
    echo json_encode(['error' => "You can not  attempt this quiz."]);
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>
