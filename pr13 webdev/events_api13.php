<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function respond($success, $message = "", $data = null, $status = 200)
{
    http_response_code($status);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    respond(false, "Access denied. Admin privileges are required.", null, 403);
}

try {
    $conn = new mysqli("localhost", "root", "", "studenthub");
    $conn->set_charset("utf8mb4");

    $method = $_SERVER["REQUEST_METHOD"];

    if ($method === "GET") {
        $result = $conn->query(
            "SELECT event_id, event_name, description, event_date, venue, poster
             FROM events12
             ORDER BY event_id DESC"
        );

        $events = [];

        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }

        respond(true, "Events retrieved successfully.", $events);
    }

    if ($method !== "POST") {
        respond(false, "Unsupported request method.", null, 405);
    }

    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        respond(false, "Invalid JSON request.", null, 400);
    }

    $action = $input["action"] ?? "";

    if ($action === "add") {
        $name = trim($input["event_name"] ?? "");
        $description = trim($input["description"] ?? "");
        $date = trim($input["event_date"] ?? "");
        $venue = trim($input["venue"] ?? "");

        if ($name === "" || $description === "" || $date === "" || $venue === "") {
            respond(false, "All fields are required.", null, 400);
        }

        if (mb_strlen($name) > 100 || mb_strlen($venue) > 100) {
            respond(false, "Event name and venue must not exceed 100 characters.", null, 400);
        }

        $parsedDate = DateTime::createFromFormat("!Y-m-d", $date);
        $dateErrors = DateTime::getLastErrors();

        if (
            !$parsedDate ||
            $parsedDate->format("Y-m-d") !== $date ||
            ($dateErrors !== false &&
                ($dateErrors["warning_count"] > 0 || $dateErrors["error_count"] > 0))
        ) {
            respond(false, "Enter a valid event date.", null, 400);
        }

        $stmt = $conn->prepare(
            "INSERT INTO events12 (event_name, description, event_date, venue)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $name, $description, $date, $venue);
        $stmt->execute();

        respond(true, "Event added successfully.", [
            "event_id" => $conn->insert_id
        ], 201);
    }

    if ($action === "update") {
        $id = filter_var(
            $input["event_id"] ?? null,
            FILTER_VALIDATE_INT
        );

        $name = trim($input["event_name"] ?? "");
        $description = trim($input["description"] ?? "");
        $date = trim($input["event_date"] ?? "");
        $venue = trim($input["venue"] ?? "");

        if (!$id || $id < 1) {
            respond(false, "Invalid event ID.", null, 400);
        }

        if ($name === "" || $description === "" || $date === "" || $venue === "") {
            respond(false, "All fields are required.", null, 400);
        }

        if (mb_strlen($name) > 100 || mb_strlen($venue) > 100) {
            respond(false, "Event name and venue must not exceed 100 characters.", null, 400);
        }

        $parsedDate = DateTime::createFromFormat("!Y-m-d", $date);
        $dateErrors = DateTime::getLastErrors();

        if (
            !$parsedDate ||
            $parsedDate->format("Y-m-d") !== $date ||
            ($dateErrors !== false &&
                ($dateErrors["warning_count"] > 0 || $dateErrors["error_count"] > 0))
        ) {
            respond(false, "Enter a valid event date.", null, 400);
        }

        $stmt = $conn->prepare(
            "UPDATE events12
             SET event_name = ?, description = ?, event_date = ?, venue = ?
             WHERE event_id = ?"
        );

        $stmt->bind_param("ssssi", $name, $description, $date, $venue, $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare(
                "SELECT event_id FROM events12 WHERE event_id = ?"
            );

            $check->bind_param("i", $id);
            $check->execute();

            if (!$check->get_result()->fetch_assoc()) {
                respond(false, "Event not found.", null, 404);
            }
        }

        respond(true, "Event updated successfully.");
    }

    if ($action === "delete") {
        $id = filter_var(
            $input["event_id"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            respond(false, "Invalid event ID.", null, 400);
        }

        $stmt = $conn->prepare(
            "DELETE FROM events12 WHERE event_id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            respond(false, "Event not found.", null, 404);
        }

        respond(true, "Event deleted successfully.");
    }

    respond(false, "Invalid action.", null, 400);

} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(false, "A server error occurred. Check the PHP error log.", null, 500);
}