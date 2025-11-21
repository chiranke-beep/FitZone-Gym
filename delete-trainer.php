<?php
session_start();
include 'config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Please log in as an admin.";
    header("Location: auth.php#login");
    exit();
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Invalid request method.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

// Retrieve and validate user ID
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
if ($user_id <= 0) {
    $_SESSION['message'] = "Invalid trainer ID.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

// Connect to database
$conn = getDB();
if (!$conn) {
    $_SESSION['message'] = "Database connection failed.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

// Verify trainer exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'staff'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    $conn->close();
    $_SESSION['message'] = "Trainer not found.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}
$stmt->close();

// Check if trainer is assigned to classes
$stmt = $conn->prepare("SELECT class_id FROM classes WHERE trainer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    $conn->close();
    $_SESSION['message'] = "Cannot delete trainer assigned to classes.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}
$stmt->close();

// Delete trainer
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
if (!$stmt) {
    $conn->close();
    $_SESSION['message'] = "Query preparation failed: " . $conn->error;
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $_SESSION['message'] = "Trainer deleted successfully.";
} else {
    $_SESSION['message'] = "Error deleting trainer: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: admin-dashboard.php#manage-trainers");
exit();
?>