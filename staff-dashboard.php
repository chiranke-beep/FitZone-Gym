<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: auth.php#login");
    exit();
}

$conn = getDB();

// Fetch contact messages
$contact_messages = $conn->query("SELECT * FROM contact_messages");

// Fetch customer inquiries
$customer_inquiries = $conn->query("SELECT cq.query_id, u.name, cq.query, cq.date FROM customer_queries cq JOIN users u ON cq.user_id = u.user_id");

// Fetch customer bookings
$bookings = $conn->query("SELECT b.booking_id, b.booking_date, u.name AS customer, c.class_name, t.name AS trainer 
                          FROM bookings b 
                          JOIN users u ON b.user_id = u.user_id 
                          JOIN classes c ON b.class_id = c.class_id 
                          JOIN users t ON c.trainer_id = t.user_id 
                          ORDER BY b.booking_date DESC");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - FitZone Fitness Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .message {
            max-width: 500px;
            margin: 0 auto 15px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            position: relative;
        }
        .modal-content h3 {
            margin-bottom: 20px;
            color: #333;
        }
        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5em;
            cursor: pointer;
            color: #333;
        }
        .modal-content form {
            display: flex;
            flex-direction: column;
        }
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
            font-family: 'Poppins', sans-serif;
        }
        .modal-content button {
            background: #f39c12;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-content button:hover {
            background: #e67e22;
        }
        .sidebar-nav ul li a.home-link i {
            margin-right: 10px;
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
                <h3>Staff Dashboard</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php#home" class="home-link"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="#bookings" class="active"><i class="fas fa-dumbbell"></i> Customer Bookings</a></li>
                    <li><a href="#queries"><i class="fas fa-question-circle"></i> Customer Queries</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="dashboard-content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message <?php echo strpos($_SESSION['message'], 'successful') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <section id="bookings" class="dashboard-section">
                <h2>Customer Bookings</h2>
                <div class="card">
                    <h3>Recent Bookings</h3>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Class</th>
                                <th>Trainer</th>
                                <th>Booking Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['customer']); ?></td>
                                <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['trainer']); ?></td>
                                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <section id="queries" class="dashboard-section">
                <h2>Customer Queries</h2>
                <div class="card">
                    <h3>Contact Us Messages</h3>
                    <table class="dashboard-table" id="contact-messages-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $contact_messages->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['message']); ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td>
                                    <button class="btn btn-primary btn-small respond-contact" 
                                            data-message-id="<?php echo $row['message_id']; ?>" 
                                            data-email="<?php echo htmlspecialchars($row['email']); ?>">Respond</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card">
                    <h3>Customer Inquiries</h3>
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Inquiry</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $customer_inquiries->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['query']); ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td>
                                    <button class="btn btn-primary btn-small respond-inquiry" 
                                            data-query-id="<?php echo $row['query_id']; ?>">Respond</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Modals -->
    <div class="modal" id="contact-response-modal">
        <div class="modal-content">
            <span class="close">×</span>
            <h3>Respond to Contact Message</h3>
            <form id="contact-response-form">
                <input type="hidden" name="message_id" id="contact-message-id">
                <input type="hidden" name="email" id="contact-email">
                <textarea name="response" placeholder="Enter your response" required></textarea>
                <button type="submit">Send Response</button>
            </form>
        </div>
    </div>
    <div class="modal" id="inquiry-response-modal">
        <div class="modal-content">
            <span class="close">×</span>
            <h3>Respond to Inquiry</h3>
            <form id="inquiry-response-form">
                <input type="hidden" name="query_id" id="inquiry-query-id">
                <textarea name="response" placeholder="Enter your response" required></textarea>
                <button type="submit">Submit Response</button>
            </form>
        </div>
    </div>

    <script>
        // Modal handling
        const modals = document.querySelectorAll('.modal');
        const closeButtons = document.querySelectorAll('.modal .close');

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                modals.forEach(modal => modal.style.display = 'none');
            });
        });

        // Contact message response
        document.querySelectorAll('.respond-contact').forEach(button => {
            button.addEventListener('click', () => {
                const messageId = button.dataset.messageId;
                const email = button.dataset.email;
                document.getElementById('contact-message-id').value = messageId;
                document.getElementById('contact-email').value = email;
                openModal('contact-response-modal');
            });
        });

        // Inquiry response
        document.querySelectorAll('.respond-inquiry').forEach(button => {
            button.addEventListener('click', () => {
                const queryId = button.dataset.queryId;
                document.getElementById('inquiry-query-id').value = queryId;
                openModal('inquiry-response-modal');
            });
        });

        // Form submissions
        document.getElementById('contact-response-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('handle_contact_response.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                closeModal('contact-response-modal');
                if (data.success) {
                    alert('Response sent successfully!');
                } else {
                    alert(data.message || 'Error sending response.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending response.');
            });
        });

        document.getElementById('inquiry-response-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('staff_id', '<?php echo $_SESSION['user_id']; ?>');
            fetch('handle_inquiry_response.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                closeModal('inquiry-response-modal');
                if (data.success) {
                    alert('Response submitted successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Error submitting response.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting response.');
            });
        });
    </script>
</body>
</html>