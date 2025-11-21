<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

// Check authorization
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Retrieve and validate inputs
$query_id = isset($_POST['query_id']) ? intval($_POST['query_id']) : 0;
$staff_id = isset($_POST['staff_id']) ? intval($_POST['staff_id']) : 0;
$response_message = isset($_POST['response']) ? htmlspecialchars(trim($_POST['response'])) : '';

if ($query_id <= 0 || $staff_id <= 0 || empty($response_message)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Connect to database
$conn = getDB();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Verify query_id exists
$stmt = $conn->prepare("SELECT query_id FROM customer_queries WHERE query_id = ?");
$stmt->bind_param("i", $query_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Invalid query ID']);
    exit();
}
$stmt->close();

// Insert response
$stmt = $conn->prepare("INSERT INTO query_responses (query_id, staff_id, response_message, created_at) VALUES (?, ?, ?, NOW())");
if (!$stmt) {
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("iis", $query_id, $staff_id, $response_message);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Response submitted successfully']);
    exit();
} else {
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    error_log("Failed to save inquiry response: " . $error);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $error]);
    exit();
}
?>