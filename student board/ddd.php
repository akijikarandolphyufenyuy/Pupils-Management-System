<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Admin Management Page</h2>
    <hr>

    <!-- Section 1: Student Applications -->
    <div id="studentApplications" class="mb-4">
        <h3>Student Applications</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Address</th>
                    <th>Parent/Guardian</th>
                    <th>Previous School</th>
                    <th>Medical Conditions</th>
                    <th>Documents</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch applications (read-only)
                $result = $conn->query("SELECT * FROM applications WHERE status = 'Pending'");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['full_name']}</td>
                            <td>{$row['dob']}</td>
                            <td>{$row['gender']}</td>
                            <td>{$row['address']}</td>
                            <td>{$row['guardian_name']} ({$row['relationship']})</td>
                            <td>{$row['previous_school']}</td>
                            <td>{$row['medical_conditions']}</td>
                            <td><a href='uploads/{$row['birth_certificate']}'>Birth Certificate</a>, <a href='uploads/{$row['proof_of_address']}'>Proof of Address</a></td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Section 2: Student Registration -->
    <div id="studentRegistration" class="mb-4">
        <h3>Register Student</h3>
        <form action="register_student.php" method="post">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" class="form-control" name="dob" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="class">Class:</label>
                <input type="text" class="form-control" name="class" required>
            </div>
            <button type="submit" class="btn btn-success">Register Student</button>
        </form>
    </div>

    <!-- Section 3: Enter Student Marks -->
    <div id="gradeEntry" class="mb-4">
        <h3>Enter Student Marks</h3>
        <form action="enter_marks.php" method="post">
            <div class="form-group">
                <label for="student_id">Select Student:</label>
                <select class="form-control" name="student_id" required>
                    <option value="">Select Student</option>
                    <?php
                    // Populate dropdown with registered students
                    $result = $conn->query("SELECT id, full_name FROM students");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['full_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <select class="form-control" name="subject" required>
                    <option value="maths">Maths</option>
                    <option value="english">English</option>
                    <option value="french">French</option>
                    <option value="computer">Computer</option>
                    <option value="economics">Economics</option>
                    <option value="physics">Physics</option>
                </select>
            </div>
            <div class="form-group">
                <label for="marks">Marks:</label>
                <input type="number" class="form-control" name="marks" min="0" max="100" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Marks</button>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
