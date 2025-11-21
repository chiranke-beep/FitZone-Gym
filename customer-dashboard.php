<?php
session_start();
include 'config.php';

// Redirect if not logged in as customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['message'] = "Please log in as a customer.";
    header("Location: auth.php#login");
    exit();
}

// Display session messages
$message = isset($_SESSION['message']) ? htmlspecialchars($_SESSION['message']) : null;
unset($_SESSION['message']);

$conn = getDB();
if (!$conn) {
    die("Database connection failed.");
}
$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, membership_plan FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch all classes
$stmt = $conn->prepare("SELECT c.class_id, c.class_name, CONCAT(c.days, ' ', TIME_FORMAT(c.start_time, '%h:%i %p'), ' - ', TIME_FORMAT(c.end_time, '%h:%i %p')) AS schedule, u.name as trainer_name 
                       FROM classes c JOIN users u ON c.trainer_id = u.user_id");
$stmt->execute();
$upcoming_classes = $stmt->get_result();

// Fetch user queries
$stmt = $conn->prepare("SELECT * FROM customer_queries WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$queries = $stmt->get_result();

// Fetch staff responses
$stmt = $conn->prepare("SELECT qr.response_id, qr.query_id, qr.response_message, qr.created_at, u.name as staff_name 
                       FROM query_responses qr 
                       JOIN customer_queries cq ON qr.query_id = cq.query_id 
                       JOIN users u ON qr.staff_id = u.user_id 
                       WHERE cq.user_id = ? 
                       ORDER BY qr.created_at DESC");
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}
$responses = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - FitZone Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px);
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: #fff;
            padding: 20px;
        }
        .sidebar-header h3 {
            margin: 0 0 20px;
            font-size: 1.5em;
        }
        .sidebar-nav ul {
            list-style: none;
            padding: 0;
        }
        .sidebar-nav li {
            margin-bottom: 10px;
        }
        .sidebar-nav a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: #3498db;
        }
        .sidebar-nav i {
            margin-right: 10px;
        }
        .dashboard-content {
            flex: 1;
            padding: 20px;
        }
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
        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .dashboard-table th, .dashboard-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .dashboard-table th {
            background: #ff5722;
            color: #fff;
        }
        .btn-primary {
            background: #ff5722;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 0.9em;
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
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Customer Dashboard</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php#home"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="#overview" class="active"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
                    <li><a href="#book-class"><i class="fas fa-dumbbell"></i> Book a Class</a></li>
                    <li><a href="#change-membership"><i class="fas fa-address-card"></i> Change Membership</a></li>
                    <li><a href="#send-query"><i class="fas fa-question-circle"></i> Send Query</a></li>
                    <li><a href="#view-responses"><i class="fas fa-envelope-open-text"></i> View Responses</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="dashboard-content">
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <section id="overview" class="dashboard-section">
                <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                <div class="overview-grid">
                    <div class="card">
                        <h3><?php echo htmlspecialchars($user['membership_plan']); ?></h3>
                        <p>Membership Plan</p>
                        <i class="fas fa-address-card overview-icon"></i>
                    </div>
                    <div class="card">
                        <h3><?php echo $upcoming_classes->num_rows; ?></h3>
                        <p>Upcoming Classes</p>
                        <i class="fas fa-dumbbell overview-icon"></i>
                    </div>
                    <div class="card">
                        <h3><?php echo $queries->num_rows; ?></h3>
                        <p>Pending Queries</p>
                        <i class="fas fa-question-circle overview-icon"></i>
                    </div>
                </div>
            </section>
            <section id="book-class" class="dashboard-section">
                <h2>Book a Class</h2>
                <div class="card">
                    <h3>Available Classes</h3>
                    <?php if ($upcoming_classes->num_rows > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Schedule</th>
                                    <th>Trainer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $upcoming_classes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['schedule']); ?></td>
                                        <td><?php echo htmlspecialchars($row['trainer_name']); ?></td>
                                        <td>
                                            <form action="book-class.php" method="POST">
                                                <input type="hidden" name="class_id" value="<?php echo $row['class_id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-small">Book</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No classes available.</p>
                    <?php endif; ?>
                </div>
            </section>
            <section id="change-membership" class="dashboard-section">
                <h2>Change Membership</h2>
                <div class="card">
                    <h3>Current Membership</h3>
                    <p><?php echo htmlspecialchars($user['membership_plan']); ?></p>
                </div>
                <div class="card">
                    <h3>Update Membership Plan</h3>
                    <form id="change-membership-form" action="change-membership.php" method="POST">
                        <div class="form-group">
                            <label for="membership-plan">Select New Plan</label>
                            <select id="membership-plan" name="membership_plan" required>
                                <option value="">Select Plan</option>
                                <option value="basic">Basic (LKR 3000/mo)</option>
                                <option value="premium">Premium (LKR 7500/mo)</option>
                                <option value="vip">VIP (LKR 15000/mo)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Plan</button>
                    </form>
                </div>
            </section>
            <section id="send-query" class="dashboard-section">
                <h2>Send a Query</h2>
                <div class="card">
                    <form id="query-form" action="query.php" method="POST">
                        <div class="form-group">
                            <label for="query-message">Your Query</label>
                            <textarea id="query-message" name="query_message" rows="4" placeholder="Type your query here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Query</button>
                    </form>
                </div>
            </section>
            <section id="view-responses" class="dashboard-section">
                <h2>Staff Responses</h2>
                <div class="card">
                    <h3>Your Query Responses</h3>
                    <?php if ($responses->num_rows > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Query ID</th>
                                    <th>Staff Name</th>
                                    <th>Response</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $responses->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['query_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['response_message']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No responses available.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>