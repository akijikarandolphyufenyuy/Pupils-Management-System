<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myschool";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $class = $_POST['class'];

    // Insert student data into the database
    $sql = "INSERT INTO students (full_name, dob, gender, class) VALUES ('$full_name', '$dob', '$gender', '$class')";

    if ($conn->query($sql) === TRUE) {
        // Redirect with success flag
        header("Location: admin.php?success=1");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
