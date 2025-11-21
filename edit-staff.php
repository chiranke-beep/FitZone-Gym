<?php
session_start();
include 'config.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Please log in as an admin.";
    header("Location: auth.php#login");
    exit();
}

$conn = getDB();

// Fetch staff member details
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$staff = null;

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT user_id, name, email FROM users WHERE user_id = ? AND role = 'staff'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $staff = $result->fetch_assoc();
    }
    $stmt->close();
}

if (!$staff) {
    $_SESSION['message'] = "Staff member not found.";
    $conn->close();
    header("Location: admin-dashboard.php#manage-staff");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Validate inputs
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }

    // Check if email is already in use by another user
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Email is already in use by another user.";
    }
    $stmt->close();

    if (!empty($errors)) {
        $_SESSION['message'] = implode(" ", $errors);
        $conn->close();
        header("Location: edit-staff.php?user_id=$user_id");
        exit();
    }

    // Update the staff member
    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE user_id = ? AND role = 'staff'");
        $stmt->bind_param("sssi", $name, $email, $password, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ? AND role = 'staff'");
        $stmt->bind_param("ssi", $name, $email, $user_id);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Staff member updated successfully!";
            $conn->close();
            header("Location: admin-dashboard.php#manage-staff");
            exit();
        } else {
            $_SESSION['message'] = "No changes made or staff member not found.";
        }
    } else {
        $_SESSION['message'] = "Error updating staff member: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: edit-staff.php?user_id=$user_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff - FitZone Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
    <div class="container">
            <div class="logo">FitZone<span>Fitness</span></div>
            <div class="mobile-home-btn">
                <a href="admin-dashboard.php#manage-staff"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    <style>
   
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-primary {
            background: #ff5722;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: #e64a19;
        }
        .btn-secondary {
            background: #666;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">FitZone<span>Fitness</span></div>
            <div class="mobile-home-btn">
                <a href="admin-dashboard.php#manage-staff"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </header>
    <div class="container">
        <h2>Edit Staff Member</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo strpos($_SESSION['message'], 'success') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form action="edit-staff.php?user_id=<?php echo $user_id; ?>" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($staff['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (optional)</label>
                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
            </div>
            <button type="submit" class="btn btn-primary">Update Staff</button>
            <a href="admin-dashboard.php#manage-staff" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>