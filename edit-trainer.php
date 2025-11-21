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
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

// Get user ID
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($user_id <= 0) {
    $_SESSION['message'] = "Invalid trainer ID.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}

// Fetch trainer details
$stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ? AND role = 'staff'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    $_SESSION['message'] = "Trainer not found.";
    header("Location: admin-dashboard.php#manage-trainers");
    exit();
}
$trainer = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($name) || empty($email)) {
        $_SESSION['message'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
    } else {
        // Check if email is taken by another user
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            $_SESSION['message'] = "Email already in use.";
        } else {
            $stmt->close();
            // Update trainer
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $name, $email, $user_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Trainer updated successfully.";
                header("Location: admin-dashboard.php#manage-trainers");
                $stmt->close();
                $conn->close();
                exit();
            } else {
                $_SESSION['message'] = "Error updating trainer: " . $stmt->error;
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
    <title>Edit Trainer - FitZone Fitness Center</title>
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
        <h2>Edit Trainer</h2>
        <form action="edit-trainer.php?user_id=<?php echo $user_id; ?>" method="POST">
            <div class="form-group">
                <label for="trainer-name">Trainer Name</label>
                <input type="text" id="trainer-name" name="name" value="<?php echo htmlspecialchars($trainer['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="trainer-email">Email</label>
                <input type="email" id="trainer-email" name="email" value="<?php echo htmlspecialchars($trainer['email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Trainer</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>