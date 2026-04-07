<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myschool";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    $query = "SELECT id, full_name, class FROM students WHERE full_name = '$username' AND class = '$class'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['full_name'];
        $_SESSION['student_class'] = $student['class'];
        header("Location: student board/studentdash.php");
        exit();
    } else {
        $error_message = "Invalid name or class. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login — EduManage Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0a2342;
            --accent: #c9a84c;
            --accent-light: #f0d080;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(10,35,66,.92) 0%, rgba(10,35,66,.7) 100%), url('sss.jfif') center/cover no-repeat fixed;
        }
        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            animation: fadeUp .7s ease both;
        }
        .login-card {
            background: rgba(255,255,255,.97);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 30px 80px rgba(0,0,0,.35);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 14px;
            box-shadow: 0 8px 24px rgba(10,35,66,.3);
        }
        .login-logo h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 4px;
        }
        .login-logo p { font-size: .88rem; color: #6b7a99; }
        .form-label {
            font-size: .85rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 6px;
        }
        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa5be;
            font-size: 1rem;
            z-index: 2;
        }
        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e8ecf4;
            border-radius: 12px;
            font-size: .95rem;
            font-family: 'Inter', sans-serif;
            color: var(--primary);
            background: #f8faff;
            transition: border-color .25s, box-shadow .25s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(201,168,76,.15);
            background: #fff;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), #1a3a6b);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: transform .25s, box-shadow .25s;
            box-shadow: 0 6px 20px rgba(10,35,66,.3);
            margin-top: 8px;
            font-family: 'Inter', sans-serif;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(10,35,66,.4);
        }
        .error-alert {
            background: #fff0f0;
            border: 1px solid #ffcdd2;
            border-left: 4px solid #e53935;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: .88rem;
            color: #c62828;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .back-link {
            text-align: center;
            margin-top: 24px;
            font-size: .88rem;
            color: #6b7a99;
        }
        .back-link a { color: var(--accent); font-weight: 600; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #c0c8d8;
            font-size: .8rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e8ecf4;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <div class="icon-wrap"><i class="bi bi-mortarboard-fill"></i></div>
            <h1>Student Portal</h1>
            <p>Sign in to access your dashboard</p>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="error-alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <?= htmlspecialchars($error_message) ?>
        </div>
        <?php endif; ?>

        <form method="post" action="">
            <label class="form-label">Full Name</label>
            <div class="input-group-custom">
                <i class="bi bi-person-fill input-icon"></i>
                <input type="text" class="form-control" name="username" placeholder="Enter your full name" autocomplete="off" required>
            </div>

            <label class="form-label">Class</label>
            <div class="input-group-custom">
                <i class="bi bi-hash input-icon"></i>
                <input type="number" class="form-control" name="class" placeholder="Enter your class number" autocomplete="off" required>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="divider">or</div>

        <div class="back-link">
            New student? <a href="admi.php">Apply for Admission</a>
        </div>
        <div class="back-link mt-2">
            <a href="home.html"><i class="bi bi-arrow-left me-1"></i>Back to Home</a>
        </div>
    </div>
</div>
</body>
</html>
