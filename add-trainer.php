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

// Retrieve and sanitize inputs
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

// Validate inputs
if (empty($name) || empty($email) || $role !== 'staff') {
    $_SESSION['message'] = "All fields are required and role must be staff.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "Invalid email format.";
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

// Check if email exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    $conn->close();
    $_SESSION['message'] = "Email already exists.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}
$stmt->close();

// Insert trainer
$password = password_hash('default_password', PASSWORD_DEFAULT); // Replace with proper password logic
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    $conn->close();
    $_SESSION['message'] = "Query preparation failed: " . $conn->error;
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

$stmt->bind_param("ssss", $name, $email, $password, $role);
if ($stmt->execute()) {
    $_SESSION['message'] = "Trainer added successfully.";
} else {
    $_SESSION['message'] = "Error adding trainer: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: admin-dashboard.php#manage-trainers");
exit();
?>