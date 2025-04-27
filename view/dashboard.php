
<?php
session_start();
include '../db/config.php'; 

if (!isset($_SESSION['fname'])) {
    header('Location: login.php');
    exit();
}

$userRole = $_SESSION['role'];

$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);

$totalUsers = 0; // Default to 0 if no users found
if ($result_users && $row_users = $result_users->fetch_assoc()) {
    $totalUsers = $row_users['total_users'];
}

// Query to get total reviews count for calculating the percentage
$query_total_reviews = "SELECT COUNT(*) AS total_reviews FROM reviews";
$result_total_reviews = $conn->query($query_total_reviews);

if ($result_total_reviews && $row_total_reviews = $result_total_reviews->fetch_assoc()) {
    $totalReviews = $row_total_reviews['total_reviews'];
}


// Query to get the total number of bookings
$sql_bookings = "SELECT COUNT(*) AS total_bookings FROM bookings"; 
$result_bookings = $conn->query($sql_bookings);

$totalBookings = 0; // Default to 0 if no bookings found
if ($result_bookings && $row_bookings = $result_bookings->fetch_assoc()) {
    $totalBookings = $row_bookings['total_bookings'];
}

$sql_count = "SELECT COUNT(*) AS total_services FROM services";
$result_count = $conn->query($sql_count);

$total_services = 0; // Default to 0 if no services found
if ($result_count && $row_count = $result_count->fetch_assoc()) {
    $total_services = $row_count['total_services'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard | EmpowerCraft</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel ="stylesheet" href = "../assets/css/dashboard.css">

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
      <a href="orders.php" class="nav-item">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-text">Orders</span>
      </a>
      <a href="#" class="nav-item">
        <i class="fas fa-chart-line"></i>
        <span class="nav-text">Analytics</span>
      </a>
      <a href="#" class="nav-item">
        <i class="fas fa-cog"></i>
        <span class="nav-text">Settings</span>
      </a>
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
          <i class="fas fa-bell"></i>
          <span class="notification-badge">3</span>
        </div>
        <div class="avatar">K</div>
      </div>
    </section>

    <section class="summary">

    <div class="summary-box">
     <?php if ($userRole == 'admin') { ?>
        <h3><i class="fas fa-box-open"></i> Total Users </h3>
        <p><?php echo $totalUsers; ?></p>
        <div class="trend">
          <i class="fas fa-arrow-up"></i> 
          <span>Users</span>
        </div>
        <div class="decoration-shape"></div>
      </div>
      <div class="summary-box">
        <h3><i class="fas fa-box-open"></i> Total Services </h3>
        <p><?php echo $total_services; ?></p>
        <div class="trend">
          <i class="fas fa-arrow-up"></i> 
          <span>+3 this month</span>
        </div>
        <div class="decoration-shape"></div>
      </div>
      <div class="summary-box">
        <h3><i class="fas fa-calendar-check"></i> Bookings</h3>
        <p><?php echo $totalBookings;?></p>
        <div class="trend">
          <i class="fas fa-arrow-up"></i> 
          <span>+2 this week</span>
        </div>
        <div class="decoration-shape"></div>
      </div>
      <div class="summary-box">
        <h3><i class="fas fa-star"></i> Reviews</h3>
        <p><?php  echo $totalReviews; ?></p>
        <div class="trend">
          <i class="fas fa-arrow-up"></i> 
          <span>98% positive</span>
        </div>
        <div class="decoration-shape"></div>
      </div>
      <?php } elseif ($userRole == 'artisan' || $userRole == 'client') { ?>
        <!-- Artisan/Client Stats -->
        <div class="summary-box">
        <h3><i class="fas fa-box-open"></i> Total Users </h3>
        <p><?php echo $totalUsers; ?></p>
        <div class="trend">
          <i class="fas fa-arrow-up"></i> 
          <span>Users</span>
        </div>
        <div class="decoration-shape"></div>
      </div>
      <div class="summary-box">
        <h3><i class="fas fa-box-open"></i> Total Services </h3>
        <p><?php echo $total_services; ?></p>
        <div class="trend">
          <i class="fas fa-arrow-up"></i> 
          <span>+3 this month</span>
        </div>
        <div class="decoration-shape"></div>
      </div>
      <div class="summary-box">
        <h3><i class="fas fa-calendar-check"></i> Bookings</h3>
        <p><?php echo $totalBookings;?></p>
        <div class="trend">
          <i class="fas fa-arrow-up"></i> 
          <span>+2 this week</span>
        </div>
        <div class="decoration-shape"></div>
      </div>
      <div class="summary-box">
        <h3><i class="fas fa-star"></i> Reviews</h3>
        <p><?php  echo $totalReviews; ?></p>
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
          <a href="#">View all</a>
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
          <a href="#">View calendar</a>
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
  </main>
</body>
</html>

