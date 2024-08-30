<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teachers_info";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$teacherName = $_POST['teacherName'];
$subject = $_POST['subject'];
$contact = $_POST['contact'];
$password = $_POST['password'];

// Hash the password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if the username already exists in the database
$stmt = $conn->prepare("SELECT * FROM teachers_info WHERE teacherName = ?");
$stmt->bind_param("s", $teacherName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  // Username already exists, update the password
  $updateStmt = $conn->prepare("UPDATE teachers_info SET password = ? WHERE teacherName = ?");
  $updateStmt->bind_param("ss", $hashedPassword, $teacherName);
  
  if ($updateStmt->execute()) {
    echo "Password updated successfully!";
  } else {
    echo "Error updating password: " . $conn->error;
  }
} else {
  // Username doesn't exist, insert a new record
  $insertStmt = $conn->prepare("INSERT INTO teachers_info (teacherName, subject, contact, password) VALUES (?, ?, ?, ?)");
  $insertStmt->bind_param("ssss", $teacherName, $subject, $contact, $hashedPassword);
  
  if ($insertStmt->execute()) {
    echo "Teacher information submitted successfully!";
  } else {
    echo "Error inserting teacher information: " . $conn->error;
  }
}

$stmt->close();
$conn->close();
?>
