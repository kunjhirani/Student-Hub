<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_COOKIE["remember_token"])) {
    $token = $_COOKIE["remember_token"];

    if (preg_match('/^[a-f0-9]{64}$/', $token)) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conn = new mysqli("localhost", "root", "", "studenthub");
            $conn->set_charset("utf8mb4");

            $tokenHash = hash("sha256", $token);

            $stmt = $conn->prepare(
                "UPDATE users SET remember_token_hash = NULL
                 WHERE remember_token_hash = ?"
            );

            $stmt->bind_param("s", $tokenHash);
            $stmt->execute();
            $stmt->close();
            $conn->close();
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
        }
    }
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

setcookie(
    "remember_token",
    "",
    [
        "expires" => time() - 3600,
        "path" => "/",
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]
);

session_destroy();

header("Location: login.html");
exit;