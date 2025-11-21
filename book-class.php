<?php
session_start();
include 'config.php';

// Redirect if not logged in as customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['message'] = "Please log in as a customer.";
    header("Location: auth.php#login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = filter_var($_POST['class_id'], FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    if (!$class_id) {
        $_SESSION['message'] = "Invalid class selection.";
        header("Location: customer-dashboard.php#book-class");
        exit();
    }

    $conn = getDB();

    // Check if class exists and is upcoming
    $stmt = $conn->prepare("SELECT class_id FROM classes WHERE class_id = ? AND start_datetime > NOW()");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $_SESSION['message'] = "Class not available.";
        header("Location: customer-dashboard.php#book-class");
        exit();
    }

    // Check if already booked
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? AND class_id = ?");
    $stmt->bind_param("ii", $user_id, $class_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['message'] = "You have already booked this class.";
        header("Location: customer-dashboard.php#book-class");
        exit();
    }

    // Insert booking with booking_date
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, class_id, booking_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $class_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Class booked successfully!";
    } else {
        $_SESSION['message'] = "Error booking class: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: customer-dashboard.php#book-class");
    exit();
}
?>