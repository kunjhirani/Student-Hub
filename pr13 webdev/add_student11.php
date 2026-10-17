<?php

require_once "auth10.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard10.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "studenthub");
$conn->set_charset("utf8mb4");

$name = "";
$email = "";
$course = "";
$semester = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $semester = trim($_POST["semester"] ?? "");

    if ($name === "" || $email === "" || $course === "" || $semester === "") {
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } elseif (!ctype_digit($semester) || (int)$semester < 1 || (int)$semester > 12) {
        $errorMessage = "Semester must be a number between 1 and 12.";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO students (name, email, course, semester)
                 VALUES (?, ?, ?, ?)"
            );

            $semesterNumber = (int)$semester;

            $stmt->bind_param(
                "sssi",
                $name,
                $email,
                $course,
                $semesterNumber
            );

            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: students11.php?status=added");
            exit;

        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $errorMessage = "A student with this email already exists.";
            } else {
                error_log($e->getMessage());
                $errorMessage = "Unable to add the student. Please try again.";
            }
        }
    }
}

$conn->close();

function addStudentEscape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student | Student Hub</title>
    <link rel="stylesheet" href="student_management11.css">
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
        <h2>Add Student</h2>
        <p>Enter the student's information to create a new record.</p>
    </section>

    <?php if ($errorMessage !== ""): ?>
        <div class="alert alert-error" role="alert">
            <?php echo addStudentEscape($errorMessage); ?>
        </div>
    <?php endif; ?>

    <section class="management-card">

        <h3>Student Information</h3>

        <form method="POST" action="add_student11.php">

            <div class="form-grid">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        maxlength="100"
                        value="<?php echo addStudentEscape($name); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        maxlength="100"
                        value="<?php echo addStudentEscape($email); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="course">Course</label>
                    <select id="course" name="course" class="form-control" required>
                        <option value="">Select Course</option>
                        <option value="CSE" <?php echo $course === "CSE" ? "selected" : ""; ?>>CSE</option>
                        <option value="IT" <?php echo $course === "IT" ? "selected" : ""; ?>>IT</option>
                        <option value="CE" <?php echo $course === "CE" ? "selected" : ""; ?>>CE</option>
                        <option value="CIVIL" <?php echo $course === "CIVIL" ? "selected" : ""; ?>>CIVIL</option>
                        <option value="ME" <?php echo $course === "ME" ? "selected" : ""; ?>>ME</option>
                        <option value="Other" <?php echo $course === "Other" ? "selected" : ""; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" class="form-control" required>
                        <option value="">Select Semester</option>

                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option
                                value="<?php echo $i; ?>"
                                <?php echo (string)$semester === (string)$i ? "selected" : ""; ?>
                            >
                                Semester <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>

                    </select>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="button">Add Student</button>
                <a href="students11.php" class="button button-secondary">Cancel</a>
            </div>

        </form>

    </section>

</main>

</body>
</html>