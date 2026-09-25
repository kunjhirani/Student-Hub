<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$errors = [];

$fullName = trim($_POST["fullName"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = $_POST["course"] ?? "";
$year = $_POST["year"] ?? "";
$gender = $_POST["gender"] ?? "";
$terms = $_POST["terms"] ?? "";


/* Full Name Validation */

if ($fullName === "") {

    $errors[] = "Full name is required.";

} elseif (!preg_match("/^[A-Za-z ]{2,50}$/", $fullName)) {

    $errors[] = "Full name must contain only letters and spaces.";

}


/* Email Validation */

if ($email === "") {

    $errors[] = "Email is required.";

} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errors[] = "Enter a valid email address.";

}


/* Mobile Validation */

if ($mobile === "") {

    $errors[] = "Mobile number is required.";

} elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {

    $errors[] = "Mobile number must contain exactly 10 digits.";

}


/* Password Validation */

if ($password === "") {

    $errors[] = "Password is required.";

} elseif (
    !preg_match(
        "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&]).{8,}$/",
        $password
    )
) {

    $errors[] =
        "Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number and one special character.";

}


/* Confirm Password */

if ($confirmPassword === "") {

    $errors[] = "Please confirm your password.";

} elseif ($password !== $confirmPassword) {

    $errors[] = "Passwords do not match.";

}


/* Course Validation */

$validCourses = [
    "CSE",
    "IT",
    "AIDS",
    "ECE"
];

if (!in_array($course, $validCourses, true)) {

    $errors[] = "Please select a valid course.";

}


/* Year Validation */

$validYears = [
    "1",
    "2",
    "3",
    "4"
];

if (!in_array($year, $validYears, true)) {

    $errors[] = "Please select a valid year.";

}


/* Gender Validation */

$validGenders = [
    "Male",
    "Female",
    "Other"
];

if (!in_array($gender, $validGenders, true)) {

    $errors[] = "Please select your gender.";

}


/* Terms Validation */

if ($terms !== "accepted") {

    $errors[] =
        "You must accept the terms and conditions.";

}


/* Display Errors */

if (!empty($errors)) {

    echo "<!DOCTYPE html>";

    echo "<html lang='en'>";

    echo "<head>";

    echo "<meta charset='UTF-8'>";

    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";

    echo "<title>Registration Error</title>";

    echo "<style>";

    echo "
        body {
            font-family: Arial, sans-serif;
            background: #eef7ee;
            padding: 40px;
        }

        .box {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px #ccc;
        }

        h1 {
            color: green;
        }

        .error {
            color: red;
            margin: 10px 0;
        }

        a {
            color: green;
            font-weight: bold;
        }
    ";

    echo "</style>";

    echo "</head>";

    echo "<body>";

    echo "<div class='box'>";

    echo "<h1>Registration Failed</h1>";

    foreach ($errors as $error) {

        echo "<p class='error'>"
            . htmlspecialchars($error)
            . "</p>";

    }

    echo "<br>";

    echo "<a href='signup.html'>Go Back to Registration</a>";

    echo "</div>";

    echo "</body>";

    echo "</html>";

    exit;
}


/* CSV File */

$file =
    __DIR__ .
    DIRECTORY_SEPARATOR .
    "registrations.csv";


/* Create CSV File If It Does Not Exist */

if (!file_exists($file)) {

    $handle = fopen($file, "w");

    if ($handle === false) {

        die("Unable to create registrations.csv.");

    }

    fputcsv(
        $handle,
        [
            "Full Name",
            "Email",
            "Mobile",
            "Password",
            "Course",
            "Year",
            "Gender"
        ]
    );

    fclose($handle);

}


/* Store Registration */

$handle = fopen($file, "a");

if ($handle === false) {

    die("Unable to open registrations.csv.");

}

$hashedPassword =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );

fputcsv(
    $handle,
    [
        $fullName,
        $email,
        $mobile,
        $hashedPassword,
        $course,
        $year,
        $gender
    ]
);

fclose($handle);


/* Success Message */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Registration Successful</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #eef7ee;
            margin: 0;
            padding: 40px;
        }

        .success-box {
            width: 500px;
            max-width: 90%;
            margin: 80px auto;
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 10px #ccc;
        }

        h1 {
            color: green;
        }

        p {
            font-size: 18px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #28c828;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

    </style>

</head>

<body>

    <div class="success-box">

        <h1>Registration Successful!</h1>

        <p>
            Welcome,
            <?php echo htmlspecialchars($fullName); ?>.
        </p>

        <p>
            Your registration has been saved successfully.
        </p>

        <a href="home.html">
            Go to Home
        </a>

    </div>

</body>

</html>