<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log received POST data
    error_log("POST data: " . print_r($_POST, true));

    // Validate and sanitize input
    $email = isset($_POST['contact_email']) ? filter_var($_POST['contact_email'], FILTER_SANITIZE_EMAIL) : null;
    $message = isset($_POST['contact_message']) ? filter_var($_POST['contact_message'], FILTER_SANITIZE_STRING) : null;

    // Check for valid input
    if (!$email || !$message || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Please provide a valid email and message.";
        error_log("Validation failed: email=$email, message=$message");
        header("Location: index.php#contact");
        exit();
    }

    $conn = getDB();
    $stmt = $conn->prepare("INSERT INTO contact_messages (email, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $message);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Message sent successfully!";
        error_log("Message inserted: email=$email");
    } else {
        $_SESSION['message'] = "Error sending message: " . $stmt->error;
        error_log("Database error: " . $stmt->error);
    }
    $stmt->close();
    $conn->close();
    header("Location: index.php#contact");
    exit();
} else {
    $_SESSION['message'] = "Invalid request method.";
    error_log("Invalid request method to contact.php");
    header("Location: index.php#contact");
    exit();
}
?>