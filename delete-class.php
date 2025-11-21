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
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}

// Retrieve and validate class ID
$class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
if ($class_id <= 0) {
    $_SESSION['message'] = "Invalid class ID.";
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}

// Connect to database
$conn = getDB();
if (!$conn) {
    $_SESSION['message'] = "Database connection failed.";
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}

// Verify class exists
$stmt = $conn->prepare("SELECT class_id FROM classes WHERE class_id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    $conn->close();
    $_SESSION['message'] = "Class not found.";
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}
$stmt->close();

// Delete class
$stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
if (!$stmt) {
    $conn->close();
    $_SESSION['message'] = "Query preparation failed: " . $conn->error;
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}

$stmt->bind_param("i", $class_id);
if ($stmt->execute()) {
    $_SESSION['message'] = "Class deleted successfully.";
} else {
    $_SESSION['message'] = "Error deleting class: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: admin-dashboard.php#manage-schedules");
exit();
?>