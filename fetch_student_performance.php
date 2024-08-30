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

// Fetch student performance data from the database
$studentName = $_SESSION['studentName']; // Assuming student name is stored in the session
$sql = "SELECT quiz_number, total_marks, marks_obtained, attempt_date ,teacher_name FROM quiz_attempts WHERE student_name = '$studentName'";
$result = $conn->query($sql);

$performanceData = array();
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $performanceData[] = $row;
    }
} else {
    echo "No performance data found for student: $studentName";
}

// Close database connection
$conn->close();

// Output performance data as JSON
header('Content-Type: application/json');
echo json_encode($performanceData);
?>
