<?php
session_start();

$host = 'localhost'; $user = 'root'; $pass = ''; $dbname = 'myschool';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$uploadsDir = 'uploads/';
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture']) && isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $image = $_FILES['profile_picture'];
    $imagePath = $uploadsDir . basename($image['name']);
    $imageType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    if (in_array($imageType, ['jpg', 'jpeg', 'png'])) {
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            $stmt = $conn->prepare("UPDATE students SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param('si', $imagePath, $student_id);
            $stmt->execute() ? $uploadMessage = "Profile picture updated!" : $uploadMessage = "Database update failed.";
            $stmt->close();
        } else { $uploadMessage = "Failed to upload image."; }
    } else { $uploadMessage = "Only JPG, JPEG, and PNG files are allowed."; }
}

if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $stmt = $conn->prepare("SELECT full_name, class, COALESCE(profile_picture, '') AS profile_picture FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $student_name = $student['full_name'];
        $student_class = $student['class'];
        $profile_picture = !empty($student['profile_picture']) ? $student['profile_picture'] : '';
    } else {
        $student_name = "Student"; $student_class = "N/A"; $profile_picture = '';
    }
    $stmt->close();
} else {
    header("Location: ../Studentslog.php"); exit();
}

// Fetch marks
$marks_stmt = $conn->prepare("SELECT subject, marks FROM marks WHERE student_id = ? ORDER BY subject");
$marks_stmt->bind_param("i", $student_id);
$marks_stmt->execute();
$marks_result = $marks_stmt->get_result();
$marks_data = [];
$total_marks = 0; $marks_count = 0;
while ($row = $marks_result->fetch_assoc()) {
    $marks_data[] = $row;
    $total_marks += $row['marks'];
    $marks_count++;
}
$marks_stmt->close();
$average = $marks_count > 0 ? round($total_marks / $marks_count, 1) : 0;
$grade = $average >= 80 ? 'A' : ($average >= 70 ? 'B' : ($average >= 60 ? 'C' : ($average >= 50 ? 'D' : 'F')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard — EduManage Pro</title>
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
        body { font-family: 'Inter', sans-serif; background: var(--light-bg); color: #1a1a2e; min-height: 100vh; }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, #1a3a6b 100%);
            padding: 14px 0;
            box-shadow: 0 4px 20px rgba(10,35,66,.35);
        }
        .navbar-brand { color: #fff !important; font-weight: 800; font-size: 1.1rem; }
        .nav-link { color: rgba(255,255,255,.8) !important; font-weight: 500; padding: 8px 16px !important; border-radius: 6px; transition: all .25s; }
        .nav-link:hover { color: #fff !important; background: rgba(201,168,76,.25); }
        .nav-link.logout { color: rgba(255,150,150,.85) !important; }
        .nav-link.logout:hover { background: rgba(255,100,100,.15); color: #ff8a80 !important; }

        /* Hero banner */
        .dash-hero {
            background: linear-gradient(135deg, var(--primary) 0%, #1a3a6b 60%, #2a5298 100%);
            padding: 40px 0 80px;
            position: relative;
            overflow: hidden;
        }
        .dash-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(201,168,76,.08);
        }
        .dash-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .hero-text { position: relative; z-index: 2; }
        .hero-text .greeting { font-size: .85rem; color: var(--accent); font-weight: 600; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; }
        .hero-text h1 { font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
        .hero-text p { color: rgba(255,255,255,.6); font-size: .92rem; }
        .hero-avatar {
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 3px solid var(--accent);
            object-fit: cover;
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
        }
        .hero-avatar-placeholder {
            width: 90px; height: 90px;
            border-radius: 50%;
            border: 3px solid var(--accent);
            background: linear-gradient(135deg, rgba(201,168,76,.3), rgba(201,168,76,.1));
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: var(--accent);
        }

        /* Floating stat cards */
        .stats-row {
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px 20px;
            box-shadow: 0 8px 32px rgba(10,35,66,.12);
            text-align: center;
            transition: transform .25s;
        }
        .stat-card:hover { transform: translateY(-4px); }
        .stat-card .icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        .icon-blue { background: linear-gradient(135deg, #1a3a6b, #2a5298); color: #fff; }
        .icon-gold { background: linear-gradient(135deg, var(--accent), var(--accent-light)); color: var(--primary); }
        .icon-green { background: linear-gradient(135deg, #1b5e20, #388e3c); color: #fff; }
        .stat-card .num { font-size: 1.6rem; font-weight: 800; color: var(--primary); }
        .stat-card .lbl { font-size: .78rem; color: #6b7a99; margin-top: 2px; }

        /* Cards */
        .content-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(10,35,66,.07);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .card-head {
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            padding: 18px 24px;
            display: flex; align-items: center; gap: 12px;
        }
        .card-head-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(201,168,76,.2);
            border: 1px solid rgba(201,168,76,.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: var(--accent);
        }
        .card-head h5 { color: #fff; font-size: .95rem; font-weight: 700; margin: 0; }
        .card-head p { color: rgba(255,255,255,.55); font-size: .78rem; margin: 0; }
        .card-body-custom { padding: 24px; }

        /* Profile upload */
        .upload-zone {
            border: 2px dashed #d0d8e8;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #f8faff;
            transition: border-color .25s;
        }
        .upload-zone:hover { border-color: var(--accent); }
        .upload-zone input[type="file"] { display: none; }
        .upload-label {
            cursor: pointer;
            color: #6b7a99;
            font-size: .88rem;
        }
        .upload-label i { font-size: 1.8rem; color: var(--accent); display: block; margin-bottom: 8px; }
        .btn-upload {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--primary);
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: .88rem;
            cursor: pointer;
            transition: transform .25s;
            font-family: 'Inter', sans-serif;
            margin-top: 12px;
        }
        .btn-upload:hover { transform: translateY(-2px); }

        /* Marks table */
        .table { font-size: .88rem; }
        .table thead th {
            background: var(--light-bg);
            color: var(--primary);
            font-weight: 700;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none;
            padding: 12px 16px;
        }
        .table tbody td { padding: 12px 16px; vertical-align: middle; border-color: #f0f4f8; }
        .table tbody tr:hover { background: #f8faff; }
        .marks-bar-wrap { display: flex; align-items: center; gap: 10px; }
        .marks-bar {
            flex: 1;
            height: 6px;
            background: #e8ecf4;
            border-radius: 3px;
            overflow: hidden;
        }
        .marks-bar-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent-light));
            transition: width 1s ease;
        }
        .marks-bar-fill.high { background: linear-gradient(90deg, #2e7d32, #66bb6a); }
        .marks-bar-fill.mid { background: linear-gradient(90deg, #f57f17, #ffca28); }
        .marks-bar-fill.low { background: linear-gradient(90deg, #c62828, #ef5350); }
        .grade-badge {
            display: inline-block;
            width: 32px; height: 32px;
            border-radius: 8px;
            font-weight: 800;
            font-size: .85rem;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .grade-A { background: #e8f5e9; color: #2e7d32; }
        .grade-B { background: #e3f2fd; color: #1565c0; }
        .grade-C { background: #fff8e1; color: #f57f17; }
        .grade-D { background: #fff3e0; color: #e65100; }
        .grade-F { background: #ffebee; color: #c62828; }

        /* Download button */
        .btn-download {
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .92rem;
            cursor: pointer;
            transition: transform .25s, box-shadow .25s;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-download:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(10,35,66,.3); color: #fff; }

        /* Alert */
        .alert-success-custom {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-left: 4px solid #2e7d32;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: .88rem;
            color: #2e7d32;
            margin-bottom: 16px;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #060f1e, var(--primary));
            color: rgba(255,255,255,.4);
            text-align: center;
            padding: 20px;
            font-size: .82rem;
            margin-top: 20px;
        }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .animate { animation: fadeUp .5s ease both; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-mortarboard-fill me-2" style="color:var(--accent)"></i>EduManage Pro</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item">
                    <span class="nav-link"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($student_name) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link logout" href="../Studentslog.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<div class="dash-hero">
    <div class="container">
        <div class="d-flex align-items-center gap-4 hero-text">
            <?php if ($profile_picture && file_exists($profile_picture)): ?>
                <img src="<?= htmlspecialchars($profile_picture) ?>" class="hero-avatar" alt="Profile">
            <?php else: ?>
                <div class="hero-avatar-placeholder"><i class="bi bi-person-fill"></i></div>
            <?php endif; ?>
            <div>
                <div class="greeting">Welcome back</div>
                <h1><?= htmlspecialchars($student_name) ?></h1>
                <p><i class="bi bi-bookmark-fill me-1" style="color:var(--accent)"></i>Class <?= htmlspecialchars($student_class) ?> &nbsp;·&nbsp; Student Dashboard</p>
            </div>
        </div>
    </div>
</div>

<!-- Floating Stats -->
<div class="container stats-row animate">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon icon-blue"><i class="bi bi-journal-text"></i></div>
                <div class="num"><?= $marks_count ?></div>
                <div class="lbl">Subjects Recorded</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon icon-gold"><i class="bi bi-bar-chart-fill"></i></div>
                <div class="num"><?= $average ?>%</div>
                <div class="lbl">Average Score</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon icon-green"><i class="bi bi-award-fill"></i></div>
                <div class="num"><?= $grade ?></div>
                <div class="lbl">Overall Grade</div>
            </div>
        </div>
    </div>
</div>

<!-- Content -->
<div class="container py-4">
    <div class="row g-4">

        <!-- Profile Upload -->
        <div class="col-lg-4">
            <div class="content-card animate">
                <div class="card-head">
                    <div class="card-head-icon"><i class="bi bi-person-fill"></i></div>
                    <div><h5>Profile Picture</h5><p>Update your photo</p></div>
                </div>
                <div class="card-body-custom">
                    <?php if (isset($uploadMessage)): ?>
                    <div class="alert-success-custom"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($uploadMessage) ?></div>
                    <?php endif; ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="upload-zone">
                            <label class="upload-label" for="fileInput">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                Click to select a photo<br>
                                <small>JPG, JPEG, PNG supported</small>
                            </label>
                            <input type="file" id="fileInput" name="profile_picture" accept=".jpg,.jpeg,.png" onchange="updateLabel(this)" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn-upload"><i class="bi bi-upload me-2"></i>Upload Photo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Marks -->
        <div class="col-lg-8">
            <div class="content-card animate">
                <div class="card-head">
                    <div class="card-head-icon"><i class="bi bi-bar-chart-fill"></i></div>
                    <div><h5>Academic Performance</h5><p>Your subject marks and grades</p></div>
                </div>
                <div class="card-body-custom">
                    <?php if (!empty($marks_data)): ?>
                    <div class="table-responsive">
                        <table class="table" id="marksTable">
                            <thead>
                                <tr><th>Subject</th><th>Marks</th><th>Performance</th><th>Grade</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($marks_data as $m):
                                $m_val = (int)$m['marks'];
                                $bar_class = $m_val >= 70 ? 'high' : ($m_val >= 50 ? 'mid' : 'low');
                                $g = $m_val >= 80 ? 'A' : ($m_val >= 70 ? 'B' : ($m_val >= 60 ? 'C' : ($m_val >= 50 ? 'D' : 'F')));
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars(ucfirst($m['subject'])) ?></strong></td>
                                <td><strong><?= $m_val ?>/100</strong></td>
                                <td>
                                    <div class="marks-bar-wrap">
                                        <div class="marks-bar">
                                            <div class="marks-bar-fill <?= $bar_class ?>" style="width:<?= $m_val ?>%"></div>
                                        </div>
                                        <small style="color:#6b7a99;min-width:32px"><?= $m_val ?>%</small>
                                    </div>
                                </td>
                                <td><span class="grade-badge grade-<?= $g ?>"><?= $g ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-3 pt-3" style="border-top:1px solid #f0f4f8">
                        <div>
                            <span style="font-size:.85rem;color:#6b7a99">Overall Average: </span>
                            <strong style="color:var(--primary);font-size:1.1rem"><?= $average ?>%</strong>
                            <span class="grade-badge grade-<?= $grade ?> ms-2"><?= $grade ?></span>
                        </div>
                        <a href="download_performance.php" class="btn-download">
                            <i class="bi bi-download"></i> Download Report
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x" style="font-size:3rem;color:#d0d8e8"></i>
                        <p style="color:#6b7a99;margin-top:12px">No marks have been recorded yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<footer>&copy; 2024 EduManage Pro — Primary School Management System</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function updateLabel(input) {
        const label = input.previousElementSibling;
        if (input.files.length > 0) {
            label.innerHTML = `<i class="bi bi-file-image-fill" style="color:var(--accent)"></i>${input.files[0].name}`;
        }
    }
    // Animate bars on load
    window.addEventListener('load', () => {
        document.querySelectorAll('.marks-bar-fill').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => bar.style.width = w, 300);
        });
    });
</script>
</body>
</html>
