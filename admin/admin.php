<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — EduManage Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0a2342;
            --accent: #c9a84c;
            --accent-light: #f0d080;
            --sidebar-w: 260px;
            --light-bg: #f4f7fb;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--light-bg); color: #1a1a2e; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(180deg, var(--primary) 0%, #0d2d52 100%);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,.2);
            transition: transform .3s;
        }
        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand .brand-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: var(--primary);
            margin-bottom: 10px;
        }
        .sidebar-brand h2 { color: #fff; font-size: 1.1rem; font-weight: 800; margin: 0; }
        .sidebar-brand span { color: var(--accent); }
        .sidebar-brand p { color: rgba(255,255,255,.45); font-size: .75rem; margin: 0; }
        .sidebar-nav { flex: 1; padding: 20px 12px; overflow-y: auto; }
        .nav-section-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            padding: 0 12px;
            margin: 16px 0 8px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            transition: all .25s;
            margin-bottom: 2px;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        .sidebar-link i { font-size: 1.05rem; width: 20px; text-align: center; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(201,168,76,.15);
            color: #fff;
        }
        .sidebar-link.active { color: var(--accent); border-left: 3px solid var(--accent); padding-left: 11px; }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(10,35,66,.08);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar h4 { font-size: 1.1rem; font-weight: 700; color: var(--primary); margin: 0; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .admin-badge {
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            color: #fff;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: .82rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-badge .dot { width: 8px; height: 8px; border-radius: 50%; background: #4caf50; }

        /* Content area */
        .content-area { padding: 28px; flex: 1; }

        /* Stat cards */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(10,35,66,.07);
            display: flex;
            align-items: center;
            gap: 18px;
            transition: transform .25s, box-shadow .25s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(10,35,66,.12); }
        .stat-icon {
            width: 56px; height: 56px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-icon.blue { background: linear-gradient(135deg, #1a3a6b, #2a5298); color: #fff; }
        .stat-icon.gold { background: linear-gradient(135deg, var(--accent), var(--accent-light)); color: var(--primary); }
        .stat-icon.green { background: linear-gradient(135deg, #1b5e20, #2e7d32); color: #fff; }
        .stat-icon.purple { background: linear-gradient(135deg, #4a148c, #6a1b9a); color: #fff; }
        .stat-num { font-size: 1.8rem; font-weight: 800; color: var(--primary); line-height: 1; }
        .stat-lbl { font-size: .82rem; color: #6b7a99; margin-top: 2px; }

        /* Section panels */
        .panel {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(10,35,66,.07);
            overflow: hidden;
            margin-bottom: 28px;
            display: none;
        }
        .panel.active { display: block; animation: fadeUp .4s ease; }
        .panel-header {
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            padding: 20px 28px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .panel-header-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            background: rgba(201,168,76,.2);
            border: 1px solid rgba(201,168,76,.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: var(--accent);
        }
        .panel-header h3 { color: #fff; font-size: 1rem; font-weight: 700; margin: 0; }
        .panel-header p { color: rgba(255,255,255,.55); font-size: .8rem; margin: 0; }
        .panel-body { padding: 28px; }

        /* Table */
        .table { font-size: .88rem; }
        .table thead th {
            background: var(--light-bg);
            color: var(--primary);
            font-weight: 700;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none;
            padding: 12px 16px;
        }
        .table tbody td { padding: 12px 16px; vertical-align: middle; border-color: #f0f4f8; color: #3a4a6b; }
        .table tbody tr:hover { background: #f8faff; }
        .badge-status {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: .75rem;
            font-weight: 600;
        }
        .badge-paid { background: #e8f5e9; color: #2e7d32; }
        .badge-pending { background: #fff8e1; color: #f57f17; }

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
        .btn-primary-custom {
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
        }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(10,35,66,.3); }
        .btn-accent-custom {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--primary);
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 700;
            font-size: .92rem;
            cursor: pointer;
            transition: transform .25s, box-shadow .25s;
            font-family: 'Inter', sans-serif;
        }
        .btn-accent-custom:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(201,168,76,.4); }

        /* Success toast */
        .toast-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .toast-overlay.show { display: flex; }
        .toast-box {
            background: #fff;
            border-radius: 20px;
            padding: 40px 36px;
            max-width: 380px;
            width: 90%;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0,0,0,.3);
            animation: scaleIn .3s ease;
        }
        .toast-icon-wrap {
            width: 68px; height: 68px;
            border-radius: 50%;
            background: #e8f5e9;
            color: #2e7d32;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin-bottom: 16px;
        }
        .toast-box h4 { font-size: 1.2rem; font-weight: 800; color: var(--primary); margin-bottom: 8px; }
        .toast-box p { color: #6b7a99; font-size: .9rem; margin-bottom: 20px; }
        .btn-toast { background: linear-gradient(135deg, var(--primary), #1a3a6b); color: #fff; border: none; padding: 11px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { transform: scale(.85); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <h2>Edu<span>Manage</span></h2>
        <p>Admin Control Panel</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <button class="sidebar-link active" onclick="showPanel('dashboard', this)">
            <i class="bi bi-grid-fill"></i> Dashboard
        </button>

        <div class="nav-section-label">Management</div>
        <button class="sidebar-link" onclick="showPanel('applications', this)">
            <i class="bi bi-file-earmark-person-fill"></i> Applications
        </button>
        <button class="sidebar-link" onclick="showPanel('register', this)">
            <i class="bi bi-person-plus-fill"></i> Register Student
        </button>
        <button class="sidebar-link" onclick="showPanel('marks', this)">
            <i class="bi bi-pencil-square"></i> Enter Marks
        </button>
    </nav>
    <div class="sidebar-footer">
        <a href="../home.html" class="sidebar-link" style="color:rgba(255,100,100,.7)">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</aside>

<!-- Main -->
<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm border-0 d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h4 id="topbarTitle">Dashboard</h4>
        </div>
        <div class="topbar-right">
            <div class="admin-badge"><div class="dot"></div> Administrator</div>
        </div>
    </div>

    <div class="content-area">

        <!-- Dashboard Panel -->
        <div class="panel active" id="panel-dashboard">
            <div class="row g-4 mb-4">
                <?php
                $servername = "localhost"; $dbuser = "root"; $dbpass = ""; $dbname = "myschool";
                $conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
                $totalStudents = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'] ?? 0;
                $totalApps = $conn->query("SELECT COUNT(*) as c FROM admissions")->fetch_assoc()['c'] ?? 0;
                $totalMarks = $conn->query("SELECT COUNT(*) as c FROM marks")->fetch_assoc()['c'] ?? 0;
                ?>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                        <div><div class="stat-num"><?= $totalStudents ?></div><div class="stat-lbl">Total Students</div></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon gold"><i class="bi bi-file-earmark-person-fill"></i></div>
                        <div><div class="stat-num"><?= $totalApps ?></div><div class="stat-lbl">Applications</div></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="bi bi-pencil-fill"></i></div>
                        <div><div class="stat-num"><?= $totalMarks ?></div><div class="stat-lbl">Marks Entered</div></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="bi bi-calendar-check-fill"></i></div>
                        <div><div class="stat-num">2024</div><div class="stat-lbl">Academic Year</div></div>
                    </div>
                </div>
            </div>
            <div class="panel" style="display:block">
                <div class="panel-header">
                    <div class="panel-header-icon"><i class="bi bi-clock-history"></i></div>
                    <div><h3>Recent Applications</h3><p>Latest student admission requests</p></div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>Name</th><th>DOB</th><th>Gender</th><th>Grade</th><th>Payment</th></tr></thead>
                            <tbody>
                            <?php
                            $r = $conn->query("SELECT student_name, dob, gender, grade_selection, payment_status FROM admissions ORDER BY id DESC LIMIT 5");
                            while ($row = $r->fetch_assoc()):
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                                <td><?= htmlspecialchars($row['dob']) ?></td>
                                <td><?= htmlspecialchars($row['gender']) ?></td>
                                <td><?= htmlspecialchars($row['grade_selection']) ?></td>
                                <td><span class="badge-status <?= $row['payment_status'] == 'Paid' ? 'badge-paid' : 'badge-pending' ?>"><?= htmlspecialchars($row['payment_status']) ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications Panel -->
        <div class="panel" id="panel-applications">
            <div class="panel-header">
                <div class="panel-header-icon"><i class="bi bi-file-earmark-person-fill"></i></div>
                <div><h3>Student Applications</h3><p>All submitted admission applications</p></div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Full Name</th><th>DOB</th><th>Gender</th><th>Address</th><th>Guardian</th><th>Prev. School</th><th>Medical</th><th>Documents</th></tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM admissions");
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['dob']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['address']) ?></td>
                            <td><?= htmlspecialchars($row['guardian_name']) ?></td>
                            <td><?= htmlspecialchars($row['previous_school']) ?></td>
                            <td><?= htmlspecialchars($row['medical_conditions']) ?></td>
                            <td>
                                <a href="../uploads/<?= $row['birth_certificate'] ?>" style="color:var(--accent);font-size:.82rem">Birth Cert</a> &nbsp;
                                <a href="../uploads/<?= $row['proof_of_address'] ?>" style="color:var(--accent);font-size:.82rem">Address</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Register Student Panel -->
        <div class="panel" id="panel-register">
            <div class="panel-header">
                <div class="panel-header-icon"><i class="bi bi-person-plus-fill"></i></div>
                <div><h3>Register New Student</h3><p>Add a student to the system</p></div>
            </div>
            <div class="panel-body">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form action="register_student.php" method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" placeholder="Student's full name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender" required>
                                        <option value="">Select gender</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Class</label>
                                    <input type="text" class="form-control" name="class" placeholder="e.g. Grade 3" required>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn-accent-custom">
                                        <i class="bi bi-person-check-fill me-2"></i>Register Student
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enter Marks Panel -->
        <div class="panel" id="panel-marks">
            <div class="panel-header">
                <div class="panel-header-icon"><i class="bi bi-pencil-square"></i></div>
                <div><h3>Enter Student Marks</h3><p>Record subject marks for a student</p></div>
            </div>
            <div class="panel-body">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form action="enter_marks.php" method="post">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Select Student</label>
                                    <select class="form-select" name="student_id" required>
                                        <option value="">Choose a student...</option>
                                        <?php
                                        $r = $conn->query("SELECT id, full_name FROM students");
                                        if ($r && $r->num_rows > 0) {
                                            while ($row = $r->fetch_assoc()) {
                                                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['full_name']) . "</option>";
                                            }
                                        } else {
                                            echo "<option disabled>No students registered yet</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subject</label>
                                    <select class="form-select" name="subject" required>
                                        <option value="maths">Mathematics</option>
                                        <option value="english">English</option>
                                        <option value="french">French</option>
                                        <option value="computer">Computer Science</option>
                                        <option value="economics">Economics</option>
                                        <option value="physics">Physics</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Marks (0–100)</label>
                                    <input type="number" class="form-control" name="marks" min="0" max="100" placeholder="Enter marks" required>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn-primary-custom">
                                        <i class="bi bi-save-fill me-2"></i>Save Marks
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /content-area -->
</div><!-- /main-content -->

<!-- Success Toast -->
<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
<div class="toast-overlay show" id="successToast">
    <div class="toast-box">
        <div class="toast-icon-wrap"><i class="bi bi-check-circle-fill"></i></div>
        <h4>Success!</h4>
        <p>The operation was completed successfully.</p>
        <button class="btn-toast" onclick="document.getElementById('successToast').classList.remove('show')">Done</button>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showPanel(name, btn) {
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
        document.getElementById('panel-' + name).classList.add('active');
        btn.classList.add('active');
        document.getElementById('topbarTitle').textContent = btn.textContent.trim();
    }
</script>
</body>
</html>
