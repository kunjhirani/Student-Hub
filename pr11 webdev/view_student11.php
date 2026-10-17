<?php

require_once "auth10.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard10.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "studenthub");
$conn->set_charset("utf8mb4");

function viewStudentEscape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $conn->close();
    header("Location: students11.php?error=invalid");
    exit;
}

$stmt = $conn->prepare(
    "SELECT student_id, name, email, course, semester
     FROM students
     WHERE student_id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$stmt->close();
$conn->close();

if (!$student) {
    header("Location: students11.php?error=notfound");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Details | Student Hub</title>

    <link rel="stylesheet" href="student_management11.css">

    <style>
        .student-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .detail-item {
            padding: 18px;
            background: #f5f9f3;
            border-radius: 8px;
            border: 1px solid #e5eee2;
        }

        .detail-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            color: #657365;
        }

        .detail-value {
            font-size: 16px;
            font-weight: bold;
            color: #263c27;
            overflow-wrap: anywhere;
        }

        @media (max-width: 600px) {
            .student-details {
                grid-template-columns: 1fr;
            }
        }

        body.dark-mode .detail-item {
            background: #2c382d;
            border-color: #465647;
        }

        body.dark-mode .detail-label {
            color: #b8c4b8;
        }

        body.dark-mode .detail-value {
            color: #e6eee6;
        }
    </style>
</head>

<body>

<header class="management-navbar">
    <div class="management-brand">
        <img src="studenthublogo.png" alt="Student Hub Logo">

        <div>
            <h1>Student Hub</h1>
            <p>Student Management System</p>
        </div>
    </div>

    <nav class="management-actions">
        <a href="students11.php">Student Records</a>
        <a href="admin_dashboard10.php">Dashboard</a>
        <a href="logout10.php" class="logout-link">Logout</a>
    </nav>
</header>

<main class="management-container">

    <section class="management-heading">
        <h2>Student Details</h2>
        <p>View the complete information for the selected student.</p>
    </section>

    <section class="management-card">

        <h3>Student Record #<?php echo (int)$student["student_id"]; ?></h3>

        <div class="student-details">

            <div class="detail-item">
                <span class="detail-label">Student ID</span>
                <span class="detail-value">
                    <?php echo (int)$student["student_id"]; ?>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Full Name</span>
                <span class="detail-value">
                    <?php echo viewStudentEscape($student["name"]); ?>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Email Address</span>
                <span class="detail-value">
                    <?php echo viewStudentEscape($student["email"]); ?>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Course</span>
                <span class="detail-value">
                    <?php echo viewStudentEscape($student["course"]); ?>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Semester</span>
                <span class="detail-value">
                    <?php echo (int)$student["semester"]; ?>
                </span>
            </div>

        </div>

        <div class="form-actions">
            <a
                href="edit_student11.php?id=<?php echo (int)$student["student_id"]; ?>"
                class="button"
            >
                Edit Student
            </a>

            <a href="students11.php" class="button button-secondary">
                Back to Records
            </a>
        </div>

    </section>

</main>

</body>
</html>