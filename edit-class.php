<?php
session_start();
include 'config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Please log in as an admin.";
    header("Location: auth.php#login");
    exit();
}

// Connect to database
$conn = getDB();
if (!$conn) {
    $_SESSION['message'] = "Database connection failed.";
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}

// Get class ID
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
if ($class_id <= 0) {
    $_SESSION['message'] = "Invalid class ID.";
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}

// Fetch class details
$stmt = $conn->prepare("SELECT class_name, schedule, trainer_id FROM classes WHERE class_id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    $_SESSION['message'] = "Class not found.";
    header("Location: admin-dashboard.php#manage-schedules");
    exit();
}
$class = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = isset($_POST['class_name']) ? trim($_POST['class_name']) : '';
    $schedule = isset($_POST['schedule']) ? trim($_POST['schedule']) : '';
    $trainer_id = isset($_POST['trainer_id']) ? intval($_POST['trainer_id']) : 0;

    if (empty($class_name) || empty($schedule) || $trainer_id <= 0) {
        $_SESSION['message'] = "All fields are required and trainer ID must be valid.";
    } else {
        // Verify trainer_id
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'staff'");
        $stmt->bind_param("i", $trainer_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $stmt->close();
            $_SESSION['message'] = "Invalid trainer ID.";
        } else {
            $stmt->close();
            // Update class
            $stmt = $conn->prepare("UPDATE classes SET class_name = ?, schedule = ?, trainer_id = ? WHERE class_id = ?");
            $stmt->bind_param("ssii", $class_name, $schedule, $trainer_id, $class_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Class updated successfully.";
                header("Location: admin-dashboard.php#manage-schedules");
                $stmt->close();
                $conn->close();
                exit();
            } else {
                $_SESSION['message'] = "Error updating class: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - FitZone Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-primary { background: #ff5722; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo strpos($_SESSION['message'], 'success') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <h2>Edit Class</h2>
        <form action="edit-class.php?class_id=<?php echo $class_id; ?>" method="POST">
            <div class="form-group">
                <label for="class-name">Class Name</label>
                <input type="text" id="class-name" name="class_name" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="class-schedule">Schedule</label>
                <input type="text" id="class-schedule" name="schedule" value="<?php echo htmlspecialchars($class['schedule']); ?>" required>
            </div>
            <div class="form-group">
                <label for="class-trainer">Trainer ID</label>
                <input type="number" id="class-trainer" name="trainer_id" value="<?php echo $class['trainer_id']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Class</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>