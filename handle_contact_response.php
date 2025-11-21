<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$message_id = $_POST['message_id'] ?? '';
$email = $_POST['email'] ?? '';
$response = $_POST['response'] ?? '';

if (!$message_id || !$email || !$response || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Simple email sending (configure SMTP for production)
$to = $email;
$subject = 'Response from FitZone Fitness Center';
$body = "Dear Customer,\n\n" . htmlspecialchars($response) . "\n\nBest regards,\nFitZone Staff";
$headers = 'From: no-reply@fitzone.com';

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true]);
} else {
    error_log("Failed to send email to $email");
    echo json_encode(['success' => false, 'message' => 'Failed to send email']);
}

exit();
?>