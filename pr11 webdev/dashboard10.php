<?php

require_once "auth10.php";

if ($_SESSION["role"] !== "student") {
    header("Location: admin_dashboard10.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "studenthub");
$conn->set_charset("utf8mb4");

$stmt = $conn->prepare(
    "SELECT user_id, name, username, email, role, created_at
     FROM users
     WHERE user_id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$stmt->close();
$conn->close();

if (!$student || $student["role"] !== "student") {
    $_SESSION = [];
    session_destroy();
    header("Location: login.html");
    exit;
}

function studentEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard | Student Hub</title>

    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7f5;
            color: #263128;
            font-family: Arial, Helvetica, sans-serif;
        }

        .dashboard-navbar {
            background: limegreen;
            min-height: 82px;
            padding: 14px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .dashboard-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-brand img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .dashboard-brand h1 {
            margin: 0;
            color: #173719;
            font-family: "Courier New", monospace;
            font-size: 25px;
        }

        .dashboard-brand p {
            margin: 4px 0 0;
            color: #173719;
            font-size: 13px;
        }

        .dashboard-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .dashboard-actions a,
        .dashboard-actions button {
            border: none;
            border-radius: 7px;
            padding: 10px 15px;
            background: white;
            color: #28542a;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .dashboard-actions a:hover,
        .dashboard-actions button:hover {
            background: #e8f5e9;
            transform: translateY(-1px);
        }

        .dashboard-actions .logout-button {
            background: #d83b32;
            color: white;
        }

        .dashboard-actions .logout-button:hover {
            background: #b92d26;
        }

        .dashboard-container {
            width: 90%;
            max-width: 1150px;
            margin: 35px auto;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #edf0ed;
            border-left: 5px solid limegreen;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .welcome-card h2 {
            margin: 0 0 12px;
            font-size: 28px;
            color: #263b27;
        }

        .welcome-card p {
            margin: 0;
            color: #707970;
            font-size: 15px;
            line-height: 1.7;
        }

        .section-heading {
            margin: 30px 0 18px;
            font-size: 22px;
            color: #293c2a;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #edf0ed;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.06);
        }

        .dashboard-card h3 {
            margin: 0 0 12px;
            font-size: 19px;
            color: #293c2a;
        }

        .dashboard-card p {
            margin: 0 0 20px;
            color: #707970;
            font-size: 14px;
            line-height: 1.7;
        }

        .primary-button {
            border: none;
            background: limegreen;
            color: #173719;
            padding: 12px 18px;
            border-radius: 7px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .primary-button:hover {
            background: #28b428;
        }

        .profile-details {
            display: none;
            margin-top: 18px;
            background: #f4f9f2;
            border: 1px solid #dcebd9;
            border-radius: 9px;
            padding: 18px;
            line-height: 2;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .profile-details strong {
            color: #2d4a2e;
        }

        .theme-toggle {
            border: none;
            background: white;
            color: #28542a;
            padding: 10px 13px;
            border-radius: 7px;
            font-weight: bold;
            cursor: pointer;
        }

        @media (max-width: 700px) {
            .dashboard-navbar {
                padding: 15px 5%;
            }

            .dashboard-brand h1 {
                font-size: 21px;
            }

            .dashboard-container {
                width: 92%;
                margin: 25px auto;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .welcome-card,
            .dashboard-card {
                padding: 21px;
            }

            .welcome-card h2 {
                font-size: 24px;
            }
        }

        body.dark-mode {
            background: #171d18;
            color: #e6eee6;
        }

        body.dark-mode .welcome-card,
        body.dark-mode .dashboard-card {
            background: #232c24;
            border-color: #354336;
        }

        body.dark-mode .welcome-card h2,
        body.dark-mode .section-heading,
        body.dark-mode .dashboard-card h3 {
            color: #e6eee6;
        }

        body.dark-mode .welcome-card p,
        body.dark-mode .dashboard-card p {
            color: #b8c4b8;
        }

        body.dark-mode .profile-details {
            background: #2c382d;
            border-color: #465647;
        }

        body.dark-mode .profile-details strong {
            color: #d4f0d2;
        }
    </style>
</head>

<body>

<header class="dashboard-navbar">
    <div class="dashboard-brand">
        <img src="studenthublogo.png" alt="Student Hub Logo">

        <div>
            <h1>Student Hub</h1>
            <p>Welcome, <?php echo studentEscape($student["name"]); ?>!</p>
        </div>
    </div>

    <nav class="dashboard-actions">
        <a href="home.html">Home</a>
        <button type="button" class="theme-toggle" id="themeToggle">🌙 Dark</button>
        <a href="logout10.php" class="logout-button">Logout</a>
    </nav>
</header>

<main class="dashboard-container">

    <section class="welcome-card">
        <h2>Welcome to Your Student Dashboard!</h2>
        <p>
            You are successfully logged in to Student Hub.
            Access your account information and explore the student portal from here.
        </p>
    </section>

    <h2 class="section-heading">Your Dashboard</h2>

    <section class="dashboard-grid">

        <article class="dashboard-card">
            <h3>My Account</h3>
            <p>
                View your registered account information, including your username,
                email address, and account role.
            </p>

            <button type="button" class="primary-button" id="viewProfile">
                View Profile
            </button>

            <div class="profile-details" id="profileDetails">
                <div><strong>Name:</strong> <?php echo studentEscape($student["name"]); ?></div>
                <div><strong>Username:</strong> <?php echo studentEscape($student["username"]); ?></div>
                <div><strong>Email:</strong> <?php echo studentEscape($student["email"]); ?></div>
                <div><strong>Account Role:</strong> Student</div>
                <div><strong>Account Created:</strong> <?php echo studentEscape($student["created_at"] ?: "Not available"); ?></div>
            </div>
        </article>

        <article class="dashboard-card">
            <h3>Explore Student Hub</h3>
            <p>
                Visit the website to explore courses, admission information,
                events, results, and other student resources.
            </p>

            <a href="home.html" class="primary-button">Explore Website</a>
        </article>

    </section>

</main>

<script>
    const viewProfileButton = document.getElementById("viewProfile");
    const profileDetails = document.getElementById("profileDetails");

    viewProfileButton.addEventListener("click", function() {
        const isVisible = profileDetails.style.display === "block";

        profileDetails.style.display = isVisible ? "none" : "block";
        viewProfileButton.textContent = isVisible ? "View Profile" : "Hide Profile";
    });

    const themeToggle = document.getElementById("themeToggle");

    function applyTheme(theme) {
        document.body.classList.toggle("dark-mode", theme === "dark");
        themeToggle.textContent = theme === "dark" ? "☀️ Light" : "🌙 Dark";
    }

    let savedTheme = "light";

    try {
        savedTheme = localStorage.getItem("theme") || "light";
    } catch (error) {
        savedTheme = "light";
    }

    applyTheme(savedTheme);

    themeToggle.addEventListener("click", function() {
        const nextTheme = document.body.classList.contains("dark-mode") ? "light" : "dark";
        applyTheme(nextTheme);

        try {
            localStorage.setItem("theme", nextTheme);
        } catch (error) {
        }
    });
</script>

</body>
</html>