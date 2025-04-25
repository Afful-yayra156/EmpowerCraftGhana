<?php
// Start session for user authentication
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login_process.php');
    exit();
}

// Include database configuration
include '../db/config.php';

// Get current user information
$userId = $_SESSION['user_id'];
$userName = $_SESSION['fname'];
$userRole = $_SESSION['role'];

// Initialize variables
$totalServices = 0;
$totalBookings = 0;
$totalReviews = 0;
$totalEarnings = 0;
$recentMessages = [];

// Function to get user dashboard statistics
function getUserStats($conn, $userId, $userRole) {
    $stats = [
        'totalServices' => 0,
        'totalBookings' => 0,
        'totalReviews' => 0,
        'totalEarnings' => 0,
        'recentMessages' => []
    ];
    
    // Get services count based on user role
    if ($userRole == 'artisan') {
        // Count services offered by the artisan
        $query = "SELECT COUNT(*) as count FROM services WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['totalServices'] = $row['count'];
        }
        $stmt->close();
        
        // Count listings by the artisan
        $query = "SELECT COUNT(*) as count FROM listings WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Add listings to services count
            $stats['totalServices'] += $row['count'];
        }
        $stmt->close();
        
        // Count bookings for artisan's services
        $query = "SELECT COUNT(*) as count FROM bookings WHERE artisan_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['totalBookings'] = $row['count'];
        }
        $stmt->close();
        
        // Calculate total earnings from payments
        $query = "SELECT SUM(amount) as total FROM payments WHERE payee_id = ? AND status = 'completed'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc() && !is_null($row['total'])) {
            $stats['totalEarnings'] = $row['total'];
        }
        $stmt->close();
    } elseif ($userRole == 'client') {
        // For clients, show services they've booked
        $query = "SELECT COUNT(DISTINCT service_id) as count FROM bookings WHERE client_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['totalServices'] = $row['count'];
        }
        $stmt->close();
        
        // Count bookings made by client
        $query = "SELECT COUNT(*) as count FROM bookings WHERE client_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['totalBookings'] = $row['count'];
        }
        $stmt->close();
        
        // Calculate total spent on services
        $query = "SELECT SUM(amount) as total FROM payments WHERE payer_id = ? AND status = 'completed'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc() && !is_null($row['total'])) {
            $stats['totalEarnings'] = $row['total']; // For clients, this is actually total spent
        }
        $stmt->close();
    }
    
    // Count reviews (either by or for the user)
    if ($userRole == 'artisan') {
        $query = "SELECT COUNT(*) as count FROM reviews WHERE reviewed_id = ?";
    } else {
        $query = "SELECT COUNT(*) as count FROM reviews WHERE reviewer_id = ?";
    }
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $stats['totalReviews'] = $row['count'];
    }
    $stmt->close();
    
    // Get recent messages
    $query = "SELECT m.message_id, m.content, m.sent_date, u.first_name, u.last_name, 
             CASE WHEN m.sender_id = ? THEN 'sent' ELSE 'received' END as message_type
             FROM messages m
             JOIN users u ON (m.sender_id = ? AND m.receiver_id = u.user_id) OR (m.receiver_id = ? AND m.sender_id = u.user_id)
             WHERE m.sender_id = ? OR m.receiver_id = ?
             ORDER BY m.sent_date DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiiii', $userId, $userId, $userId, $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Truncate message content if too long
        if (strlen($row['content']) > 100) {
            $row['content'] = substr($row['content'], 0, 97) . '...';
        }
        $stats['recentMessages'][] = $row;
    }
    $stmt->close();
    
    return $stats;
}

// Get user's dashboard data
$userStats = getUserStats($conn, $userId, $userRole);
$totalServices = $userStats['totalServices'];
$totalBookings = $userStats['totalBookings'];
$totalReviews = $userStats['totalReviews'];
$totalEarnings = $userStats['totalEarnings'];
$recentMessages = $userStats['recentMessages'];

// Get new notifications
function getUserNotifications($conn, $userId) {
    $notifications = [];
    
    // Check for recent bookings
    if ($_SESSION['role'] == 'artisan') {
        $query = "SELECT b.booking_id, b.service_id, s.title, u.first_name, u.last_name
                 FROM bookings b
                 JOIN services s ON b.service_id = s.service_id
                 JOIN users u ON b.client_id = u.user_id
                 WHERE b.artisan_id = ? AND b.status = 'pending'
                 ORDER BY b.booking_date DESC LIMIT 3";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $notifications[] = "New booking request for your service: " . $row['title'] . " from " . $row['first_name'] . " " . $row['last_name'];
        }
        $stmt->close();
    } else {
        $query = "SELECT b.booking_id, b.status, s.title, u.first_name, u.last_name
                 FROM bookings b
                 JOIN services s ON b.service_id = s.service_id
                 JOIN users u ON b.artisan_id = u.user_id
                 WHERE b.client_id = ? AND (b.status = 'confirmed' OR b.status = 'completed')
                 AND b.booking_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 ORDER BY b.booking_date DESC LIMIT 3";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $notifications[] = "Your booking for " . $row['title'] . " has been " . $row['status'] . " by " . $row['first_name'] . " " . $row['last_name'];
        }
        $stmt->close();
    }
    
    // Check for new messages
    $query = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc() && $row['count'] > 0) {
        $notifications[] = "You have " . $row['count'] . " unread message(s)";
    }
    $stmt->close();
    
    // Check for new reviews
    if ($_SESSION['role'] == 'artisan') {
        $query = "SELECT COUNT(*) as count FROM reviews r 
                  WHERE r.reviewed_id = ? 
                  AND r.creation_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc() && $row['count'] > 0) {
            $notifications[] = "You have " . $row['count'] . " new review(s)";
        }
        $stmt->close();
    }
    
    return $notifications;
}

$notifications = getUserNotifications($conn, $userId);

// Update last_login timestamp
$updateStmt = $conn->prepare('UPDATE users SET last_login = NOW() WHERE user_id = ?');
$updateStmt->bind_param('i', $userId);
$updateStmt->execute();
$updateStmt->close();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard | EmpowerCraft</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f2fff5;
      display: flex;
    }
    
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background: linear-gradient(135deg, #4CAF50, #2196F3);
      color: white;
      padding: 10px 15px;
    }
    
    .sidebar h2 {
      font-size: 24px;
      margin-bottom: 30px;
    }
    
    .sidebar nav a {
      display: block;
      margin: 10px 0;
      text-decoration: none;
      color: white;
      padding: 10px;
      border-radius: 5px;
      transition: background 0.3s ease;
    }
    
    .sidebar nav a:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }
    
    .notification-badge {
      display: inline-block;
      background-color: #ff4757;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      text-align: center;
      font-size: 12px;
      line-height: 20px;
      margin-left: 5px;
    }
    
    .main {
      margin-left: 250px;
      padding: 40px;
      width: calc(100% - 250px);
    }
    
    .header {
      margin-bottom: 30px;
    }
    
    .header h1 {
      font-size: 28px;
      color: #2e7d32;
      margin-bottom: 5px;
    }
    
    .header p {
      font-size: 16px;
      color: #666;
    }
    
    .summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }
    
    .summary-box {
      background: white;
      padding: 20px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .summary-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    
    .summary-box h3 {
      font-size: 16px;
      color: #666;
      margin-bottom: 10px;
    }
    
    .summary-box p {
      font-size: 22px;
      font-weight: bold;
      color: #2e7d32;
    }
    
    .dashboard-section {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .section-header h2 {
      font-size: 20px;
      color: #2e7d32;
    }
    
    .section-header a {
      color: #2196F3;
      text-decoration: none;
      font-size: 14px;
    }
    
    .messages h2 {
      font-size: 20px;
      margin-bottom: 15px;
      color: #2e7d32;
    }
    
    .message {
      background: #e8f5e9;
      padding: 12px 16px;
      border-left: 4px solid #43a047;
      border-radius: 6px;
      margin-bottom: 10px;
      color: #333;
    }
    
    .message-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
      font-size: 14px;
      color: #666;
    }
    
    .message-content {
      font-size: 15px;
    }
    
    .notification {
      background: #e3f2fd;
      padding: 12px 16px;
      border-left: 4px solid #2196F3;
      border-radius: 6px;
      margin-bottom: 10px;
      color: #333;
    }
  </style>
</head>
<body>
  <aside class="sidebar">
    <h2>EmpowerCraft</h2>
    <nav>
      <a href="dashboard1.php">🏠 Dashboard</a>
      <a href="profile_actions.php">👤 My Profile</a>
      <a href="messages.html">📨 Messages
        <?
        $unreadCount = 0;
        foreach ($notifications as $notification) {
          if (strpos($notification, 'unread message') !== false) {
            preg_match('/(\d+)/', $notification, $matches);
            if (isset($matches[1])) {
              $unreadCount = $matches[1];
            }
          }
        }
        if ($unreadCount > 0): 
        ?>
          <span class="notification-badge"><?php echo $unreadCount; ?></span>
        <?php endif; ?>
      </a>
      <?php if($_SESSION['role'] == 'artisan'): ?>
        <a href="services.html">🛠️ My Services</a>
        <a href="bookings.php">📅 Bookings</a>
      <?php else: ?>
        <a href="find-services.php">🔍 Find Services</a>
        <a href="my-bookings.php">📅 My Bookings</a>
      <?php endif; ?>
      <a href="review_actions.php">⭐ Reviews</a>
      <a href="settings.php">⚙ Settings</a>
      <a href="checkout.php">🚪 Logout</a>
    </nav>
  </aside>
  
  <main class="main">
    <section class="header">
      <h1>Welcome back, <?php echo htmlspecialchars($userName); ?></h1>
      <p>Your EmpowerCraft summary</p>
    </section>
    
    <section class="summary">
      <div class="summary-box">
        <h3>Total Services</h3>
        <p><?php echo number_format($totalServices); ?></p>
      </div>
      <div class="summary-box">
        <h3>Bookings</h3>
        <p><?php echo number_format($totalBookings); ?></p>
      </div>
      <div class="summary-box">
        <h3>Reviews</h3>
        <p><?php echo number_format($totalReviews); ?></p>
      </div>
      <div class="summary-box">
        <h3><?php echo ($_SESSION['role'] == 'artisan') ? 'Earnings' : 'Spent'; ?></h3>
        <p>GH₵ <?php echo number_format($totalEarnings, 2); ?></p>
      </div>
    </section>
    
    <?php if (!empty($notifications)): ?>
    <section class="dashboard-section">
      <div class="section-header">
        <h2>Notifications</h2>
      </div>
      <?php foreach($notifications as $notification): ?>
        <div class="notification"><?php echo htmlspecialchars($notification); ?></div>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>
    
    <section class="dashboard-section">
      <div class="section-header">
        <h2>Recent Messages</h2>
        <a href="messages.php">View all</a>
      </div>
      <?php if(empty($recentMessages)): ?>
        <div class="message">You have no recent messages.</div>
      <?php else: ?>
        <?php foreach($recentMessages as $message): ?>
          <div class="message">
            <div class="message-header">
              <span>
                <?php if($message['message_type'] == 'received'): ?>
                  From: <?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?>
                <?php else: ?>
                  To: <?php echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']); ?>
                <?php endif; ?>
              </span>
              <span><?php echo date('M d, Y H:i', strtotime($message['sent_date'])); ?></span>
            </div>
            <div class="message-content"><?php echo htmlspecialchars($message['content']); ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
    
    <?php if($_SESSION['role'] == 'artisan'): ?>
    <section class="dashboard-section">
      <div class="section-header">
        <h2>Quick Actions</h2>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <a href="add-service.php" style="text-decoration: none;">
          <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; margin-bottom: 10px;">➕</div>
            <div style="font-weight: bold; color: #2e7d32;">Add New Service</div>
          </div>
        </a>
        <a href="manage-bookings.php" style="text-decoration: none;">
          <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; margin-bottom: 10px;">📅</div>
            <div style="font-weight: bold; color: #1565c0;">Manage Bookings</div>
          </div>
        </a>
        <a href="earnings-report.php" style="text-decoration: none;">
          <div style="background: #fff3e0; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; margin-bottom: 10px;">💰</div>
            <div style="font-weight: bold; color: #e65100;">View Earnings</div>
          </div>
        </a>
      </div>
    </section>
    <?php else: ?>
    <section class="dashboard-section">
      <div class="section-header">
        <h2>Quick Actions</h2>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <a href="browse-services.php" style="text-decoration: none;">
          <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; margin-bottom: 10px;">🔍</div>
            <div style="font-weight: bold; color: #2e7d32;">Browse Services</div>
          </div>
        </a>
        <a href="my-bookings.php" style="text-decoration: none;">
          <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; margin-bottom: 10px;">📅</div>
            <div style="font-weight: bold; color: #1565c0;">My Bookings</div>
          </div>
        </a>
        <a href="contact-support.php" style="text-decoration: none;">
          <div style="background: #fff3e0; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 24px; margin-bottom: 10px;">🎧</div>
            <div style="font-weight: bold; color: #e65100;">Contact Support</div>
          </div>
        </a>
      </div>
    </section>
    <?php endif; ?>
  </main>
</body>
</html>