<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$appointment_id = $_POST['appointment_id'] ?? '';
$action = $_POST['action'] ?? '';

if (!$appointment_id || !in_array($action, ['confirm', 'cancel'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

$status = $action === 'confirm' ? 'Confirmed' : 'Cancelled';

$conn = getDB();
$stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
$stmt->bind_param("si", $status, $appointment_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log("Failed to update appointment: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
exit();
?>