<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required POST variables exist
    if (!isset($_POST['reset_email']) || !isset($_POST['role'])) {
        $_SESSION['message'] = "Missing email or role information.";
        header("Location: auth.php#login");
        exit();
    }

    $email = filter_var($_POST['reset_email'], FILTER_SANITIZE_EMAIL);
    $role = $_POST['role'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        header("Location: auth.php#login");
        exit();
    }

    $conn = getDB();
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // In a real application, generate a reset token and send an email
        $_SESSION['message'] = "A password reset link has been sent to your email.";
    } else {
        $_SESSION['message'] = "No account found with that email and role.";
    }
    $stmt->close();
    $conn->close();
    header("Location: auth.php#login");
    exit();
} else {
    $_SESSION['message'] = "Invalid request method.";
    header("Location: auth.php#login");
    exit();
}
?>