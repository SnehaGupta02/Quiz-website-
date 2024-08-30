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

// Fetch student evaluation data from the database for the current teacher
$teacherName = $_SESSION['username']; // Assuming teacher name is stored in the session
$sql = "SELECT student_name, quiz_number, total_marks, marks_obtained, attempt_date FROM quiz_attempts WHERE teacher_name = '$teacherName'";
$result = $conn->query($sql);

$evaluationData = array();
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $evaluationData[] = $row;
    }
} else {
    echo "No evaluation data found for teacher: $teacherName";
}

// Close database connection
$conn->close();

// Output evaluation data as JSON
header('Content-Type: application/json');
echo json_encode($evaluationData);
?>
