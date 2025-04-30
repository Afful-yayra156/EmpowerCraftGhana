<?php
session_start();
include '../db/config.php';

// Fetch number of users registered daily
$sql_users = "SELECT DATE(registration_date) as reg_date, COUNT(*) as user_count
              FROM users
              GROUP BY DATE(registration_date)
              ORDER BY reg_date ASC";

$result_users = $conn->query($sql_users);
$userDates = [];
$userCounts = [];

if ($result_users && $result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        $userDates[] = $row['reg_date'];
        $userCounts[] = $row['user_count'];
    }
}

// Fetch number of services created daily
$sql_services = "SELECT DATE(creation_date) as service_date, COUNT(*) as service_count
                 FROM services
                 GROUP BY DATE(creation_date)
                 ORDER BY service_date ASC";

$result_services = $conn->query($sql_services);
$serviceDates = [];
$serviceCounts = [];

if ($result_services && $result_services->num_rows > 0) {
    while ($row = $result_services->fetch_assoc()) {
        $serviceDates[] = $row['service_date'];
        $serviceCounts[] = $row['service_count'];
    }
}

// Fetch number of orders placed daily
$sql_orders = "SELECT DATE(order_date) as order_date, COUNT(*) as order_count
               FROM orders
               GROUP BY DATE(order_date)
               ORDER BY order_date ASC";

$result_orders = $conn->query($sql_orders);
$orderDates = [];
$orderCounts = [];

if ($result_orders && $result_orders->num_rows > 0) {
    while ($row = $result_orders->fetch_assoc()) {
        $orderDates[] = $row['order_date'];
        $orderCounts[] = $row['order_count'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Analytics | EmpowerHub</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/analytics.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<aside class="sidebar">
    <div class="logo">
      <div class="logo-icon"><i class="fas fa-paint-brush"></i></div>
      <h2>EmpowerCraft</h2>
    </div>
    <nav>
      <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i><span class="nav-text">Dashboard</span></a>
      <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span class="nav-text">My Profile</span></a>
      <a href="messages.php" class="nav-item"><i class="fas fa-envelope"></i><span class="nav-text">Messages</span></a>
      <a href="reviews.php" class="nav-item"><i class="fas fa-star"></i><span class="nav-text">Reviews</span></a>
      <a href="services.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span class="nav-text">Services</span></a>
      <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span class="nav-text">Orders</span></a>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="analytics.php" class="nav-item active"><i class="fas fa-chart-line"></i><span class="nav-text">Analytics</span></a>
      <?php endif; ?>
      <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span class="nav-text">Logout</span></a>
    </nav>
</aside>

<main class="main" style="margin-left: 250px;">
  <section class="analytics-section">

    <h2 style="text-align: center;">User Registrations Over Time</h2>
    <div style="width: 90%; max-width: 900px; margin: 30px auto;">
        <canvas id="userChart"></canvas>
    </div>

    <h2 style="text-align: center;">Services Created Over Time</h2>
    <div style="width: 90%; max-width: 900px; margin: 30px auto;">
        <canvas id="serviceChart"></canvas>
    </div>

    <h2 style="text-align: center;">Orders Placed Over Time</h2>
    <div style="width: 90%; max-width: 900px; margin: 30px auto;">
        <canvas id="orderChart"></canvas>
    </div>

  </section>
</main>

<script>
// Chart 1 - User Registrations
const ctxUser = document.getElementById('userChart').getContext('2d');
new Chart(ctxUser, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($userDates); ?>,
        datasets: [{
            label: 'Users Registered',
            data: <?php echo json_encode($userCounts); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.4)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true },
            x: { title: { display: true, text: 'Date' } }
        }
    }
});

// Chart 2 - Services Created
const ctxService = document.getElementById('serviceChart').getContext('2d');
new Chart(ctxService, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($serviceDates); ?>,
        datasets: [{
            label: 'Services Created',
            data: <?php echo json_encode($serviceCounts); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true },
            x: { title: { display: true, text: 'Date' } }
        }
    }
});

// Chart 3 - Orders Placed
const ctxOrder = document.getElementById('orderChart').getContext('2d');
new Chart(ctxOrder, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($orderDates); ?>,
        datasets: [{
            label: 'Orders Placed',
            data: <?php echo json_encode($orderCounts); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.4)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true },
            x: { title: { display: true, text: 'Date' } }
        }
    }
});
</script>

</body>
</html>
