<?php
session_start();
include 'config.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Please log in as an admin.";
    header("Location: auth.php#login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];

    if ($user_id <= 0) {
        $_SESSION['message'] = "Invalid staff ID.";
        header("Location: admin-dashboard.php#manage-staff");
        exit();
    }

    $conn = getDB();

    // Check if the staff member is assigned to any classes
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM classes WHERE trainer_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $class_count = $result['count'];

    if ($class_count > 0) {
        $_SESSION['message'] = "Cannot delete staff member. They are assigned to $class_count class(es). Reassign or delete the classes first.";
        $stmt->close();
        $conn->close();
        header("Location: admin-dashboard.php#manage-staff");
        exit();
    }

    // Delete the staff member
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'staff'");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Staff member deleted successfully!";
        } else {
            $_SESSION['message'] = "Staff member not found or not a staff role.";
        }
    } else {
        $_SESSION['message'] = "Error deleting staff member: " . $stmt->error;
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