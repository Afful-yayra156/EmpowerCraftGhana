<?php
session_start();
include '../db/config.php'; 

// Redirect if not logged in
if (!isset($_SESSION['fname'])) {
    header('Location: login.php');
    exit();
}

$userRole = $_SESSION['role'];
$user_id = $_SESSION['user_id']; 

// Initialize variables with default values
$totalUsers = 0;
$total_services = 0;
$totalBookings = 0;
$totalReviews = 0;
$total_userservices = 0;
$totaluserBookings = 0;
$totaluserReviews = 0;

// Error handling function
function handleQueryError($conn, $query) {
    if ($conn->error) {
        error_log("Query error: " . $query . " - " . $conn->error);
        return false;
    }
    return true;
}

// Admin statistics
if ($userRole == 'admin') {
    // Get total users
    $sql_users = "SELECT COUNT(*) AS total_users FROM users";
    $result_users = $conn->query($sql_users);
    handleQueryError($conn, $sql_users);
    
    if ($result_users && $row_users = $result_users->fetch_assoc()) {
        $totalUsers = $row_users['total_users'];
    }
    
    // Get total services
    $sql_services = "SELECT COUNT(*) AS total_services FROM services";
    $result_services = $conn->query($sql_services);
    handleQueryError($conn, $sql_services);
    
    if ($result_services && $row_services = $result_services->fetch_assoc()) {
        $total_services = $row_services['total_services'];
    }
    
    // Get total bookings
    $sql_bookings = "SELECT COUNT(*) AS total_bookings FROM bookings";
    $result_bookings = $conn->query($sql_bookings);
    handleQueryError($conn, $sql_bookings);
    
    if ($result_bookings && $row_bookings = $result_bookings->fetch_assoc()) {
        $totalBookings = $row_bookings['total_bookings'];
    }
    
    // Get total reviews
    $sql_reviews = "SELECT COUNT(*) AS total_reviews FROM reviews";
    $result_reviews = $conn->query($sql_reviews);
    handleQueryError($conn, $sql_reviews);
    
    if ($result_reviews && $row_reviews = $result_reviews->fetch_assoc()) {
        $totalReviews = $row_reviews['total_reviews'];
    }
}

// User-specific statistics (for both artisan and client)
if ($userRole == 'artisan' || $userRole == 'client') {
    // Get user's services
    $stmt_services = $conn->prepare("SELECT COUNT(*) AS total_services FROM services WHERE user_id = ?");
    $stmt_services->bind_param("i", $user_id);
    $stmt_services->execute();
    $result_services = $stmt_services->get_result();
    
    if ($result_services && $row_services = $result_services->fetch_assoc()) {
        $total_userservices = $row_services['total_services'];
    }
    $stmt_services->close();
    
// Get total orders placed on *your* services
    $stmt_bookings = $conn->prepare(
      "SELECT COUNT(*) AS total_bookings
      FROM orders o
      JOIN services s ON o.service_id = s.service_id
      WHERE s.user_id = ?"
    );
    $stmt_bookings->bind_param("i", $user_id);
    $stmt_bookings->execute();
    $stmt_bookings->bind_result($totaluserBookings);
    $stmt_bookings->fetch();
    $stmt_bookings->close();

    
    // Get user's reviews
    $stmt_reviews = $conn->prepare("SELECT COUNT(*) AS total_reviews FROM reviews WHERE review_id = ?");
    $stmt_reviews->bind_param("i", $user_id);
    $stmt_reviews->execute();
    $result_reviews = $stmt_reviews->get_result();
    
    if ($result_reviews && $row_reviews = $result_reviews->fetch_assoc()) {
        $totaluserReviews = $row_reviews['total_reviews'];
    }
    $stmt_reviews->close();



}

// Move this code OUTSIDE of any user role conditions - place it before the last closing PHP tag
$sql_contacts = "SELECT id, first_name, last_name, message, submitted_at 
                FROM contact_messages 
                ORDER BY submitted_at DESC 
                LIMIT 3";
$result_contacts = $conn->query($sql_contacts);
$recent_contacts = [];

if ($result_contacts && $result_contacts->num_rows > 0) {
    while($row = $result_contacts->fetch_assoc()) {
        $recent_contacts[] = $row;
    }
}


$sql = "SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status = 'cart'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the buyer_id parameter
$stmt->execute();
$stmt->bind_result($num_services);
$stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard | EmpowerCraft</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
  <!-- Floating emojis -->
  <div class="floating-emoji" style="top: 20%; left: 30%;">🧵</div>
  <div class="floating-emoji" style="top: 15%; left: 80%; animation-delay: 1s;">🧶</div>
  <div class="floating-emoji" style="top: 70%; left: 85%; animation-delay: 2s;">🪡</div>
  <div class="floating-emoji" style="top: 60%; left: 20%; animation-delay: 3s;">🪵</div>
  <div class="floating-emoji" style="top: 80%; left: 50%; animation-delay: 4s;">🧺</div>

  <aside class="sidebar">
    <div class="logo">
      <div class="logo-icon"><i class="fas fa-paint-brush"></i></div>
      <h2>EmpowerCraft</h2>
    </div>
    <nav>
      <a href="dashboard.php" class="nav-item active">
        <i class="fas fa-home"></i>
        <span class="nav-text">Dashboard</span>
      </a>
      <a href="profile.php" class="nav-item">
        <i class="fas fa-user"></i>
        <span class="nav-text">My Profile</span>
      </a>
      <a href="messages.php" class="nav-item">
        <i class="fas fa-envelope"></i>
        <span class="nav-text">Messages</span>
      </a>
      <a href="reviews.php" class="nav-item">
        <i class="fas fa-star"></i>
        <span class="nav-text">Reviews</span>
      </a>

      <a href="services.php" class="nav-item">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-text">Services</span>
      </a>
      <a href="orders.php" class="nav-item">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-text">Orders</span>
      </a>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="analytics.php" class="nav-item active">
          <i class="fas fa-chart-line"></i>
          <span class="nav-text">Analytics</span>
        </a>
      <?php endif; ?>

      <a href="logout.php" class="nav-item">
        <i class="fas fa-sign-out-alt"></i>
        <span class="nav-text">Logout</span>
      </a>
    </nav>
  </aside>

  <main class="main">
    <section class="header">
      <div class="header-left">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['fname']); ?></h1>
        <p>Here's what's happening with your craft business</p>
      </div>
      <div class="user-profile">
        <div class="notification-bell">
          <i class="fas fa-shopping-cart"></i>
          <span class="notification-badge"><?php echo $num_services; ?></span>
        </div>
        <div class="avatar">
          <?php echo htmlspecialchars(substr($_SESSION['fname'], 0, 1)); ?>
        </div>
      </div>
    </section>

    <section class="summary">
      <?php if ($userRole == 'admin') { ?>
        <!-- Admin stats -->
        <div class="summary-box">
          <h3><i class="fas fa-users"></i> Total Users</h3>
          <p><?php echo $totalUsers; ?></p>
          <div class="trend">
            <i class="fas fa-arrow-up"></i> 
            <span>Users</span>
          </div>
          <div class="decoration-shape"></div>
        </div>
        <div class="summary-box">
          <h3><i class="fas fa-box-open"></i> Total Services</h3>
          <p><?php echo $total_services; ?></p>
          <div class="trend">
            <i class="fas fa-arrow-up"></i> 
            <span>+3 this month</span>
          </div>
          <div class="decoration-shape"></div>
        </div>
        <div class="summary-box">
          <h3><i class="fas fa-calendar-check"></i> Bookings</h3>
          <p><?php echo $totalBookings; ?></p>
          <div class="trend">
            <i class="fas fa-arrow-up"></i> 
            <span>+2 this week</span>
          </div>
          <div class="decoration-shape"></div>
        </div>
        <div class="summary-box">
          <h3><i class="fas fa-star"></i> Reviews</h3>
          <p><?php echo $totalReviews; ?></p>
          <div class="trend">
            <i class="fas fa-arrow-up"></i> 
            <span>98% positive</span>
          </div>
          <div class="decoration-shape"></div>
        </div>

    </section>

      <div class="dashboard-grid">
  <section class="messages-section">
    <div class="section-header">
      <h2>Recent Messages</h2>
      <a href="messages.php">View all</a>
    </div>
    
    <?php if (!empty($recent_contacts)): ?>
      <?php foreach ($recent_contacts as $contact): ?>
        <div class="message">
          <div class="message-icon">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="message-content">
            <h4><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></h4>
            <p><?php echo htmlspecialchars(substr($contact['message'], 0, 100)); ?>...</p>
            <div class="message-time"><?php echo date('F j, Y', strtotime($contact['submitted_at'])); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="message">
        <div class="message-content">
          <h4>No Recent Messages</h4>
          <p>There are no recent contact messages to display.</p>
        </div>
      </div>
    <?php endif; ?>
               
    
        
      </section>

      <section class="upcoming-section">
        <div class="section-header">
          <h2>Upcoming Bookings</h2>
          <a href="orders.php">View calendar</a>
        </div>
        <div class="upcoming-event">
          <div class="event-date">
            <div class="day">28</div>
            <div class="month">APR</div>
          </div>
          <div class="event-details">
            <h4>Weaving Workshop</h4>
            <p>10:00 AM - 12:00 PM</p>
          </div>
        </div>
        <div class="upcoming-event">
          <div class="event-date">
            <div class="day">03</div>
            <div class="month">MAY</div>
          </div>
          <div class="event-details">
            <h4>Pottery Delivery</h4>
            <p>2:30 PM - 3:30 PM</p>
          </div>
        </div>
        <div class="upcoming-event">
          <div class="event-date">
            <div class="day">09</div>
            <div class="month">MAY</div>
          </div>
          <div class="event-details">
            <h4>Woodcraft Class</h4>
            <p>1:00 PM - 4:00 PM</p>
          </div>
        </div>
      </section>
    </div>
      <?php } else { ?>
        <!-- User stats (artisan/client) -->
        <div class="summary-box">
          <h3><i class="fas fa-box-open"></i> My Services</h3>
          <p><?php echo $total_userservices; ?></p>
          <div class="trend">
            <i class="fas fa-arrow-up"></i> 
            <span>+3 this month</span>
          </div>
          <div class="decoration-shape"></div>
        </div>
        <div class="summary-box">
          <h3><i class="fas fa-calendar-check"></i> My Bookings</h3>
          <p><?php echo $totaluserBookings; ?></p>
          <div class="trend">
            <i class="fas fa-arrow-up"></i> 
            <span>+2 this week</span>
          </div>
          <div class="decoration-shape"></div>
        </div>
        <div class="summary-box">
          <h3><i class="fas fa-star"></i> My Reviews</h3>
          <p><?php echo $totaluserReviews; ?></p>
          <div class="trend">
            <i class="fas fa-arrow-up"></i> 
            <span>98% positive</span>
          </div>
          <div class="decoration-shape"></div>
        </div>
      <?php } ?>
    </section>

    <div class="dashboard-grid">
      <section class="messages-section">
        <div class="section-header">
          <h2>Recent Messages</h2>
          <a href="messages.php">View all</a>
        </div>
        <div class="message">
          <div class="message-icon">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="message-content">
            <h4>New Inquiry</h4>
            <p>You have a new inquiry for your Kente bag.</p>
            <div class="message-time">2 hours ago</div>
          </div>
        </div>
        <div class="message">
          <div class="message-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="message-content">
            <h4>Booking Confirmed</h4>
            <p>Booking confirmed for Woodcraft Repair.</p>
            <div class="message-time">Yesterday</div>
          </div>
        </div>
        <div class="message">
          <div class="message-icon">
            <i class="fas fa-star"></i>
          </div>
          <div class="message-content">
            <h4>New Review</h4>
            <p>Kofi left a 5-star review on your Beaded Jewelry.</p>
            <div class="message-time">2 days ago</div>
          </div>
        </div>
      </section>

<section class="upcoming-section">
    <div class="section-header">
        <h2>Upcoming Bookings</h2>
        <a href="orders.php">View calendar</a>
    </div>

    <?php if (!empty($upcoming_bookings)): ?>
        <?php foreach ($upcoming_bookings as $booking): ?>
            <?php
                $date = strtotime($booking['order_date']);
                $day = date('d', $date);
                $month = strtoupper(date('M', $date));
                $time = date('g:i A', $date);
            ?>
            <div class="upcoming-event">
                <div class="event-date">
                    <div class="day"><?php echo $day; ?></div>
                    <div class="month"><?php echo $month; ?></div>
                </div>
                <div class="event-details">
                    <h4><?php echo htmlspecialchars($booking['title']); ?></h4>
                    <p><?php echo $time; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="padding: 10px;">No upcoming bookings yet.</p>
    <?php endif; ?>
</section>

    </div>
  </main>
</body>
</html>