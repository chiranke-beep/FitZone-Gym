<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'customer') {
        header("Location: customer-dashboard.php");
    } elseif ($_SESSION['role'] === 'staff') {
        header("Location: staff-dashboard.php");
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: admin-dashboard.php");
    }
    exit();
}

// Display any session messages
if (isset($_SESSION['message'])) {
    $message = htmlspecialchars($_SESSION['message']);
    unset($_SESSION['message']);
} else {
    $message = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone Fitness Center - Login</title>
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
    <header class="header">
        <div class="container">
            <div class="logo">FitZone<span>Fitness</span></div>
            <div class="mobile-home-btn">
                <a href="index.php#home"><i class="fas fa-home"></i></a>
            </div>
        </div>
    </header>
    <section id="login" class="auth-section">
        <div class="container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Welcome to <span>FitZone</span></h1>
                    <p>Unlock your fitness potential today</p>
                </div>
                <?php if ($message): ?>
                    <div class="auth-message <?php echo strpos($message, 'successful') !== false ? 'success' : ''; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <div class="auth-tabs">
                    <button class="tab-btn active" data-tab="customer">Customer</button>
                    <button class="tab-btn" data-tab="staff">Staff</button>
                    <button class="tab-btn" data-tab="admin">Admin</button>
                </div>
                <div id="select-role-message">Please select your role to login.</div>
                <div class="auth-form" id="customer-login">
                    <div class="login-form">
                        <form id="customer-login-form" action="login.php" method="POST">
                            <input type="hidden" name="role" value="customer">
                            <div class="form-group">
                                <label for="customer-email">Email</label>
                                <input type="email" id="customer-email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="customer-password">Password</label>
                                <input type="password" id="customer-password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="form-options">
                                <a href="#" class="forgot-password">Forgot?</a>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                            <p class="form-footer">New here? <a href="signup.php">Create Account</a></p>
                        </form>
                    </div>
                    <div class="forgot-password-form" style="display: none;">
                        <h3>Reset Password</h3>
                        <form id="customer-reset-form" action="reset-password.php" method="POST">
                            <input type="hidden" name="role" value="customer">
                            <div class="form-group">
                                <label for="customer-reset-email">Email</label>
                                <input type="email" id="customer-reset-email" name="reset_email" placeholder="Enter your email" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Reset Link</button>
                            <a href="#" class="back-to-login">Back to Login</a>
                        </form>
                    </div>
                </div>
                <div class="auth-form" id="staff-login" style="display: none;">
                    <div class="login-form">
                        <form id="staff-login-form" action="login.php" method="POST">
                            <input type="hidden" name="role" value="staff">
                            <div class="form-group">
                                <label for="staff-email">Email</label>
                                <input type="email" id="staff-email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="staff-password">Password</label>
                                <input type="password" id="staff-password" name="password" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                            <p class="form-footer">Need help? Contact Admin</p>
                        </form>
                    </div>
                </div>
                <div class="auth-form" id="admin-login" style="display: none;">
                    <div class="login-form">
                        <form id="admin-login-form" action="login.php" method="POST">
                            <input type="hidden" name="role" value="admin">
                            <div class="form-group">
                                <label for="admin-email">Email</label>
                                <input type="email" id="admin-email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="admin-password">Password</label>
                                <input type="password" id="admin-password" name="password" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="go-home-container">
                <a href="index.php#home" class="btn btn-primary">Go Home</a>
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="js/auth.js"></script>
</body>
</html>