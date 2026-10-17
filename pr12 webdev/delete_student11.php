<?php

require_once "auth10.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard10.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: students11.php?error=invalid");
    exit;
}

$id = filter_input(INPUT_POST, "student_id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: students11.php?error=invalid");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "studenthub");
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare(
        "DELETE FROM students WHERE student_id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        $status = "deleted";
    } else {
        $status = "notfound";
    }

    $stmt->close();
    $conn->close();

    if ($status === "deleted") {
        header("Location: students11.php?status=deleted");
    } else {
        header("Location: students11.php?error=notfound");
    }

    exit;

} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }

    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }

    header("Location: students11.php?error=failed");
    exit;
}