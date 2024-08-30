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

// Query to fetch teacher information along with quiz counts
$sql = "SELECT t.teacherName, t.subject, t.contact, COUNT(DISTINCT q.quiz_number) AS quizCount 
        FROM teachers_info t 
        LEFT JOIN quizzes q ON t.teacherName = q.teacher_name 
        GROUP BY t.teacherName";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Array to store teacher information
    $teachers = array();

    // Fetch each row of the result
    while ($row = $result->fetch_assoc()) {
        // Add teacher information to the array
        $teacher = array(
            'teacherName' => $row['teacherName'],
            'subject' => $row['subject'],
            'contact' => $row['contact'],
            'quizCount' => $row['quizCount'] // Include the quiz count in the result
        );
        // Add the teacher to the array of teachers
        $teachers[] = $teacher;
    }

    // Return the array of teachers as JSON data
    echo json_encode($teachers);
} else {
    // If no teachers found, return an empty array as JSON data
    echo json_encode(array());
}

// Close database connection
$conn->close();
?>
