<?php

require_once "auth10.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard10.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "studenthub");
$conn->set_charset("utf8mb4");

$search = trim($_GET["search"] ?? "");
$courseFilter = trim($_GET["course"] ?? "");

$allowedMessages = [
    "added" => "Student added successfully.",
    "updated" => "Student updated successfully.",
    "deleted" => "Student deleted successfully."
];

$status = $_GET["status"] ?? "";
$error = $_GET["error"] ?? "";

$message = $allowedMessages[$status] ?? "";
$errorMessage = "";

if ($error === "duplicate") {
    $errorMessage = "A student with this email already exists.";
} elseif ($error === "notfound") {
    $errorMessage = "The requested student record was not found.";
} elseif ($error === "failed") {
    $errorMessage = "The operation could not be completed.";
} elseif ($error === "invalid") {
    $errorMessage = "Invalid request. Please try again.";
}

$courses = [];

$result = $conn->query(
    "SELECT DISTINCT course FROM students ORDER BY course"
);

while ($row = $result->fetch_assoc()) {
    $courses[] = $row["course"];
}

$sql = "SELECT student_id, name, email, course, semester
        FROM students
        WHERE 1 = 1";

$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (
        name LIKE ?
        OR email LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ss";
}

if ($courseFilter !== "") {
    $sql .= " AND course = ?";

    $params[] = $courseFilter;
    $types .= "s";
}

$sql .= " ORDER BY student_id DESC";

$stmt = $conn->prepare($sql);

if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();
$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
$conn->close();

function studentManagementEscape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Management | Student Hub</title>

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
        <a href="admin_dashboard10.php">Dashboard</a>
        <a href="home.html">Home</a>
        <a href="logout10.php" class="logout-link">Logout</a>
    </nav>

</header>

<main class="management-container">

    <section class="management-heading">
        <h2>Student Management</h2>
        <p>Add, view, edit, delete, search, and filter student records.</p>
    </section>

    <?php if ($message !== ""): ?>

        <div class="alert alert-success" role="status">
            <?php echo studentManagementEscape($message); ?>
        </div>

    <?php endif; ?>

    <?php if ($errorMessage !== ""): ?>

        <div class="alert alert-error" role="alert">
            <?php echo studentManagementEscape($errorMessage); ?>
        </div>

    <?php endif; ?>

    <section class="management-card">

        <div class="management-toolbar">

            <h3>Student Records</h3>

            <a href="add_student11.php" class="button">
                + Add Student
            </a>

        </div>

        <form method="GET" action="students11.php" class="management-toolbar">

            <div class="search-filter">

                <input
                    type="search"
                    name="search"
                    class="form-control search-control"
                    placeholder="Search by name or email..."
                    value="<?php echo studentManagementEscape($search); ?>"
                >

                <select name="course" class="form-control filter-control">

                    <option value="">All Courses</option>

                    <?php foreach ($courses as $course): ?>

                        <option
                            value="<?php echo studentManagementEscape($course); ?>"
                            <?php echo $courseFilter === $course ? "selected" : ""; ?>
                        >
                            <?php echo studentManagementEscape($course); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <button type="submit" class="button">
                    Search / Filter
                </button>

                <a href="students11.php" class="button button-secondary">
                    Reset
                </a>

            </div>

        </form>

        <div class="table-wrapper">

            <table class="student-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($students) > 0): ?>

                        <?php foreach ($students as $student): ?>

                            <tr>

                                <td>
                                    <?php echo (int)$student["student_id"]; ?>
                                </td>

                                <td>
                                    <?php echo studentManagementEscape($student["name"]); ?>
                                </td>

                                <td>
                                    <?php echo studentManagementEscape($student["email"]); ?>
                                </td>

                                <td>
                                    <?php echo studentManagementEscape($student["course"]); ?>
                                </td>

                                <td>
                                    <?php echo (int)$student["semester"]; ?>
                                </td>

                                <td>

                                    <div class="actions">

                                        <a
                                            href="view_student11.php?id=<?php echo (int)$student["student_id"]; ?>"
                                            class="button button-secondary"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="edit_student11.php?id=<?php echo (int)$student["student_id"]; ?>"
                                            class="button"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="delete_student11.php"
                                            onsubmit="return confirm('Are you sure you want to delete this student record?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="student_id"
                                                value="<?php echo (int)$student["student_id"]; ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="button button-danger"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="empty-state">
                                No student records found. Try a different search or add a student.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <p class="form-note">
            Showing <?php echo count($students); ?> matching student record(s).
        </p>

    </section>

</main>

</body>

</html>