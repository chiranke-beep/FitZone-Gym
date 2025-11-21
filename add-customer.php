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
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

// Retrieve and sanitize inputs
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$membership_plan = isset($_POST['membership_plan']) ? trim($_POST['membership_plan']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

// Validate inputs
$valid_plans = ['basic', 'premium', 'vip'];
if (empty($name) || empty($email) || $role !== 'customer' || !in_array($membership_plan, $valid_plans)) {
    $_SESSION['message'] = "All fields are required and membership plan must be valid.";
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "Invalid email format.";
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

// Connect to database
$conn = getDB();
if (!$conn) {
    $_SESSION['message'] = "Database connection failed.";
    header("Location: admin-dashboard.php#manage-customers");
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
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}
$stmt->close();

// Insert customer
$password = password_hash('default_password', PASSWORD_DEFAULT); // Replace with proper password logic
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, membership_plan) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    $conn->close();
    $_SESSION['message'] = "Query preparation failed: " . $conn->error;
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

$stmt->bind_param("sssss", $name, $email, $password, $role, $membership_plan);
if ($stmt->execute()) {
    $_SESSION['message'] = "Customer added successfully.";
} else {
    $_SESSION['message'] = "Error adding customer: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: admin-dashboard.php#manage-customers");
exit();
?>