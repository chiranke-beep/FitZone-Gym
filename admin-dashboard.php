<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    $_SESSION['message'] = "Please log in as an admin.";
    header("Location: auth.php#login");
    exit();
}

$conn = getDB();
if (!$conn) {
    die("Database connection failed.");
}

// Fetch staff members
$staff = $conn->query("SELECT user_id, name, email FROM users WHERE role = 'staff'");

// Fetch current schedules
$schedules = $conn->query("SELECT c.class_id, c.class_name, CONCAT(c.days, ' ', TIME_FORMAT(c.start_time, '%h:%i %p'), ' - ', TIME_FORMAT(c.end_time, '%h:%i %p')) AS schedule, u.name as trainer_name 
                           FROM classes c JOIN users u ON c.trainer_id = u.user_id");

// Fetch all customers
$customers = $conn->query("SELECT user_id, name, email, membership_plan FROM users WHERE role = 'customer'");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitZone Fitness Center</title>
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
            padding-top: 80px; /* Adjust this value based on the header height */
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
        }
        .btn-secondary:hover {
            background: #555;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 0.9em;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
                <h3>Admin Dashboard</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php#home"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="#schedules" class="active"><i class="fas fa-calendar-alt"></i> Schedules</a></li>
                    <li><a href="#manage-staff"><i class="fas fa-users"></i> Manage Staff</a></li>
                    <li><a href="#manage-customers"><i class="fas fa-users"></i> Manage Customers</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="dashboard-content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message <?php echo strpos($_SESSION['message'], 'success') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <section id="schedules" class="dashboard-section">
                <h2>Class Schedules</h2>
                <div class="card">
                    <h3>Add New Class</h3>
                    <form action="add-class.php" method="POST">
                        <div class="form-group">
                            <label for="class-name">Class Name</label>
                            <input type="text" id="class-name" name="class_name" required>
                        </div>
                        <div class="form-group">
                            <label for="days">Days (e.g., Mon, Wed, Fri)</label>
                            <input type="text" id="days" name="days" required>
                        </div>
                        <div class="form-group">
                            <label for="start-time">Start Time</label>
                            <input type="time" id="start-time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="end-time">End Time</label>
                            <input type="time" id="end-time" name="end_time" required>
                        </div>
                        <div class="form-group">
                            <label for="start-datetime">Next Occurrence (Date and Time)</label>
                            <input type="datetime-local" id="start-datetime" name="start_datetime" required>
                        </div>
                        <div class="form-group">
                            <label for="trainer-id">Trainer</label>
                            <select id="trainer-id" name="trainer_id" required>
                                <option value="">Select Trainer</option>
                                <?php 
                                $staff->data_seek(0); // Reset pointer to reuse the result set
                                while ($row = $staff->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $row['user_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Class</button>
                    </form>
                </div>
                <div class="card">
                    <h3>Current Schedules</h3>
                    <?php if ($schedules->num_rows > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Schedule</th>
                                    <th>Trainer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $schedules->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['schedule']); ?></td>
                                        <td><?php echo htmlspecialchars($row['trainer_name']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No schedules available.</p>
                    <?php endif; ?>
                </div>
            </section>
            <section id="manage-staff" class="dashboard-section">
                <h2>Manage Staff</h2>
                <div class="card">
                    <h3>Add New Staff</h3>
                    <form action="add-staff.php" method="POST">
                        <div class="form-group">
                            <label for="staff-name">Name</label>
                            <input type="text" id="staff-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="staff-email">Email</label>
                            <input type="email" id="staff-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="staff-password">Password</label>
                            <input type="password" id="staff-password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Staff</button>
                    </form>
                </div>
                <div class="card">
                    <h3>All Staff</h3>
                    <?php 
                    $staff->data_seek(0); // Reset pointer to reuse the result set
                    if ($staff->num_rows > 0): 
                    ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $staff->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <a href="edit-staff.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-primary btn-small">Edit</a>
                                            <form action="delete-staff.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                                <button type="submit" class="btn btn-secondary btn-small">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No staff members found.</p>
                    <?php endif; ?>
                </div>
            </section>
            <section id="manage-customers" class="dashboard-section">
                <h2>Manage Customers</h2>
                <div class="card">
                    <h3>All Customers</h3>
                    <?php if ($customers->num_rows > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Membership Plan</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $customers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['membership_plan']); ?></td>
                                        <td>
                                            <form action="delete-user.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-small">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No customers found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>