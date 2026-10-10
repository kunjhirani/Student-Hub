<?php

include "db.php";

$stmt = $pdo->prepare(
    "SELECT * FROM students WHERE course = ?"
);

$stmt->execute(["CSE"]);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($students as $student) {
    echo "Name: " . $student["name"] . "<br>";
    echo "Email: " . $student["email"] . "<br>";
    echo "Course: " . $student["course"] . "<br>";
    echo "Semester: " . $student["semester"] . "<br><br>";
}

?>