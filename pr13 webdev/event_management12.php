<?php
require_once "auth10.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403);
    exit("Access denied. Admin privileges are required.");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "studenthub");
$conn->set_charset("utf8mb4");

$table = "events12";
$uploadDir = __DIR__ . "/uploads/events12/";

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    exit("Unable to create the upload directory.");
}

$message = "";
$error = "";

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function removePoster($filename, $directory)
{
    if (
        $filename !== null &&
        $filename !== "" &&
        basename($filename) === $filename
    ) {
        $path = $directory . $filename;

        if (is_file($path)) {
            unlink($path);
        }
    }
}

function uploadPoster($directory)
{
    if (!isset($_FILES["poster"])) {
        return null;
    }

    $file = $_FILES["poster"];

    if ($file["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Poster upload failed.");
    }

    if ($file["size"] > 2 * 1024 * 1024) {
        throw new RuntimeException("Poster size must not exceed 2 MB.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException(
            "Only JPG, PNG, and WEBP poster images are allowed."
        );
    }

    if (getimagesize($file["tmp_name"]) === false) {
        throw new RuntimeException("The uploaded file is not a valid image.");
    }

    $filename = bin2hex(random_bytes(16)) . "." . $allowed[$mime];

    if (!move_uploaded_file($file["tmp_name"], $directory . $filename)) {
        throw new RuntimeException("Unable to save the poster.");
    }

    return $filename;
}

function validateEvent($name, $description, $date, $venue)
{
    if (
        trim($name) === "" ||
        trim($description) === "" ||
        trim($date) === "" ||
        trim($venue) === ""
    ) {
        return "All event fields are required.";
    }

    if (mb_strlen($name) > 100 || mb_strlen($venue) > 100) {
        return "Event name and venue must not exceed 100 characters.";
    }

    $parsedDate = DateTime::createFromFormat("!Y-m-d", $date);
    $dateErrors = DateTime::getLastErrors();

    if (
        !$parsedDate ||
        $parsedDate->format("Y-m-d") !== $date ||
        ($dateErrors !== false &&
            ($dateErrors["warning_count"] > 0 ||
             $dateErrors["error_count"] > 0))
    ) {
        return "Please enter a valid event date.";
    }

    return "";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $action = $_POST["action"] ?? "";

        if ($action === "add" || $action === "update") {
            $eventName = trim($_POST["event_name"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $eventDate = trim($_POST["event_date"] ?? "");
            $venue = trim($_POST["venue"] ?? "");

            $validationError = validateEvent(
                $eventName,
                $description,
                $eventDate,
                $venue
            );

            if ($validationError !== "") {
                throw new RuntimeException($validationError);
            }

            $newPoster = uploadPoster($uploadDir);

            if ($action === "add") {
                $stmt = $conn->prepare(
                    "INSERT INTO events12
                    (event_name, description, event_date, venue, poster)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "sssss",
                    $eventName,
                    $description,
                    $eventDate,
                    $venue,
                    $newPoster
                );

                $stmt->execute();
                $stmt->close();

                $message = "Event added successfully.";
            } else {
                $eventId = filter_input(
                    INPUT_POST,
                    "event_id",
                    FILTER_VALIDATE_INT
                );

                if (!$eventId || $eventId < 1) {
                    removePoster($newPoster, $uploadDir);
                    throw new RuntimeException("Invalid event ID.");
                }

                $stmt = $conn->prepare(
                    "SELECT poster FROM events12 WHERE event_id = ?"
                );

                $stmt->bind_param("i", $eventId);
                $stmt->execute();

                $existingEvent = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$existingEvent) {
                    removePoster($newPoster, $uploadDir);
                    throw new RuntimeException("Event not found.");
                }

                $finalPoster = $newPoster ?? $existingEvent["poster"];

                $stmt = $conn->prepare(
                    "UPDATE events12
                    SET event_name = ?, description = ?,
                        event_date = ?, venue = ?, poster = ?
                    WHERE event_id = ?"
                );

                $stmt->bind_param(
                    "sssssi",
                    $eventName,
                    $description,
                    $eventDate,
                    $venue,
                    $finalPoster,
                    $eventId
                );

                $stmt->execute();
                $stmt->close();

                if (
                    $newPoster !== null &&
                    $existingEvent["poster"] !== $newPoster
                ) {
                    removePoster($existingEvent["poster"], $uploadDir);
                }

                $message = "Event updated successfully.";
            }
        } elseif ($action === "delete") {
            $eventId = filter_input(
                INPUT_POST,
                "event_id",
                FILTER_VALIDATE_INT
            );

            if (!$eventId || $eventId < 1) {
                throw new RuntimeException("Invalid event ID.");
            }

            $stmt = $conn->prepare(
                "SELECT poster FROM events12 WHERE event_id = ?"
            );

            $stmt->bind_param("i", $eventId);
            $stmt->execute();

            $event = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$event) {
                throw new RuntimeException("Event not found.");
            }

            $stmt = $conn->prepare(
                "DELETE FROM events12 WHERE event_id = ?"
            );

            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $stmt->close();

            removePoster($event["poster"], $uploadDir);

            $message = "Event deleted successfully.";
        } else {
            throw new RuntimeException("Invalid action.");
        }
    } catch (Throwable $exception) {
        $error = $exception instanceof RuntimeException
            ? $exception->getMessage()
            : "An unexpected error occurred. Please check your PHP and database configuration.";
    }
}

$editEvent = null;

if (isset($_GET["edit"])) {
    $editId = filter_input(INPUT_GET, "edit", FILTER_VALIDATE_INT);

    if ($editId && $editId > 0) {
        $stmt = $conn->prepare(
            "SELECT * FROM events12 WHERE event_id = ?"
        );

        $stmt->bind_param("i", $editId);
        $stmt->execute();

        $editEvent = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

$result = $conn->query(
    "SELECT * FROM events12 ORDER BY event_date DESC, event_id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Event Management | Student Hub</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            background: #f4f7f2;
            color: #263326;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1150px;
            margin: auto;
        }

        h1, h2 {
            color: #287b24;
        }

        .card {
            background: white;
            padding: 22px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000012;
        }

        label {
            display: block;
            margin-top: 13px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ccd5c8;
            border-radius: 5px;
        }

        button, .button {
            display: inline-block;
            padding: 9px 14px;
            margin-top: 12px;
            border: none;
            border-radius: 5px;
            background: #55a630;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .delete {
            background: #d93636;
        }

        .cancel {
            background: #777;
        }

        .message {
            padding: 12px;
            background: #e2f5df;
            color: #23631f;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .error {
            padding: 12px;
            background: #ffe4e4;
            color: #a00000;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #e5f4df;
        }

        .poster {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }

        .inline-form {
            display: inline-block;
            margin-right: 5px;
        }

        .inline-form button {
            margin-top: 4px;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>Student Hub - Event Management</h1>
    <p>Manage college events and upload event posters.</p>

    <?php if ($message !== ""): ?>
        <div class="message"><?= e($message) ?></div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editEvent ? "Update Event" : "Add New Event" ?></h2>

        <form method="POST" enctype="multipart/form-data">

            <input
                type="hidden"
                name="action"
                value="<?= $editEvent ? "update" : "add" ?>"
            >

            <?php if ($editEvent): ?>
                <input
                    type="hidden"
                    name="event_id"
                    value="<?= (int)$editEvent["event_id"] ?>"
                >
            <?php endif; ?>

            <label for="event_name">Event Name</label>
            <input
                type="text"
                id="event_name"
                name="event_name"
                maxlength="100"
                required
                value="<?= e($editEvent["event_name"] ?? "") ?>"
            >

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                required
            ><?= e($editEvent["description"] ?? "") ?></textarea>

            <label for="event_date">Event Date</label>
            <input
                type="date"
                id="event_date"
                name="event_date"
                required
                value="<?= e($editEvent["event_date"] ?? "") ?>"
            >

            <label for="venue">Venue</label>
            <input
                type="text"
                id="venue"
                name="venue"
                maxlength="100"
                required
                value="<?= e($editEvent["venue"] ?? "") ?>"
            >

            <label for="poster">
                Event Poster (JPG, PNG, WEBP; maximum 2 MB)
            </label>

            <input
                type="file"
                id="poster"
                name="poster"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            >

            <?php if (!empty($editEvent["poster"])): ?>
                <p>Current poster:</p>

                <img
                    class="poster"
                    src="uploads/events12/<?= e($editEvent["poster"]) ?>"
                    alt="Current event poster"
                >

                <p>Leave the poster field empty to keep the existing poster.</p>
            <?php endif; ?>

            <button type="submit">
                <?= $editEvent ? "Update Event" : "Add Event" ?>
            </button>

            <?php if ($editEvent): ?>
                <a class="button cancel" href="event_management12.php">
                    Cancel
                </a>
            <?php endif; ?>

        </form>
    </div>

    <div class="card">
        <h2>All Events</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($result->num_rows > 0): ?>

                    <?php while ($event = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$event["event_id"] ?></td>

                            <td>
                                <?php if (!empty($event["poster"])): ?>
                                    <img
                                        class="poster"
                                        src="uploads/events12/<?= e($event["poster"]) ?>"
                                        alt="Event poster"
                                    >
                                <?php else: ?>
                                    No poster
                                <?php endif; ?>
                            </td>

                            <td><?= e($event["event_name"]) ?></td>
                            <td><?= e($event["event_date"]) ?></td>
                            <td><?= e($event["venue"]) ?></td>

                            <td>
                                <a
                                    class="button"
                                    href="?edit=<?= (int)$event["event_id"] ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    class="inline-form"
                                    method="POST"
                                    onsubmit="return confirm('Delete this event?');"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="event_id"
                                        value="<?= (int)$event["event_id"] ?>"
                                    >

                                    <button class="delete" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6">No events found.</td>
                    </tr>

                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>

<?php
$conn->close();
?>