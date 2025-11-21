<?php
session_start();
include 'config.php';

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Invalid request method.";
    error_log("Login failed: Invalid request method");
    header("Location: auth.php#login");
    exit();
}

$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

// Log raw POST data
error_log("Login attempt: email=$email, role=$role, raw_post=" . json_encode($_POST));

// Validate input
if (!$email || !$password || !in_array(strtolower($role), ['customer', 'staff', 'admin'])) {
    $_SESSION['message'] = "Invalid input. Please provide email, password, and valid role.";
    error_log("Login failed: Invalid input (email=$email, role=$role)");
    header("Location: auth.php#login");
    exit();
}

// Test database connection and query
$conn = getDB();
if (!$conn) {
    $_SESSION['message'] = "Database connection failed.";
    error_log("Login failed: Database connection failed");
    header("Location: auth.php#login");
    exit();
}

// Debug: Log all users to verify data
$debug_result = $conn->query("SELECT email, role FROM users");
$users = [];
while ($row = $debug_result->fetch_assoc()) {
    $users[] = $row;
}
error_log("Users in database: " . json_encode($users));

// Query user
$stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE email = ? AND role = ?");
if (!$stmt) {
    $_SESSION['message'] = "Database query error.";
    error_log("Login failed: Prepare failed - " . $conn->error);
    header("Location: auth.php#login");
    exit();
}
$stmt->bind_param("ss", $email, $role);
if (!$stmt->execute()) {
    $_SESSION['message'] = "Database execution error.";
    error_log("Login failed: Execute failed - " . $stmt->error);
    header("Location: auth.php#login");
    exit();
}
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = strtolower($user['role']);
        $_SESSION['email'] = $email;
        $_SESSION['message'] = "Login successful!";
        error_log("Login successful: email=$email, role=" . $user['role']);
        $dashboard = $_SESSION['role'] . '-dashboard.php';
        if (file_exists($dashboard)) {
            header("Location: $dashboard");
        } else {
            $_SESSION['message'] = "Dashboard not found for role: " . $_SESSION['role'];
            error_log("Login failed: Dashboard not found ($dashboard)");
            header("Location: auth.php#login");
        }
    } else {
        $_SESSION['message'] = "Invalid password.";
        error_log("Login failed: Invalid password for email=$email, role=$role");
        header("Location: auth.php#login");
    }
} else {
    $_SESSION['message'] = "Invalid email or role.";
    error_log("Login failed: No user found for email=$email, role=$role, rows=" . $result->num_rows);
    header("Location: auth.php#login");
}

$stmt->close();
$conn->close();
exit();
?>