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
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

// Get user ID
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($user_id <= 0) {
    $_SESSION['message'] = "Invalid customer ID.";
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}

// Fetch customer details
$stmt = $conn->prepare("SELECT name, email, membership_plan FROM users WHERE user_id = ? AND role = 'customer'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    $_SESSION['message'] = "Customer not found.";
    header("Location: admin-dashboard.php#manage-customers");
    exit();
}
$customer = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $membership_plan = isset($_POST['membership_plan']) ? trim($_POST['membership_plan']) : '';

    $valid_plans = ['basic', 'premium', 'vip'];
    if (empty($name) || empty($email) || !in_array($membership_plan, $valid_plans)) {
        $_SESSION['message'] = "All fields are required and membership plan must be valid.";
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
            // Update customer
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, membership_plan = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $name, $email, $membership_plan, $user_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Customer updated successfully.";
                header("Location: admin-dashboard.php#manage-customers");
                $stmt->close();
                $conn->close();
                exit();
            } else {
                $_SESSION['message'] = "Error updating customer: " . $stmt->error;
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
    <title>Edit Customer - FitZone Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
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
        <h2>Edit Customer</h2>
        <form action="edit-customer.php?user_id=<?php echo $user_id; ?>" method="POST">
            <div class="form-group">
                <label for="customer-name">Customer Name</label>
                <input type="text" id="customer-name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="customer-email">Email</label>
                <input type="email" id="customer-email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="customer-membership">Membership Plan</label>
                <select id="customer-membership" name="membership_plan" required>
                    <option value="basic" <?php if ($customer['membership_plan'] == 'basic') echo 'selected'; ?>>Basic (LKR 3000/mo)</option>
                    <option value="premium" <?php if ($customer['membership_plan'] == 'premium') echo 'selected'; ?>>Premium (LKR 7500/mo)</option>
                    <option value="vip" <?php if ($customer['membership_plan'] == 'vip') echo 'selected'; ?>>VIP (LKR 15000/mo)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>