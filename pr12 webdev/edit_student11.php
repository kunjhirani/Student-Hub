<?php

require_once "auth10.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard10.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "studenthub");
$conn->set_charset("utf8mb4");

function editStudentEscape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "student_id", FILTER_VALIDATE_INT);
}

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

if (!$student) {
    $conn->close();
    header("Location: students11.php?error=notfound");
    exit;
}

$name = $student["name"];
$email = $student["email"];
$course = $student["course"];
$semester = (string)$student["semester"];
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
                "UPDATE students
                 SET name = ?, email = ?, course = ?, semester = ?
                 WHERE student_id = ?"
            );

            $semesterNumber = (int)$semester;

            $stmt->bind_param(
                "sssii",
                $name,
                $email,
                $course,
                $semesterNumber,
                $id
            );

            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: students11.php?status=updated");
            exit;

        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $errorMessage = "Another student already uses this email address.";
            } else {
                error_log($e->getMessage());
                $errorMessage = "Unable to update the student. Please try again.";
            }
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Student | Student Hub</title>

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
        <h2>Edit Student</h2>
        <p>Update the selected student's information.</p>
    </section>

    <?php if ($errorMessage !== ""): ?>
        <div class="alert alert-error" role="alert">
            <?php echo editStudentEscape($errorMessage); ?>
        </div>
    <?php endif; ?>

    <section class="management-card">

        <h3>Student Information</h3>

        <form method="POST" action="edit_student11.php?id=<?php echo (int)$id; ?>">

            <input
                type="hidden"
                name="student_id"
                value="<?php echo (int)$id; ?>"
            >

            <div class="form-grid">

                <div class="form-group">
                    <label for="name">Full Name</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        maxlength="100"
                        value="<?php echo editStudentEscape($name); ?>"
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
                        value="<?php echo editStudentEscape($email); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="course">Course</label>

                    <select id="course" name="course" class="form-control" required>
                        <option value="">Select Course</option>

                        <?php
                        $courseOptions = ["CSE", "IT", "CE", "CIVIL", "ME", "Other"];

                        foreach ($courseOptions as $option):
                        ?>
                            <option
                                value="<?php echo editStudentEscape($option); ?>"
                                <?php echo $course === $option ? "selected" : ""; ?>
                            >
                                <?php echo editStudentEscape($option); ?>
                            </option>
                        <?php endforeach; ?>

                        <?php if ($course !== "" && !in_array($course, $courseOptions, true)): ?>
                            <option value="<?php echo editStudentEscape($course); ?>" selected>
                                <?php echo editStudentEscape($course); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>

                    <select id="semester" name="semester" class="form-control" required>
                        <option value="">Select Semester</option>

                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option
                                value="<?php echo $i; ?>"
                                <?php echo $semester === (string)$i ? "selected" : ""; ?>
                            >
                                Semester <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>

                    </select>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="button">Save Changes</button>

                <a href="students11.php" class="button button-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </section>

</main>

</body>
</html>