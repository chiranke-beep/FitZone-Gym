<?php
// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/fitzone/error.log'); // Adjust for your environment

// Start session
session_start();
include 'config.php';

// Log script access
file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "delete-user.php accessed: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Check admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    $_SESSION['message'] = "Unauthorized access. Please log in as an admin.";
    file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "Unauthorized access attempt\n", FILE_APPEND);
    header("Location: auth.php#login");
    exit();
}

// Check for user_id
if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
    $_SESSION['message'] = "Error: No customer ID provided.";
    file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "No user_id provided\n", FILE_APPEND);
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

$user_id = intval($_POST['user_id']); // Sanitize input
file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "Attempting to delete user_id: $user_id\n", FILE_APPEND);

// Connect to database
$conn = getDB();
if (!$conn) {
    $_SESSION['message'] = "Error: Database connection failed.";
    file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "DB connection failed: " . mysqli_connect_error() . "\n", FILE_APPEND);
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

try {
    // Begin transaction
    $conn->begin_transaction();

    // Check if user exists
    $check_stmt = $conn->prepare("SELECT user_id, role FROM users WHERE user_id = ? AND role = 'customer'");
    if (!$check_stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        $check_stmt->close();
        $conn->rollback();
        $_SESSION['message'] = "Error: No customer found with ID $user_id.";
        file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "No customer found: user_id $user_id\n", FILE_APPEND);
        $conn->close();
        header("Location: admin-dashboard.php#manage-customers");
        exit();
    }
    $check_stmt->close();

    // Delete related records in customer_queries
    $delete_queries = $conn->query("DELETE FROM customer_queries WHERE user_id = $user_id");
    if ($delete_queries === false) {
        throw new Exception("Failed to delete customer_queries records: " . $conn->error);
    }
    file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "Deleted customer_queries for user_id: $user_id\n", FILE_APPEND);

    // Delete related records in bookings (if applicable)
    $delete_bookings = $conn->query("DELETE FROM bookings WHERE user_id = $user_id");
    if ($delete_bookings === false) {
        throw new Exception("Failed to delete bookings records: " . $conn->error);
    }
    file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "Deleted bookings for user_id: $user_id\n", FILE_APPEND);

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'customer'");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Customer deleted successfully.";
        file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "Customer deleted: user_id $user_id\n", FILE_APPEND);
    } else {
        $_SESSION['message'] = "Error: No customer deleted (ID: $user_id).";
        file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "No rows affected: user_id $user_id\n", FILE_APPEND);
    }

    $stmt->close();
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = "Error: " . $e->getMessage();
    file_put_contents('C:/xampp/htdocs/fitzone/debug.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
}

$conn->close();
header("Location: admin-dashboard.php#manage-customers");
exit();
?>