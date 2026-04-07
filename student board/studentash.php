<?php
// Start session to store student_id
session_start();

// Include database connection
include 'config.php';

// Check if student is logged in, if not redirect to login page
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch student data (profile picture and marks)
$student_id = $_SESSION['student_id'];

// Fetch profile picture
$result = $conn->query("SELECT profile_picture FROM students WHERE id = $student_id");
$row = $result->fetch_assoc();
$profile_picture = $row['profile_picture'] ? "uploads/" . $row['profile_picture'] : "path/to/default/profile.jpg";

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    if (getimagesize($_FILES["profile_picture"]["tmp_name"]) === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (5MB limit)
    if ($_FILES["profile_picture"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain file formats (e.g., JPG, PNG, JPEG)
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update the student's profile picture in the database
            $sql = "UPDATE students SET profile_picture = '" . basename($_FILES["profile_picture"]["name"]) . "' WHERE id = $student_id";
            if ($conn->query($sql) === TRUE) {
                header("Location: student_dashboard.php"); // Redirect to refresh the page
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Fetch marks for the student
$marks_result = $conn->query("SELECT subject, marks FROM marks WHERE student_id = $student_id");

// Handle download performance
if (isset($_GET['download']) && $_GET['download'] == 'true') {
    // Prepare CSV for download
    $filename = "performance_report_{$student_id}.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Subject', 'Marks']); // Column headers

    while ($row = $marks_result->fetch_assoc()) {
        fputcsv($output, $row); // Write each row of data
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Welcome to Your Dashboard</h2>
    <hr>

    <!-- Profile Picture Upload Section -->
    <div class="row mb-4">
        <div class="col-md-4 text-center">
            <!-- Display Profile Picture -->
            <img id="profilePic" src="<?= $profile_picture ?>" class="rounded-circle" alt="Profile Picture" width="150" height="150">
            <form id="uploadForm" action="student_dashboard.php" method="post" enctype="multipart/form-data">
                <input type="file" name="profile_picture" class="form-control-file mt-2" required>
                <button type="submit" class="btn btn-primary mt-2">Upload Profile Picture</button>
            </form>
        </div>
    </div>

    <!-- View Marks Section -->
    <div class="card mb-4">
        <div class="card-header">Your Marks</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display student marks
                    while ($row = $marks_result->fetch_assoc()) {
                        echo "<tr><td>{$row['subject']}</td><td>{$row['marks']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Download Performance Section -->
    <div class="text-center">
        <a href="student_dashboard.php?download=true" class="btn btn-success">Download Overall Performance</a>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
