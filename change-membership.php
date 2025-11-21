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
    $membership_plan = filter_var($_POST['membership_plan'], FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user_id'];

    // Validate membership plan
    $valid_plans = ['basic', 'premium', 'vip'];
    if (!in_array($membership_plan, $valid_plans)) {
        $_SESSION['message'] = "Invalid membership plan selected.";
        header("Location: customer-dashboard.php#change-membership");
        exit();
    }

    $conn = getDB();
    $stmt = $conn->prepare("UPDATE users SET membership_plan = ? WHERE user_id = ?");
    $stmt->bind_param("si", $membership_plan, $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Membership plan updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating membership: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: customer-dashboard.php#change-membership");
    exit();
}
?>