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

    .icon {
      font-size: 30px;
      margin-bottom: 10px;
      display: block;
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
    <nav>
      <a href="dashboard.php">🏠 Dashboard</a>
      <a href="profile.html">👤 My Profile</a>
      <a href="services.php"> 👤Services</a>
      <a href="#">⭐ Reviews</a>
      <a href="#">⚙ Settings</a>
      <a href="#">🚪 Logout</a>
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
      <p>This dashboard provides a snapshot of user activity and system statistics. EmpowerCraft is dedicated to connecting skilled individuals with those in need of their expertise, creating opportunities for artisans and making services accessible.</p>
    </section>

    <section class="dashboard-card">
      <span class="icon">📈</span>
      <h2>Statistics Overview</h2>
      <p>We currently have over <strong>1,200 active users</strong> and <strong>320 verified artisans</strong> registered on the platform. Monthly traffic has increased by <strong>25%</strong> since our last update.</p>
    </section>

    <section class="dashboard-card">
      <span class="icon">🛠</span>
      <h2>Artisan Performance</h2>
      <p>Our top-rated artisans have completed more than <strong>3,000 tasks</strong> combined. Feedback shows a <strong>98% satisfaction rate</strong>, reflecting our commitment to quality service.</p>
    </section>

    <section class="dashboard-card">
      <span class="icon">📝</span>
      <h2>Latest Updates</h2>
      <p>New features include a direct messaging system, advanced booking tools, and improved profile customization. We're constantly working to enhance user experience on EmpowerCraft.</p>
    </section>

    <section class="dashboard-card">
      <span class="icon">🔒</span>
      <h2>Security & Privacy</h2>
      <p>Your information is protected by end-to-end encryption. We follow strict data protection regulations to ensure privacy and confidentiality across all user interactions.</p>
    </section>

    <section class="dashboard-card">
      <span class="icon">📢</span>
      <h2>Announcements</h2>
      <p>🎉 EmpowerCraft is expanding! We're rolling out our services to two new regions. Stay tuned for more features and events coming your way this quarter.</p>
    </section>
  </main>
</body>
</html>
