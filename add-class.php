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
    $class_name = filter_var($_POST['class_name'], FILTER_SANITIZE_STRING);
    $days = filter_var($_POST['days'], FILTER_SANITIZE_STRING);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $start_datetime = $_POST['start_datetime'];
    $trainer_id = (int)$_POST['trainer_id'];

    // Validate inputs
    if (empty($class_name) || empty($days) || empty($start_time) || empty($end_time) || empty($start_datetime) || !$trainer_id) {
        $_SESSION['message'] = "All fields are required.";
        header("Location: admin-dashboard.php#schedules");
        exit();
    }

    $conn = getDB();

    // Insert the new class
    $stmt = $conn->prepare("INSERT INTO classes (class_name, days, start_time, end_time, start_datetime, trainer_id) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $class_name, $days, $start_time, $end_time, $start_datetime, $trainer_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Class added successfully!";
    } else {
        $_SESSION['message'] = "Error adding class: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: admin-dashboard.php#schedules");
    exit();
}
?>