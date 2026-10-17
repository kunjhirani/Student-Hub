<?php

require_once "auth10.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard10.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "studenthub");
$conn->set_charset("utf8mb4");

$totalAccounts = 0;
$studentAccounts = 0;
$adminAccounts = 0;
$students = [];

$result = $conn->query(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) AS students,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS admins
     FROM users"
);

$stats = $result->fetch_assoc();

$totalAccounts = (int) $stats["total"];
$studentAccounts = (int) $stats["students"];
$adminAccounts = (int) $stats["admins"];

$result = $conn->query(
    "SELECT user_id, name, username, email, role, created_at
     FROM users
     ORDER BY user_id DESC"
);

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$conn->close();

function adminEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Student Hub</title>

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
            max-width: 1250px;
            margin: 35px auto;
        }

        .page-heading {
            margin-bottom: 25px;
        }

        .page-heading h2 {
            margin: 0 0 8px;
            font-size: 28px;
            color: #243c26;
        }

        .page-heading p {
            margin: 0;
            color: #68736a;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 23px;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.06);
            border: 1px solid #edf0ed;
            border-top: 4px solid limegreen;
        }

        .stat-card .stat-label {
            display: block;
            color: #707970;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .stat-card .stat-number {
            display: block;
            color: #263b27;
            font-size: 32px;
            font-weight: bold;
        }

        .overview-card {
            background: white;
            border-radius: 12px;
            padding: 27px;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.06);
            border: 1px solid #edf0ed;
        }

        .overview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .overview-header h3 {
            margin: 0 0 9px;
            font-size: 21px;
            color: #293c2a;
        }

        .overview-header p {
            margin: 0;
            color: #707970;
            font-size: 14px;
            line-height: 1.6;
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

        .overview-note {
            margin: 23px 0 0;
            color: #788078;
            font-size: 13px;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            padding: 25px;
            background: rgba(24, 31, 25, 0.58);
            align-items: center;
            justify-content: center;
        }

        .modal.open {
            display: flex;
        }

        .modal-content {
            width: 100%;
            max-width: 1000px;
            max-height: 85vh;
            overflow: auto;
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0 0 7px;
            color: #293c2a;
            font-size: 22px;
        }

        .modal-header p {
            margin: 0;
            color: #727a72;
            font-size: 13px;
        }

        .close-modal {
            border: none;
            background: #edf3ed;
            color: #304531;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            font-size: 21px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .search-input {
            width: 100%;
            max-width: 360px;
            padding: 11px 13px;
            border: 1px solid #d9e1d9;
            border-radius: 7px;
            font-size: 14px;
            outline: none;
            margin-bottom: 18px;
        }

        .search-input:focus {
            border-color: limegreen;
            box-shadow: 0 0 0 3px rgba(50, 205, 50, 0.13);
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        .records-table th {
            padding: 13px 12px;
            background: #f0f7ef;
            color: #385239;
            font-weight: bold;
            white-space: nowrap;
        }

        .records-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #edf0ed;
            color: #4c554c;
        }

        .records-table tbody tr:hover {
            background: #f8fbf7;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .role-student {
            background: #e2f7e3;
            color: #267b2b;
        }

        .role-admin {
            background: #fff0cc;
            color: #805b00;
        }

        .view-button {
            border: none;
            border-radius: 5px;
            background: limegreen;
            color: #173719;
            padding: 8px 11px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .view-button:hover {
            background: #28b428;
        }

        .empty-message {
            text-align: center;
            padding: 25px;
            color: #727a72;
        }

        .details-box {
            display: none;
            margin-top: 20px;
            padding: 18px;
            background: #f4f9f2;
            border: 1px solid #dcebd9;
            border-radius: 9px;
            line-height: 1.9;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .details-box h3 {
            margin: 0 0 12px;
            color: #2d4a2e;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
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

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .stat-card {
                padding: 19px;
            }

            .overview-card {
                padding: 20px;
            }

            .modal {
                padding: 12px;
            }

            .modal-content {
                padding: 18px;
                max-height: 90vh;
            }
        }

        body.dark-mode {
            background: #171d18;
            color: #e6eee6;
        }

        body.dark-mode .dashboard-container .page-heading h2,
        body.dark-mode .stat-number,
        body.dark-mode .overview-header h3 {
            color: #e6eee6;
        }

        body.dark-mode .page-heading p,
        body.dark-mode .stat-label,
        body.dark-mode .overview-header p,
        body.dark-mode .overview-note {
            color: #b8c4b8;
        }

        body.dark-mode .stat-card,
        body.dark-mode .overview-card,
        body.dark-mode .modal-content {
            background: #232c24;
            border-color: #354336;
        }

        body.dark-mode .modal-header h2,
        body.dark-mode .details-box h3 {
            color: #e6eee6;
        }

        body.dark-mode .modal-header p,
        body.dark-mode .records-table td {
            color: #d1dbd1;
        }

        body.dark-mode .records-table th,
        body.dark-mode .details-box {
            background: #2c382d;
            color: #e6eee6;
        }

        body.dark-mode .records-table td {
            border-color: #354336;
        }

        body.dark-mode .search-input {
            background: #171d18;
            color: white;
            border-color: #465647;
        }
    </style>
</head>

<body>

<header class="dashboard-navbar">
    <div class="dashboard-brand">
        <img src="studenthublogo.png" alt="Student Hub Logo">

        <div>
            <h1>Student Hub</h1>
            <p>Welcome, <?php echo adminEscape($_SESSION["name"]); ?>!</p>
        </div>
    </div>

    <nav class="dashboard-actions">
        <button type="button" id="openRecordsTop">View Student Records</button>
        <a href="home.html">Home</a>
        <button type="button" class="theme-toggle" id="themeToggle">🌙 Dark</button>
        <a href="logout10.php" class="logout-button">Logout</a>
    </nav>
</header>

<main class="dashboard-container">

    <div class="page-heading">
        <h2>Admin Dashboard</h2>
        <p>Manage registered accounts and view Student Hub user information.</p>
    </div>

    <section class="stats-grid">

        <article class="stat-card">
            <span class="stat-label">Total Accounts</span>
            <span class="stat-number"><?php echo $totalAccounts; ?></span>
        </article>

        <article class="stat-card">
            <span class="stat-label">Student Accounts</span>
            <span class="stat-number"><?php echo $studentAccounts; ?></span>
        </article>

        <article class="stat-card">
            <span class="stat-label">Admin Accounts</span>
            <span class="stat-number"><?php echo $adminAccounts; ?></span>
        </article>

    </section>

    <section class="overview-card">
        <div class="overview-header">
            <div>
                <h3>Account Overview</h3>
                <p>View registered accounts and their details.</p>
            </div>

            <button type="button" class="primary-button" id="openRecordsMain">
                Open Records
            </button>
        </div>

        <p class="overview-note">
            Open the records window to search accounts and view individual profiles.
        </p>
    </section>

</main>

<div class="modal" id="recordsModal" role="dialog" aria-modal="true" aria-labelledby="recordsTitle">
    <div class="modal-content">

        <div class="modal-header">
            <div>
                <h2 id="recordsTitle">Student &amp; User Records</h2>
                <p>Search registered accounts and select View Details to see a profile.</p>
            </div>

            <button type="button" class="close-modal" id="closeRecordsTop" aria-label="Close">&times;</button>
        </div>

        <input
            type="search"
            class="search-input"
            id="recordSearch"
            placeholder="Search name, email, username, or role..."
            autocomplete="off"
        >

        <div class="table-wrapper">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="recordsBody">
                    <?php foreach ($students as $student): ?>
                        <tr
                            data-search="<?php echo adminEscape(strtolower(
                                $student["name"] . " " .
                                $student["username"] . " " .
                                $student["email"] . " " .
                                $student["role"]
                            )); ?>"
                        >
                            <td><?php echo adminEscape($student["name"]); ?></td>
                            <td><?php echo adminEscape($student["username"]); ?></td>
                            <td><?php echo adminEscape($student["email"]); ?></td>
                            <td>
                                <span class="role-badge <?php echo $student["role"] === "admin" ? "role-admin" : "role-student"; ?>">
                                    <?php echo ucfirst(adminEscape($student["role"])); ?>
                                </span>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="view-button"
                                    data-name="<?php echo adminEscape($student["name"]); ?>"
                                    data-username="<?php echo adminEscape($student["username"]); ?>"
                                    data-email="<?php echo adminEscape($student["email"]); ?>"
                                    data-role="<?php echo adminEscape($student["role"]); ?>"
                                    data-created="<?php echo adminEscape($student["created_at"]); ?>"
                                >View Details</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($students) === 0): ?>
                        <tr>
                            <td colspan="5" class="empty-message">No registered accounts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="details-box" id="accountDetails">
            <h3>Account Details</h3>
            <div id="detailsContent"></div>
        </div>

        <div class="modal-footer">
            <button type="button" class="primary-button" id="closeRecordsBottom">Close</button>
        </div>

    </div>
</div>

<script>
    const recordsModal = document.getElementById("recordsModal");
    const recordSearch = document.getElementById("recordSearch");
    const accountDetails = document.getElementById("accountDetails");
    const detailsContent = document.getElementById("detailsContent");

    function openRecords() {
        recordsModal.classList.add("open");
        recordSearch.value = "";
        filterRecords();
        accountDetails.style.display = "none";
        recordSearch.focus();
    }

    function closeRecords() {
        recordsModal.classList.remove("open");
    }

    function filterRecords() {
        const query = recordSearch.value.toLowerCase().trim();
        const rows = document.querySelectorAll("#recordsBody tr[data-search]");

        rows.forEach(function(row) {
            row.style.display = row.dataset.search.includes(query) ? "" : "none";
        });
    }

    document.getElementById("openRecordsTop").addEventListener("click", openRecords);
    document.getElementById("openRecordsMain").addEventListener("click", openRecords);
    document.getElementById("closeRecordsTop").addEventListener("click", closeRecords);
    document.getElementById("closeRecordsBottom").addEventListener("click", closeRecords);
    recordSearch.addEventListener("input", filterRecords);

    recordsModal.addEventListener("click", function(event) {
        if (event.target === recordsModal) {
            closeRecords();
        }
    });

    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            closeRecords();
        }
    });

    document.querySelectorAll(".view-button").forEach(function(button) {
        button.addEventListener("click", function() {
            const name = this.dataset.name;
            const username = this.dataset.username;
            const email = this.dataset.email;
            const role = this.dataset.role;
            const created = this.dataset.created || "Not available";

            detailsContent.replaceChildren();

            [
                ["Name", name],
                ["Username", username],
                ["Email", email],
                ["Role", role.charAt(0).toUpperCase() + role.slice(1)],
                ["Account Created", created]
            ].forEach(function(item) {
                const line = document.createElement("div");
                const label = document.createElement("strong");

                label.textContent = item[0] + ": ";
                line.appendChild(label);
                line.appendChild(document.createTextNode(item[1] || "Not available"));
                detailsContent.appendChild(line);
            });

            accountDetails.style.display = "block";
            accountDetails.scrollIntoView({ behavior: "smooth", block: "nearest" });
        });
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