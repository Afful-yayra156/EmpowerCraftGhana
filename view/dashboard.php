<?php
// Start session for user authentication
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database configuration
include '../db/config.php';

// Get current user information
$userId = $_SESSION['user_id'];
$userName = $_SESSION['fname'];
$userRole = $_SESSION['role'];

// Initialize statistics variables
$totalUsers = 0;
$verifiedArtisans = 0;
$completedTasks = 0;
$satisfactionRate = 0;

// Function to get dashboard statistics based on user role
function getDashboardStats($conn, $userId, $userRole) {
    $stats = [
        'totalUsers' => 0,
        'verifiedArtisans' => 0,
        'completedTasks' => 0,
        'satisfactionRate' => 0,
        'pendingBookings' => 0,
        'myListings' => 0,
        'myServices' => 0,
        'unreadMessages' => 0,
        'recentReviews' => []
    ];
    
    // Get total users count
    $query = "SELECT COUNT(*) as count FROM users WHERE account_status = 'active'";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $stats['totalUsers'] = $row['count'];
    }
    
    // Get verified artisans count
    $query = "SELECT COUNT(*) as count FROM users WHERE user_type = 'artisan' AND is_verified = 1";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $stats['verifiedArtisans'] = $row['count'];
    }
    
    // Get completed bookings/tasks count
    $query = "SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $stats['completedTasks'] = $row['count'];
    }
    
    // Calculate average satisfaction rate from completed bookings with reviews
    $query = "SELECT AVG(r.rating) as avg_rating FROM reviews r 
              JOIN bookings b ON r.reference_id = b.booking_id 
              WHERE r.reference_type = 'service' AND b.status = 'completed'";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc() && !is_null($row['avg_rating'])) {
        $stats['satisfactionRate'] = round($row['avg_rating'] * 20, 1); // Convert 5-scale to percentage
    } else {
        $stats['satisfactionRate'] = 98; // Default value if no data
    }
    
    // User-specific statistics
    if ($userRole == 'artisan') {
        // Get pending bookings for artisan
        $query = "SELECT COUNT(*) as count FROM bookings WHERE artisan_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['pendingBookings'] = $row['count'];
        }
        $stmt->close();
        
        // Get artisan's services count
        $query = "SELECT COUNT(*) as count FROM services WHERE user_id = ? AND status = 'active'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['myServices'] = $row['count'];
        }
        $stmt->close();
        
        // Get artisan's listings count
        $query = "SELECT COUNT(*) as count FROM listings WHERE user_id = ? AND status = 'active'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['myListings'] = $row['count'];
        }
        $stmt->close();
    } elseif ($userRole == 'client') {
        // Get pending bookings for client
        $query = "SELECT COUNT(*) as count FROM bookings WHERE client_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['pendingBookings'] = $row['count'];
        }
        $stmt->close();
    }
    
    // Get unread messages count
    $query = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $stats['unreadMessages'] = $row['count'];
    }
    $stmt->close();
    
    // Get recent reviews (either by or for the user)
    if ($userRole == 'artisan') {
        $query = "SELECT r.review_id, r.rating, r.comment, r.creation_date, 
                 u.first_name, u.last_name 
                 FROM reviews r
                 JOIN users u ON r.reviewer_id = u.user_id
                 WHERE r.reviewed_id = ? 
                 ORDER BY r.creation_date DESC LIMIT 3";
    } else {
        $query = "SELECT r.review_id, r.rating, r.comment, r.creation_date, 
                 u.first_name, u.last_name 
                 FROM reviews r
                 JOIN users u ON r.reviewed_id = u.user_id
                 WHERE r.reviewer_id = ? 
                 ORDER BY r.creation_date DESC LIMIT 3";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $stats['recentReviews'][] = $row;
    }
    $stmt->close();
    
    return $stats;
}

// Get user-specific dashboard data
$dashboardStats = getDashboardStats($conn, $userId, $userRole);

// Get system announcements
function getAnnouncements($conn) {
    $announcements = [];
    
    // In a real implementation, you'd fetch from an announcements table
    // For now, returning static data
    $announcements[] = [
        'title' => 'System Expansion',
        'content' => '🎉 EmpowerCraft is expanding! We\'re rolling out our services to two new regions. Stay tuned for more features and events coming your way this quarter.',
        'date' => date('Y-m-d')
    ];
    
    return $announcements;
}

$announcements = getAnnouncements($conn);

// Update last_login timestamp
$updateStmt = $conn->prepare('UPDATE users SET last_login = NOW() WHERE user_id = ?');
$updateStmt->bind_param('i', $userId);
$updateStmt->execute();
$updateStmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | EmpowerCraft</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      min-height: 100vh;
      background: linear-gradient(to right, #e6f2ff, #d4e6ff);
      position: relative;
      overflow-x: hidden;
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
      z-index: 10;
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

    .user-info {
      margin-bottom: 20px;
      padding: 15px 10px;
      border-top: 1px solid rgba(255, 255, 255, 0.3);
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    }

    .user-info p {
      margin: 5px 0;
      font-size: 14px;
    }

    .dashboard-main {
      margin-left: 250px;
      padding: 40px;
      flex: 1;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
      position: relative;
      z-index: 1;
    }

    .dashboard-card {
      background-color: #ffffffee;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      border: 1px solid #e3e3e3;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      backdrop-filter: blur(5px);
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .dashboard-card h2 {
      margin-bottom: 10px;
      font-size: 22px;
      color: #2e5c3d;
    }

    .dashboard-card p {
      font-size: 15px;
      line-height: 1.6;
      color: #333;
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

    .icon {
      font-size: 30px;
      margin-bottom: 10px;
      display: block;
    }

    .stat-box {
      display: flex;
      justify-content: space-between;
      margin: 10px 0;
      padding: 10px;
      background-color: rgba(76, 175, 80, 0.1);
      border-radius: 5px;
    }

    .stat-label {
      font-weight: bold;
    }

    .review-item {
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }

    .review-item .rating {
      color: #FFC107;
    }

    .review-item .review-date {
      font-size: 12px;
      color: #777;
    }

    /* Floating decorative elements */
    .floating-element {
      position: absolute;
      opacity: 0.15;
      z-index: 0;
      pointer-events: none;
    }

    .tools {
      top: 10%;
      right: 5%;
      font-size: 80px;
      transform: rotate(15deg);
      color: #4CAF50;
      animation: float 8s ease-in-out infinite;
    }

    .profile {
      bottom: 15%;
      right: 15%;
      font-size: 70px;
      transform: rotate(-10deg);
      color: #2196F3;
      animation: float 10s ease-in-out infinite 1s;
    }

    .messaging {
      top: 60%;
      left: 20%;
      font-size: 60px;
      transform: rotate(5deg);
      color: #4CAF50;
      animation: float 7s ease-in-out infinite 0.5s;
    }

    .stats {
      top: 20%;
      left: 30%;
      font-size: 65px;
      transform: rotate(-5deg);
      color: #2196F3;
      animation: float 9s ease-in-out infinite 2s;
    }

    @keyframes float {
      0% { transform: translateY(0) rotate(0); }
      50% { transform: translateY(-20px) rotate(5deg); }
      100% { transform: translateY(0) rotate(0); }
    }
  </style>
</head>
<body>
  <aside class="sidebar">
    <h2>EmpowerCraft</h2>
    
    <div class="user-info">
      <p>Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
      <p>Role: <?php echo ucfirst(htmlspecialchars($userRole)); ?></p>
    </div>
    
    <nav>
      <a href="dashboard.php">🏠 Dashboard</a>
      <a href="profile.php">👤 My Profile</a>
      <a href="messages.php">💬 Messages 
        <?php if($dashboardStats['unreadMessages'] > 0): ?>
          <span class="notification-badge"><?php echo $dashboardStats['unreadMessages']; ?></span>
        <?php endif; ?>
      </a>
      <?php if($userRole == 'artisan'): ?>
        <a href="services.php">🛠️ My Services</a>
        <a href="bookings.php">📅 Bookings
          <?php if($dashboardStats['pendingBookings'] > 0): ?>
            <span class="notification-badge"><?php echo $dashboardStats['pendingBookings']; ?></span>
          <?php endif; ?>
        </a>
      <?php elseif($userRole == 'client'): ?>
        <a href="find-services.php">🔍 Find Services</a>
        <a href="my-bookings.php">📅 My Bookings
          <?php if($dashboardStats['pendingBookings'] > 0): ?>
            <span class="notification-badge"><?php echo $dashboardStats['pendingBookings']; ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
      <a href="reviews.php">⭐ Reviews</a>
      <a href="settings.php">⚙ Settings</a>
      <a href="logout.php">🚪 Logout</a>
    </nav>
  </aside>

  <!-- Floating decorative elements -->
  <div class="floating-element tools">🛠️</div>
  <div class="floating-element profile">👤</div>
  <div class="floating-element messaging">💬</div>
  <div class="floating-element stats">📊</div>

  <main class="dashboard-main">
    <section class="dashboard-card">
      <span class="icon">👥</span>
      <h2>Welcome to EmpowerCraft</h2>
      <p>This dashboard provides a snapshot of your activity and system statistics. EmpowerCraft is dedicated to connecting skilled individuals with those in need of their expertise, creating opportunities for artisans and making services accessible.</p>
    </section>

    <section class="dashboard-card">
      <span class="icon">📈</span>
      <h2>Platform Statistics</h2>
      <div class="stat-box">
        <span class="stat-label">Active Users:</span>
        <span class="stat-value"><?php echo number_format($dashboardStats['totalUsers']); ?></span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Verified Artisans:</span>
        <span class="stat-value"><?php echo number_format($dashboardStats['verifiedArtisans']); ?></span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Completed Tasks:</span>
        <span class="stat-value"><?php echo number_format($dashboardStats['completedTasks']); ?></span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Satisfaction Rate:</span>
        <span class="stat-value"><?php echo $dashboardStats['satisfactionRate']; ?>%</span>
      </div>
    </section>

    <?php if($userRole == 'artisan'): ?>
    <section class="dashboard-card">
      <span class="icon">🛠</span>
      <h2>My Services & Listings</h2>
      <div class="stat-box">
        <span class="stat-label">Active Services:</span>
        <span class="stat-value"><?php echo $dashboardStats['myServices']; ?></span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Active Listings:</span>
        <span class="stat-value"><?php echo $dashboardStats['myListings']; ?></span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Pending Bookings:</span>
        <span class="stat-value"><?php echo $dashboardStats['pendingBookings']; ?></span>
      </div>
      <p><a href="services.php" style="color: #4CAF50; text-decoration: none;">Manage your services →</a></p>
    </section>
    <?php elseif($userRole == 'client'): ?>
    <section class="dashboard-card">
      <span class="icon">📝</span>
      <h2>My Bookings</h2>
      <div class="stat-box">
        <span class="stat-label">Pending Bookings:</span>
        <span class="stat-value"><?php echo $dashboardStats['pendingBookings']; ?></span>
      </div>
      <p><a href="my-bookings.php" style="color: #4CAF50; text-decoration: none;">View all bookings →</a></p>
    </section>
    <?php endif; ?>

    <section class="dashboard-card">
      <span class="icon">💬</span>
      <h2>Messages</h2>
      <?php if($dashboardStats['unreadMessages'] > 0): ?>
        <p>You have <strong><?php echo $dashboardStats['unreadMessages']; ?> unread message(s)</strong>.</p>
      <?php else: ?>
        <p>You have no unread messages.</p>
      <?php endif; ?>
      <p><a href="messages.php" style="color: #4CAF50; text-decoration: none;">Go to inbox →</a></p>
    </section>

    <section class="dashboard-card">
      <span class="icon">⭐</span>
      <h2>Recent Reviews</h2>
      <?php if(empty($dashboardStats['recentReviews'])): ?>
        <p>No reviews available yet.</p>
      <?php else: ?>
        <?php foreach($dashboardStats['recentReviews'] as $review): ?>
          <div class="review-item">
            <div class="rating">
              <?php for($i = 0; $i < 5; $i++): ?>
                <?php if($i < floor($review['rating'])): ?>
                  ★
                <?php else: ?>
                  ☆
                <?php endif; ?>
              <?php endfor; ?>
            </div>
            <p>"<?php echo htmlspecialchars(substr($review['comment'], 0, 100)); ?>..."</p>
            <p class="review-date">By <?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?> on <?php echo date('M d, Y', strtotime($review['creation_date'])); ?></p>
          </div>
        <?php endforeach; ?>
        <p><a href="reviews.php" style="color: #4CAF50; text-decoration: none;">View all reviews →</a></p>
      <?php endif; ?>
    </section>

    <section class="dashboard-card">
      <span class="icon">📢</span>
      <h2>Announcements</h2>
      <?php foreach($announcements as $announcement): ?>
        <div class="announcement-item">
          <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
          <p><?php echo $announcement['content']; ?></p>
          <p class="announcement-date">Posted on: <?php echo date('M d, Y', strtotime($announcement['date'])); ?></p>
        </div>
      <?php endforeach; ?>
    </section>
  </main>
</body>
</html>