<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function redirectByRole($role)
{
    if ($role === "admin") {
        header("Location: admin_dashboard10.php");
    } else {
        header("Location: dashboard10.php");
    }
    exit;
}

function rememberCookieOptions($expires)
{
    return [
        "expires" => $expires,
        "path" => "/",
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ];
}

function clearRememberCookie()
{
    setcookie("remember_token", "", rememberCookieOptions(time() - 3600));
}

function restoreRememberedLogin($conn)
{
    if (empty($_COOKIE["remember_token"])) {
        return false;
    }

    $token = $_COOKIE["remember_token"];

    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        clearRememberCookie();
        return false;
    }

    $tokenHash = hash("sha256", $token);

    $stmt = $conn->prepare(
        "SELECT user_id, name, username, role
         FROM users
         WHERE remember_token_hash = ?
         LIMIT 1"
    );

    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$user) {
        clearRememberCookie();
        return false;
    }

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["last_activity"] = time();

    redirectByRole($user["role"]);
}

$message = "";

if (isset($_GET["timeout"])) {
    $message = "Your session expired. Please log in again.";
}

try {
    $conn = new mysqli("localhost", "root", "", "studenthub");
    $conn->set_charset("utf8mb4");

    if (isset($_SESSION["user_id"])) {
        redirectByRole($_SESSION["role"]);
    }

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        restoreRememberedLogin($conn);
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $login = trim($_POST["login"] ?? "");
        $password = $_POST["password"] ?? "";
        $selectedRole = $_POST["role"] ?? "";
        $rememberMe = isset($_POST["rememberMe"]);

        if (
            $login === "" ||
            $password === "" ||
            !in_array($selectedRole, ["student", "admin"], true)
        ) {
            $message = "Please enter your username/email, password, and a valid role.";
        } else {
            $stmt = $conn->prepare(
                "SELECT user_id, name, username, email, password, role
                 FROM users
                 WHERE username = ? OR email = ?
                 LIMIT 1"
            );

            $stmt->bind_param("ss", $login, $login);
            $stmt->execute();

            $user = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            if (
                $user &&
                password_verify($password, $user["password"]) &&
                $user["role"] === $selectedRole
            ) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["last_activity"] = time();

                if ($rememberMe) {
                    $token = bin2hex(random_bytes(32));
                    $tokenHash = hash("sha256", $token);
                    $expires = time() + (30 * 24 * 60 * 60);

                    $rememberStmt = $conn->prepare(
                        "UPDATE users SET remember_token_hash = ? WHERE user_id = ?"
                    );

                    $rememberStmt->bind_param(
                        "si",
                        $tokenHash,
                        $user["user_id"]
                    );

                    $rememberStmt->execute();
                    $rememberStmt->close();

                    setcookie(
                        "remember_token",
                        $token,
                        rememberCookieOptions($expires)
                    );
                } else {
                    $clearStmt = $conn->prepare(
                        "UPDATE users SET remember_token_hash = NULL WHERE user_id = ?"
                    );

                    $clearStmt->bind_param("i", $user["user_id"]);
                    $clearStmt->execute();
                    $clearStmt->close();

                    clearRememberCookie();
                }

                $conn->close();

                redirectByRole($user["role"]);
            }

            $message = "Invalid credentials or incorrect account role.";
        }
    }

    $conn->close();
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    $message = "Login is currently unavailable. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Hub - Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <img src="studenthublogo.png" alt="Student Hub Logo">
        <h1>Student Hub</h1>
    </div>

    <div class="links">
        <a href="home.html">Home</a> |
        <a href="about.html">About</a> |
        <a href="courses.html">Courses</a> |
        <a href="admission.html">Admission</a> |
        <a href="timetable.html">Time Table</a> |
        <a href="events.html">Events</a> |
        <a href="result.html">Result</a> |
        <a href="help.html">Help</a> |
        <a href="login.html">Login</a>
    </div>
</div>

<div class="main">
    <div class="login-box">
        <h1>Login</h1>

        <p class="description">Enter your account details</p>

        <?php if ($message !== ""): ?>
            <p class="error" role="alert">
                <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
            </p>
        <?php endif; ?>

        <form action="login10.php" method="POST">
            <label for="login">Username or Email</label>
            <input
                type="text"
                id="login"
                name="login"
                placeholder="Enter Username or Email"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter Password"
                autocomplete="current-password"
                required
            >

            <label for="role">Login As</label>
            <select id="role" name="role" required>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>

            <div class="remember-me">
                <label for="rememberMe">
                    <input
                        type="checkbox"
                        id="rememberMe"
                        name="rememberMe"
                        value="1"
                    >
                    Remember Me
                </label>
            </div>

            <div class="forgot">
                <a href="#">Forgot Password?</a>
            </div>

            <button type="submit" class="login-button">Login</button>
        </form>

        <p class="signup">
            Don't have an account?
            <a href="signup.html">Sign Up</a>
        </p>
    </div>
</div>

</body>
</html>