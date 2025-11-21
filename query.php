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
    $query_message = filter_var($_POST['query_message'], FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user_id'];

    if (empty($query_message)) {
        $_SESSION['message'] = "Query cannot be empty.";
        header("Location: customer-dashboard.php#send-query");
        exit();
    }

    $conn = getDB();
    $stmt = $conn->prepare("INSERT INTO customer_queries (user_id, query) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $query_message);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Query sent successfully!";
    } else {
        $_SESSION['message'] = "Error sending query: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: customer-dashboard.php#send-query");
    exit();
}
?>