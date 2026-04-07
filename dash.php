<?php
session_start();
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


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Studentslog.php"); // Redirect to login if not logged in
    exit();
}

// Fetch and display profile picture
$userId = $_SESSION['user_id'];
$profilePic = "default-profile.png"; // Default profile picture

// Fetch user's current profile picture if available
$query = "SELECT profile_pic FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($dbProfilePic);
if ($stmt->fetch() && !empty($dbProfilePic)) {
    $profilePic = "uploads/" . $dbProfilePic;
}
$stmt->close();

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $uploadDir = "uploads/";
    $fileName = basename($_FILES["profile_pic"]["name"]);
    $targetFile = $uploadDir . $userId . "_" . $fileName;

    // Move file to uploads directory
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
        $query = "UPDATE students SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $fileName, $userId);
        $stmt->execute();
        $stmt->close();

        // Refresh to show the new profile picture
        header("Location: dashboard.php");
        exit();
    }
}

// Fetch and display marks based on selected semester
$marks = [];
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["semester"])) {
    $semester = $_GET["semester"];
    $query = "SELECT course_name, marks FROM marks WHERE student_id = ? AND semester = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $userId, $semester);
    $stmt->execute();
    $stmt->bind_result($courseName, $mark);
    while ($stmt->fetch()) {
        $marks[] = ["course" => $courseName, "marks" => $mark];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include existing HTML head here -->
</head>
<body>
    <!-- Navbar code here -->

    <div class="container mt-5">
        <!-- Profile Information Section -->
        <div class="card profile-card mb-4">
            <div class="card-header">Profile Information</div>
            <div class="card-body text-center">
                <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" class="profile-pic mb-3">
                <h4 class="mt-4"><?= $_SESSION['user_name'] ?></h4>
                <p><?= $_SESSION['user_email'] ?></p>

                <!-- Profile Upload Form -->
                <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <input type="file" name="profile_pic" class="form-control mb-3" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-upload">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- View and Download Marks Section -->
        <div class="card view-marks-card">
            <div class="card-header">View and Download Marks</div>
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <label for="semester" class="form-label">Select Semester:</label>
                    <div class="input-group">
                        <select name="semester" id="semester" class="form-select" required>
                            <option value="">Choose...</option>
                            <option value="Semester 1">Semester 1</option>
                            <option value="Semester 2">Semester 2</option>
                        </select>
                        <button type="submit" class="btn btn-secondary btn-filter">Filter</button>
                    </div>
                </form>

                <!-- Marks Table -->
                <?php if (!empty($marks)): ?>
                <table class="table table-bordered table-hover table-striped text-center" id="marksTable">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($marks as $mark): ?>
                        <tr>
                            <td><?= htmlspecialchars($mark["course"]) ?></td>
                            <td><?= htmlspecialchars($mark["marks"]) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No marks found for the selected semester.</p>
                <?php endif; ?>

                <button class="btn btn-download" onclick="downloadTable()">Download Results</button>
            </div>
        </div>
    </div>

    <script>
        function downloadTable() {
            const table = document.getElementById("marksTable").outerHTML;
            const downloadLink = document.createElement("a");
            downloadLink.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(table);
            downloadLink.download = "Marks_Report.xls";
            downloadLink.click();
        }
    </script>

    <!-- Footer here -->

</body>
</html>
