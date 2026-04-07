<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Student Dashboard</h2>
    <div class="card">
        <div class="card-header bg-primary text-white">View and Download Marks</div>
        <div class="card-body">
            <form method="GET" class="mb-4">
                <label for="semester">Select Semester:</label>
                <select name="semester" id="semester" class="form-select" required>
                    <option value="">Choose...</option>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <!-- Add more semesters as needed -->
                </select>
                <button type="submit" class="btn btn-primary mt-2">Filter</button>
            </form>

            <?php
            require 'db.php';
            $semester = isset($_GET['semester']) ? $_GET['semester'] : '';
            
            if ($semester) {
                $stmt = $pdo->prepare("SELECT course, marks FROM marks WHERE student_id = :student_id AND semester = :semester");
                $stmt->execute(['student_id' => 1, 'semester' => $semester]);
                $marks = $stmt->fetchAll();
                
                if ($marks) {
                    echo '<table class="table table-striped" id="marksTable">';
                    echo '<thead><tr><th>Course</th><th>Marks</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($marks as $mark) {
                        echo "<tr><td>{$mark['course']}</td><td>{$mark['marks']}</td></tr>";
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p class="text-danger">No records found for this semester.</p>';
                }
            }
            ?>
            <button class="btn btn-success mt-3" onclick="downloadTable()">Download Results</button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">Calculate Grades</div>
        <div class="card-body">
            <button class="btn btn-secondary" onclick="calculateGrade()">Calculate Semester GPA</button>
            <p id="gradeResult" class="mt-3"></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function downloadTable() {
        const table = document.getElementById("marksTable").outerHTML;
        const downloadLink = document.createElement("a");
        downloadLink.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(table);
        downloadLink.download = "Marks_Report.xls";
        downloadLink.click();
    }

    function calculateGrade() {
        let marks = document.querySelectorAll("#marksTable tbody tr td:nth-child(2)");
        let total = 0, count = 0;
        
        marks.forEach(mark => {
            total += parseFloat(mark.textContent);
            count++;
        });

        const gpa = (total / count).toFixed(2);
        document.getElementById("gradeResult").textContent = `Your GPA for the semester is: ${gpa}`;
    }
</script>
</body>
</html>
