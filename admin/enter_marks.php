<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myschool";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $subject = $_POST['subject'];
    $marks = $_POST['marks'];

    // Insert marks data into the 'marks' table
    $sql = "INSERT INTO marks (student_id, subject, marks) VALUES ('$student_id', '$subject', '$marks')";

    if ($conn->query($sql) === TRUE) {
        // After successfully saving the marks in the database:
header("Location: admin.php?success=1");
exit;

    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
