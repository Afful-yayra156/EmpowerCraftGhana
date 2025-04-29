<?php
session_start();
include '../db/config.php';

$user_id = $_SESSION['user_id'];

// Fetch user info from database
$sql = "SELECT first_name, last_name, email, bio, phone_number, location, profile_picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set default profile picture if none exists
$profilePicture = $user['profile_picture'] ? '../' . $user['profile_picture'] : '../assets/images/default-avatar.png';

// Update user info when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $location = $_POST['location'];
    $phone_number = $_POST['phone_number'];

    // Optionally split full name into first and last
    $names = explode(' ', $full_name, 2);
    $first_name = $names[0];
    $last_name = isset($names[1]) ? $names[1] : '';

    $updateSql = "UPDATE users SET first_name=?, last_name=?, location=?, phone_number=? , bio=? WHERE user_id=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssisi", $first_name, $last_name, $location, $phone_number, $bio, $user_id);

    if ($updateStmt->execute()) {
        // Refresh user data
        header("Location: profile.php"); 
        exit();
    } else {
        echo "Error updating profile.";
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel ="stylesheet" href = "../assets/css/profile.css" >
  <title>Profile | EmpowerCraft</title>
  
</head>
<body>

  <!-- Background shapes and decorations -->
  <div class="bg-shape circle circle-1"></div>
  <div class="bg-shape circle circle-2"></div>
  <div class="bg-shape square square-1"></div>
  <div class="bg-shape square square-2"></div>
  
  <!-- Floating icons -->
  <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLXVzZXIiPjxwYXRoIGQ9Ik0xOSAyMUg1YTIgMiAwIDAgMS0yLTJWNWEyIDIgMCAwIDEgMi0yaDE0YTIgMiAwIDAgMSAyIDJ2MTRhMiAyIDAgMCAxLTIgMloiLz48Y2lyY2xlIGN4PSIxMiIgY3k9IjgiIHI9IjIiLz48cGF0aCBkPSJNNiAxNmE0IDQgMCAwIDEgNC00aDRhNCA0IDAgMCAxIDQgNCIvPjwvc3ZnPg==" alt="Profile Icon" class="floating-icon icon-profile">
  
  <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLXN0YXIiPjxwb2x5Z29uIHBvaW50cz0iMTIgMiAxNS4wOSA4LjI2IDIyIDkuMjcgMTcgMTQuMTQgMTguMTggMjEuMDIgMTIgMTcuNzcgNS44MiAyMS4wMiA3IDE0LjE0IDIgOS4yNyA4LjkxIDguMjYgMTIgMiIvPjwvc3ZnPg==" alt="Star Icon" class="floating-icon icon-star">
  
  <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLW1lc3NhZ2Utc3F1YXJlIj48cGF0aCBkPSJNMjEgMTVhMiAyIDAgMCAxLTIgMkg3bC00IDR2LTRhMiAyIDAgMCAxLTItMlY1YTIgMiAwIDAgMSAyLTJoMTRhMiAyIDAgMCAxIDIgMnoiLz48L3N2Zz4=" alt="Message Icon" class="floating-icon icon-message">

  <aside class="sidebar">
    <div class="logo">
      <div class="logo-icon"><i class="fas fa-paint-brush"></i></div>
      <h2>EmpowerCraft</h2>
    </div>
    <nav>
      <a href="dashboard.php" class="nav-item">
        <i class="fas fa-home"></i>
        <span class="nav-text">Dashboard</span>
      </a>
      <a href="profile.php" class="nav-item active">
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
        <a href="analytics.php" class="nav-item">
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

  <main class="profile-main-full">
    <section class="profile-header">
      <div class="avatar-container">
        <form id="upload-form" action="../actions/upload_profile_picture.php" method="POST" enctype="multipart/form-data">
        <img src="../actions/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="User Avatar" class="avatar" id="profile-avatar" />

          <label for="avatar-upload" class="change-avatar" title="Change Profile Picture">📷</label>
          <input type="file" id="avatar-upload" name="profile_picture" accept="image/*" style="display: none;"/>
        </form>
      </div>
      <div>
        <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
        <p>Artisan - Woodwork & Decor</p>

      </div>
    </section>

    <div class="profile-columns">
      <section class="profile-info">
        <h2>About Me</h2>
        <p><?php echo htmlspecialchars($user['bio']); ?></p>

        <h2>Contact Info</h2>
        <ul>
          <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
          <li><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></li>
          <li><strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?></li>
        </ul>
      </section>

      <section class="edit-profile">
        <h2>Edit Profile</h2>
        <form method="post">
          <input type="text" name="full_name" placeholder="Full Name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" required/>
          <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required/>
          <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required/>
          <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($user['location']); ?>" required/>
          <textarea name="bio" placeholder="Bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
          <button type="submit">Save Changes</button>
        </form>
      </section>
    </div>

    <section class="reviews-comments">
      <h2>Reviews</h2>
      <div class="review">
        <strong>Akwesi B. ⭐⭐⭐⭐⭐</strong>
        <p>Excellent craftsmanship. Highly recommend!</p>
      </div>
      <div class="review">
        <strong>Nana Yaa ⭐⭐⭐⭐</strong>
        <p>Delivery took a bit long, but the product is great.</p>
      </div>

      <h3>Leave a Review</h3>
      <form>
        <textarea placeholder="Write your review here..."></textarea>
        <button type="submit">Submit</button>
      </form>
    </section>
  </main>
  
  <div class="toast" id="toast-notification">Profile picture updated successfully!</div>

  <script>
    // Add event listener to handle file selection
    document.getElementById('avatar-upload').addEventListener('change', function(event) {
      const file = event.target.files[0];
      
      if (file) {
        // Create a FileReader to read the selected image
        const reader = new FileReader();
        
        reader.onload = function(e) {
          // Update the profile image with the selected file
          document.getElementById('profile-avatar').src = e.target.result;
          
          // Show success notification
          showToast('Profile picture updated successfully!');
          
          // Submit the form to upload the image
          document.getElementById('upload-form').submit();
        };
        
        // Read the image file as a data URL
        reader.readAsDataURL(file);
      }
    });
    
    // Function to show toast notification
    function showToast(message) {
      const toast = document.getElementById('toast-notification');
      toast.textContent = message;
      toast.style.display = 'block';
      
      // Hide the toast after 3 seconds
      setTimeout(function() {
        toast.style.display = 'none';
      }, 3000);
    }


    document.getElementById('avatar-upload').addEventListener('change', function() {
    const fileInput = this;
    const form = document.getElementById('upload-form');
    const avatar = document.getElementById('profile-avatar');

    if (fileInput.files && fileInput.files[0]) {
        // Preview the selected image immediately
        const reader = new FileReader();
        reader.onload = function(e) {
            avatar.src = e.target.result;
        };
        reader.readAsDataURL(fileInput.files[0]);

        // Automatically submit the form after selecting a file
        form.submit();
    }
});
  </script>
</body>
</html>