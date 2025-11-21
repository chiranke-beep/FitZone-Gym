<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = filter_var($_POST['customer-name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['customer-email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['customer-password'];
    $confirm_password = $_POST['customer-confirm-password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        header("Location: signup.php");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        header("Location: signup.php");
        exit();
    }

    // Connect to database
    $conn = getDB();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['message'] = "Email already in use.";
        header("Location: signup.php");
        exit();
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, membership_plan) VALUES (?, ?, ?, 'customer', 'basic')");
        $stmt->bind_param("sss", $name, $email, $hashed_password); // Use hashed password
        if ($stmt->execute()) {
            $_SESSION['message'] = "Signup successful! Please login.";
            header("Location: auth.php#login");
            exit();
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            header("Location: signup.php");
            exit();
        }
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone Fitness Center - Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .auth-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .auth-message.success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <header class="header">
        <div class="container">
            <div class="logo">FitZone<span>Fitness</span></div>
            <div class="mobile-home-btn">
                <a href="index.php#home"><i class="fas fa-home"></i></a>
            </div>
        </div>
    </header>

    <!-- Sign Up Section -->
    <section id="signup" class="auth-section">
        <div class="container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Join <span>FitZone</span> Today</h1>
                    <p>Create your account to start your fitness journey</p>
                </div>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="auth-message <?php echo strpos($_SESSION['message'], 'successful') !== false ? 'success' : ''; ?>">
                        <?php echo htmlspecialchars($_SESSION['message']); ?>
                        <?php unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
                <div class="auth-form active" id="customer-signup">
                    <div class="signup-form">
                        <form id="customer-signup-form" action="signup.php" method="POST">
                            <div class="form-group">
                                <label for="customer-name">Full Name</label>
                                <input type="text" id="customer-name" name="customer-name" placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="customer-email">Email</label>
                                <input type="email" id="customer-email" name="customer-email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="customer-password">Password</label>
                                <input type="password" id="customer-password" name="customer-password" placeholder="Create a password" required>
                            </div>
                            <div class="form-group">
                                <label for="customer-confirm-password">Confirm Password</label>
                                <input type="password" id="customer-confirm-password" name="customer-confirm-password" placeholder="Confirm your password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Sign Up</button>
                            <p class="form-footer">Already have an account? <a href="auth.php#login">Login</a></p>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Go Home Button -->
            <div class="go-home-container">
                <a href="index.php#home" class="btn btn-primary">Go Home</a>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
</body>
</html>