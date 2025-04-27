<?php
// Database connection
include "../db/config.php";

// Fetch all messages from the database
$sql = "SELECT first_name, last_name, message, submitted_at 
        FROM contact_messages 
        ORDER BY submitted_at DESC";

$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    $messages = [];
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
} else {
    $messages = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Messages</title>
    <link rel="stylesheet" href="../assets/css/contact.css">
    <style>
        /* Example styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        .message-time {
            font-size: 0.85rem;
            color: #888;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">EmpowerCraft Ghana</div>
        <nav class="nav">
            <a href="index.html">Home</a>
            <a href="dashboard.php">Dashboard</a>
        </nav>
    </header>

    <div class="container">
        <h1>All Messages</h1>

        <?php if (!empty($messages)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Message</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?></td>
                            <td><?= htmlspecialchars($message['message']) ?></td>
                            <td class="message-time"><?= date('F j, Y, g:i A', strtotime($message['submitted_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No messages available.</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="services.php">Services</a>
                <a href="index.html">About Us</a>
            </div>
        </div>
        <div style="margin-top: 1rem;">
            &copy; 2025 EmpowerCraft Ghana. All rights reserved.
        </div>
    </footer>
</body>
</html>
