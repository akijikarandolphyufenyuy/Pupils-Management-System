<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'myschool';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_marks.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Subject', 'Marks']);

    $marks_stmt = $conn->prepare("SELECT subject, marks FROM marks WHERE student_id = ? ORDER BY subject");
    $marks_stmt->bind_param("i", $student_id);
    $marks_stmt->execute();
    $marks_result = $marks_stmt->get_result();

    if ($marks_result->num_rows > 0) {
        while ($row = $marks_result->fetch_assoc()) {
            fputcsv($output, [$row['subject'], $row['marks']]);
        }
    } else {
        fputcsv($output, ["No marks found", ""]);
    }

    fclose($output);
    $marks_stmt->close();
    exit();
} else {
    echo "Student ID not found in session.";
}
?>
