<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myschool";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$submissionMessage = "";
$submissionSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        !empty($_POST['studentName']) && !empty($_POST['dob']) && !empty($_POST['gender']) && !empty($_POST['address']) &&
        !empty($_POST['guardianName']) && !empty($_POST['relation']) && !empty($_POST['guardianPhone']) &&
        !empty($_POST['guardianEmail']) && !empty($_POST['previousSchool']) && !empty($_POST['gradeCompleted']) &&
        !empty($_POST['medicalConditions']) && !empty($_POST['emergencyContact']) && !empty($_POST['gradeSelection']) &&
        !empty($_POST['startDate']) && !empty($_POST['paymentStatus']) &&
        isset($_FILES['birthCertificate']) && $_FILES['birthCertificate']['error'] == 0 &&
        isset($_FILES['proofOfAddress']) && $_FILES['proofOfAddress']['error'] == 0
    ) {
        $studentName = $_POST['studentName'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $guardianName = $_POST['guardianName'];
        $relation = $_POST['relation'];
        $guardianPhone = $_POST['guardianPhone'];
        $guardianEmail = $_POST['guardianEmail'];
        $previousSchool = $_POST['previousSchool'];
        $gradeCompleted = $_POST['gradeCompleted'];
        $medicalConditions = $_POST['medicalConditions'];
        $emergencyContact = $_POST['emergencyContact'];
        $gradeSelection = $_POST['gradeSelection'];
        $startDate = $_POST['startDate'];
        $paymentStatus = $_POST['paymentStatus'];

        $uploadFolder = "uploads/";
        $birthCertificate = $_FILES['birthCertificate']['name'];
        $proofOfAddress = $_FILES['proofOfAddress']['name'];
        move_uploaded_file($_FILES['birthCertificate']['tmp_name'], $uploadFolder . $birthCertificate);
        move_uploaded_file($_FILES['proofOfAddress']['tmp_name'], $uploadFolder . $proofOfAddress);

        $stmt = $conn->prepare("INSERT INTO admissions (student_name, dob, gender, address, guardian_name, relation, guardian_phone, guardian_email, previous_school, grade_completed, medical_conditions, emergency_contact, birth_certificate, proof_of_address, grade_selection, start_date, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssssssss", $studentName, $dob, $gender, $address, $guardianName, $relation, $guardianPhone, $guardianEmail, $previousSchool, $gradeCompleted, $medicalConditions, $emergencyContact, $birthCertificate, $proofOfAddress, $gradeSelection, $startDate, $paymentStatus);

        if ($stmt->execute()) {
            $submissionMessage = "Application submitted successfully! We will contact you shortly.";
            $submissionSuccess = true;
        } else {
            $submissionMessage = "Error submitting application: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $submissionMessage = "Please fill in all required fields and upload all documents.";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions — EduManage Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0a2342;
            --accent: #c9a84c;
            --accent-light: #f0d080;
            --light-bg: #f4f7fb;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--light-bg); color: #1a1a2e; }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, #1a3a6b 100%);
            padding: 14px 0;
            box-shadow: 0 4px 20px rgba(10,35,66,.35);
        }
        .navbar-brand { color: #fff !important; font-weight: 800; font-size: 1.1rem; }
        .nav-link { color: rgba(255,255,255,.85) !important; font-weight: 500; padding: 8px 16px !important; border-radius: 6px; transition: all .25s; }
        .nav-link:hover { color: #fff !important; background: rgba(201,168,76,.25); }

        /* Page header */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a3a6b 100%);
            padding: 60px 0 40px;
            text-align: center;
            color: #fff;
        }
        .page-header h1 { font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; }
        .page-header p { color: rgba(255,255,255,.7); font-size: 1rem; }
        .breadcrumb-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.1);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: .82rem;
            color: rgba(255,255,255,.7);
            margin-bottom: 20px;
        }
        .breadcrumb-custom a { color: var(--accent); text-decoration: none; }

        /* Form card */
        .form-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(10,35,66,.1);
            overflow: hidden;
            margin-bottom: 28px;
        }
        .form-card-header {
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            padding: 20px 28px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .form-card-header .header-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(201,168,76,.2);
            border: 1px solid rgba(201,168,76,.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--accent);
            flex-shrink: 0;
        }
        .form-card-header h3 { color: #fff; font-size: 1.05rem; font-weight: 700; margin: 0; }
        .form-card-header p { color: rgba(255,255,255,.6); font-size: .82rem; margin: 0; }
        .form-card-body { padding: 28px; }

        /* Form controls */
        .form-label { font-size: .85rem; font-weight: 600; color: var(--primary); margin-bottom: 6px; }
        .form-control, .form-select {
            border: 2px solid #e8ecf4;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: .92rem;
            font-family: 'Inter', sans-serif;
            color: var(--primary);
            background: #f8faff;
            transition: border-color .25s, box-shadow .25s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(201,168,76,.15);
            background: #fff;
            outline: none;
        }
        .form-control::placeholder { color: #b0bac8; }

        /* Submit button */
        .btn-submit {
            background: linear-gradient(135deg, var(--accent), #e8b84b);
            color: var(--primary);
            font-weight: 700;
            padding: 14px 40px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: transform .25s, box-shadow .25s;
            box-shadow: 0 6px 20px rgba(201,168,76,.35);
            font-family: 'Inter', sans-serif;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(201,168,76,.5); }

        /* Success/Error popup */
        .toast-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .toast-overlay.show { display: flex; animation: fadeIn .3s ease; }
        .toast-box {
            background: #fff;
            border-radius: 20px;
            padding: 40px 36px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0,0,0,.3);
            animation: scaleIn .35s ease;
        }
        .toast-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .toast-icon.success { background: #e8f5e9; color: #2e7d32; }
        .toast-icon.error { background: #fff0f0; color: #c62828; }
        .toast-box h4 { font-size: 1.3rem; font-weight: 800; color: var(--primary); margin-bottom: 8px; }
        .toast-box p { color: #6b7a99; font-size: .92rem; margin-bottom: 24px; }
        .btn-toast {
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #060f1e, var(--primary));
            color: rgba(255,255,255,.5);
            text-align: center;
            padding: 24px;
            font-size: .85rem;
            margin-top: 20px;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { transform: scale(.85); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="home.html"><i class="bi bi-mortarboard-fill me-2" style="color:var(--accent)"></i>EduManage Pro</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="home.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="Studentslog.php">Student Login</a></li>
                <li class="nav-item"><a class="nav-link" href="admin/admin.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="page-header">
    <div class="breadcrumb-custom">
        <a href="home.html">Home</a> <i class="bi bi-chevron-right"></i> Admissions
    </div>
    <h1><i class="bi bi-pencil-square me-2" style="color:var(--accent)"></i>Admission Application</h1>
    <p>Complete the form below to apply. Ensure all information is accurate and documents are ready.</p>
</div>

<!-- Form -->
<div class="container py-5">
    <form action="" method="post" enctype="multipart/form-data">

        <!-- Student Info -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon"><i class="bi bi-person-fill"></i></div>
                <div>
                    <h3>Student Personal Information</h3>
                    <p>Basic details about the student</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="studentName" placeholder="Student's full name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="dob" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" name="gender" required>
                            <option value="" disabled selected>Select gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Residential Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="address" placeholder="Full address" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guardian Info -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <h3>Parent / Guardian Information</h3>
                    <p>Contact details for the student's guardian</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Guardian Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="guardianName" placeholder="Guardian's full name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Relationship to Student <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="relation" placeholder="e.g. Father, Mother, Uncle" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="guardianPhone" placeholder="Contact number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="guardianEmail" placeholder="Guardian's email" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- School History -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon"><i class="bi bi-building-fill"></i></div>
                <div>
                    <h3>School History & Health</h3>
                    <p>Previous academic and medical information</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Previous School <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="previousSchool" placeholder="Name of previous school attended" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Grade Completed <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="gradeCompleted" placeholder="e.g. Grade 3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Medical Conditions <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="medicalConditions" placeholder="None, or describe condition" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="emergencyContact" placeholder="Emergency phone number" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Details -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon"><i class="bi bi-calendar-check-fill"></i></div>
                <div>
                    <h3>Enrollment Details</h3>
                    <p>Grade selection and payment information</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Grade Selection <span class="text-danger">*</span></label>
                        <select class="form-select" name="gradeSelection" required>
                            <option value="" disabled selected>Select grade</option>
                            <option>Grade 1</option>
                            <option>Grade 2</option>
                            <option>Grade 3</option>
                            <option>Grade 4</option>
                            <option>Grade 5</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="startDate" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="paymentStatus" required>
                            <option value="" disabled selected>Select status</option>
                            <option>Paid</option>
                            <option>Pending</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="form-card">
            <div class="form-card-header">
                <div class="header-icon"><i class="bi bi-file-earmark-arrow-up-fill"></i></div>
                <div>
                    <h3>Upload Documents</h3>
                    <p>Required supporting documents (PDF, JPG, PNG)</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Birth Certificate <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="birthCertificate" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Proof of Address <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="proofOfAddress" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center pb-4">
            <button type="submit" class="btn-submit">
                <i class="bi bi-send-fill me-2"></i>Submit Application
            </button>
        </div>
    </form>
</div>

<!-- Toast Popup -->
<div class="toast-overlay <?= $submissionMessage ? 'show' : '' ?>" id="toastOverlay">
    <div class="toast-box">
        <div class="toast-icon <?= $submissionSuccess ? 'success' : 'error' ?>">
            <i class="bi bi-<?= $submissionSuccess ? 'check-circle-fill' : 'x-circle-fill' ?>"></i>
        </div>
        <h4><?= $submissionSuccess ? 'Application Submitted!' : 'Submission Failed' ?></h4>
        <p><?= htmlspecialchars($submissionMessage) ?></p>
        <button class="btn-toast" onclick="document.getElementById('toastOverlay').classList.remove('show')">
            <?= $submissionSuccess ? 'Great, Thanks!' : 'Try Again' ?>
        </button>
    </div>
</div>

<footer>&copy; 2024 EduManage Pro — Primary School Management System</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
