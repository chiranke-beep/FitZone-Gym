<?php
session_start();
include 'config.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Please log in as an admin.";
    header("Location: auth.php#login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Validate inputs
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Check if email is already in use
    $conn = getDB();
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Email is already in use.";
    }
    $stmt->close();

    if (!empty($errors)) {
        $_SESSION['message'] = implode(" ", $errors);
        $conn->close();
        header("Location: admin-dashboard.php#manage-staff");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'staff';

    // Insert the new staff member
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Staff member added successfully!";
    } else {
        $_SESSION['message'] = "Error adding staff member: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: admin-dashboard.php#manage-staff");
    exit();
}

$_SESSION['message'] = "Invalid request.";
header("Location: admin-dashboard.php#manage-staff");
exit();
?>