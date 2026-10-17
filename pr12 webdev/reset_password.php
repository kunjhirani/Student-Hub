<?php

require_once "db.php";

$username = "mann_hirani";
$newPassword = "Mann@2026Secure";

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "UPDATE users SET password = ? WHERE username = ?"
);

$stmt->execute([$hashedPassword, $username]);

if ($stmt->rowCount() > 0) {
    echo "Password reset successfully.";
} else {
    echo "No account was updated. Check the username.";
}

?>