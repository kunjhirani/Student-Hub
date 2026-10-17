<?php

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";

    $errors = [];

    // Validate name
    if ($name === "") {
        $errors[] = "Name is required.";
    } elseif (!preg_match("/^[A-Za-z ]{2,100}$/", $name)) {
        $errors[] = "Name must contain only letters and spaces.";
    }

    // Validate username
    if ($username === "") {
        $errors[] = "Username is required.";
    } elseif (!preg_match("/^[A-Za-z0-9_]{3,50}$/", $username)) {
        $errors[] = "Username must be 3-50 characters and contain only letters, numbers, or underscores.";
    }

    // Validate email
    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    // Validate password
    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {
        $errors[] = "Password must have at least 8 characters, an uppercase letter, a lowercase letter, and a number.";
    }

    // Confirm password
    if ($confirmPassword === "") {
        $errors[] = "Please confirm your password.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {

        $host = "localhost";
        $dbName = "studenthub";
        $dbUser = "root";
        $dbPassword = "";

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {

            // Connect using MySQLi
            $conn = new mysqli(
                $host,
                $dbUser,
                $dbPassword,
                $dbName
            );

            $conn->set_charset("utf8mb4");

            // Check for duplicate email or username
            $check = $conn->prepare(
                "SELECT user_id
                 FROM users
                 WHERE email = ? OR username = ?
                 LIMIT 1"
            );

            $check->bind_param("ss", $email, $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {

                $errors[] = "Email or username already exists.";

            } else {

                // Hash the password
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Insert the new user using a prepared statement
                $insert = $conn->prepare(
                    "INSERT INTO users
                    (name, username, email, password)
                    VALUES (?, ?, ?, ?)"
                );

                $insert->bind_param(
                    "ssss",
                    $name,
                    $username,
                    $email,
                    $hashedPassword
                );

                $insert->execute();

                $message = "Registration successful!";
                $messageType = "success";

                $insert->close();
            }

            $check->close();
            $conn->close();

        } catch (mysqli_sql_exception $e) {

            // Log technical details on the server, not to the user
            error_log($e->getMessage());

            if ($e->getCode() === 1062) {
                $errors[] = "Email or username already exists.";
            } else {
                $errors[] = "Registration could not be completed. Please try again.";
            }

        }
    }

    if (!empty($errors)) {
        $message = implode(" ", $errors);
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure User Registration</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef7ee;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 450px;
            margin: 40px auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px #ccc;
        }

        h1 {
            text-align: center;
            color: green;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            border: none;
            background: limegreen;
            color: white;
            cursor: pointer;
        }

        .success {
            color: green;
            text-align: center;
            margin-top: 15px;
        }

        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>User Registration</h1>

        <?php if ($message !== ""): ?>
            <p class="<?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form action="register_user9.php" method="POST" id="userForm">

            <label for="name">Full Name</label>
            <input type="text" id="name" name="name"
                   maxlength="100" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   minlength="3" maxlength="50" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   maxlength="100" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   minlength="8" required>

            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword"
                   name="confirmPassword" required>

            <button type="submit">Register</button>

        </form>

    </div>

    <script>
        const form = document.getElementById("userForm");

        form.addEventListener("submit", function (event) {

            const name = document.getElementById("name").value.trim();
            const username = document.getElementById("username").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            const namePattern = /^[A-Za-z ]{2,100}$/;
            const usernamePattern = /^[A-Za-z0-9_]{3,50}$/;

            if (!namePattern.test(name)) {
                alert("Enter a valid name.");
                event.preventDefault();
                return;
            }

            if (!usernamePattern.test(username)) {
                alert("Username must be 3-50 characters and use only letters, numbers, or underscores.");
                event.preventDefault();
                return;
            }

            if (email === "") {
                alert("Email is required.");
                event.preventDefault();
                return;
            }

            if (
                password.length < 8 ||
                !/[A-Z]/.test(password) ||
                !/[a-z]/.test(password) ||
                !/[0-9]/.test(password)
            ) {
                alert("Password must contain at least 8 characters, an uppercase letter, a lowercase letter, and a number.");
                event.preventDefault();
                return;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                event.preventDefault();
            }
        });
    </script>

</body>
</html>