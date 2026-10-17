<?php
require_once "auth10.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403);
    exit("Access denied. Admin privileges are required.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Event Management API | Student Hub</title>

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
            max-width: 1100px;
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
            margin-top: 12px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ccd5c8;
            border-radius: 5px;
        }

        button {
            padding: 9px 14px;
            margin: 5px 3px 0 0;
            border: none;
            border-radius: 5px;
            background: #55a630;
            color: white;
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
    </style>
</head>

<body>
<div class="container">

    <h1>Student Hub - Event Management</h1>
    <p>Manage events asynchronously using PHP JSON API and Fetch API.</p>

    <div id="message"></div>

    <div class="card">
        <h2 id="formTitle">Add New Event</h2>

        <form id="eventForm">
            <input type="hidden" id="event_id">

            <label for="event_name">Event Name</label>
            <input type="text" id="event_name" maxlength="100" required>

            <label for="description">Description</label>
            <textarea id="description" rows="4" required></textarea>

            <label for="event_date">Event Date</label>
            <input type="date" id="event_date" required>

            <label for="venue">Venue</label>
            <input type="text" id="venue" maxlength="100" required>

            <button type="submit" id="submitButton">Add Event</button>
            <button type="button" class="cancel" id="cancelButton" hidden>
                Cancel Edit
            </button>
        </form>
    </div>

    <div class="card">
        <h2>All Events</h2>

        <button type="button" id="refreshButton">Refresh Events</button>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="eventsTable">
                    <tr>
                        <td colspan="6">Loading events...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
const apiUrl = "events_api13.php";

const eventForm = document.getElementById("eventForm");
const eventsTable = document.getElementById("eventsTable");
const messageBox = document.getElementById("message");

function showMessage(message, isError = false) {
    messageBox.replaceChildren();

    const div = document.createElement("div");
    div.className = isError ? "error" : "message";
    div.textContent = message;

    messageBox.appendChild(div);
}

async function apiRequest(method = "GET", payload = null) {
    const options = {
        method,
        headers: {
            "Accept": "application/json"
        }
    };

    if (payload !== null) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(payload);
    }

    const response = await fetch(apiUrl, options);

    let result;

    try {
        result = await response.json();
    } catch {
        throw new Error("The server returned an invalid response.");
    }

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed.");
    }

    return result;
}

async function loadEvents() {
    eventsTable.replaceChildren();

    const loadingRow = document.createElement("tr");
    const loadingCell = document.createElement("td");
    loadingCell.colSpan = 6;
    loadingCell.textContent = "Loading events...";
    loadingRow.appendChild(loadingCell);
    eventsTable.appendChild(loadingRow);

    try {
        const result = await apiRequest("GET");

        eventsTable.replaceChildren();

        if (result.data.length === 0) {
            const row = document.createElement("tr");
            const cell = document.createElement("td");

            cell.colSpan = 6;
            cell.textContent = "No events found.";

            row.appendChild(cell);
            eventsTable.appendChild(row);
            return;
        }

        result.data.forEach(event => {
            const row = document.createElement("tr");

            [
                event.event_id,
                event.event_name,
                event.description,
                event.event_date,
                event.venue
            ].forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value ?? "";
                row.appendChild(cell);
            });

            const actionsCell = document.createElement("td");

            const editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.addEventListener("click", () => editEvent(event));

            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.className = "delete";
            deleteButton.addEventListener("click", () => deleteEvent(event.event_id));

            actionsCell.append(editButton, deleteButton);
            row.appendChild(actionsCell);
            eventsTable.appendChild(row);
        });
    } catch (error) {
        eventsTable.replaceChildren();

        const row = document.createElement("tr");
        const cell = document.createElement("td");

        cell.colSpan = 6;
        cell.textContent = error.message;

        row.appendChild(cell);
        eventsTable.appendChild(row);
    }
}

function editEvent(event) {
    document.getElementById("event_id").value = event.event_id;
    document.getElementById("event_name").value = event.event_name;
    document.getElementById("description").value = event.description;
    document.getElementById("event_date").value = event.event_date;
    document.getElementById("venue").value = event.venue;

    document.getElementById("formTitle").textContent = "Update Event";
    document.getElementById("submitButton").textContent = "Update Event";
    document.getElementById("cancelButton").hidden = false;

    document.getElementById("eventForm").scrollIntoView({
        behavior: "smooth"
    });
}

function resetForm() {
    eventForm.reset();
    document.getElementById("event_id").value = "";
    document.getElementById("formTitle").textContent = "Add New Event";
    document.getElementById("submitButton").textContent = "Add Event";
    document.getElementById("cancelButton").hidden = true;
}

eventForm.addEventListener("submit", async function(event) {
    event.preventDefault();

    const id = document.getElementById("event_id").value;

    const payload = {
        action: id ? "update" : "add",
        event_name: document.getElementById("event_name").value.trim(),
        description: document.getElementById("description").value.trim(),
        event_date: document.getElementById("event_date").value,
        venue: document.getElementById("venue").value.trim()
    };

    if (id) {
        payload.event_id = Number(id);
    }

    try {
        const result = await apiRequest("POST", payload);

        showMessage(result.message);
        resetForm();
        await loadEvents();
    } catch (error) {
        showMessage(error.message, true);
    }
});

async function deleteEvent(id) {
    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    try {
        const result = await apiRequest("POST", {
            action: "delete",
            event_id: Number(id)
        });

        showMessage(result.message);
        await loadEvents();
    } catch (error) {
        showMessage(error.message, true);
    }
}

document.getElementById("cancelButton").addEventListener("click", resetForm);
document.getElementById("refreshButton").addEventListener("click", loadEvents);

loadEvents();
</script>

</body>
</html>