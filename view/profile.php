<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
    <h2>EmpowerCraft</h2>
    <nav>
      <a href="dashboard.php">🏠 Dashboard</a>
      <a href="profile.html" class="active">👤 My Profile</a>
      <a href="#">💬 Messages</a>
      <a href="#">⭐ Reviews</a>
      <a href="#">⚙ Settings</a>
      <a href="#">🚪 Logout</a>
    </nav>
  </aside>

  <main class="profile-main-full">
    <section class="profile-header">
      <div class="avatar-container">
        <img src="../assets/images/velma.jpg" alt="User Avatar" class="avatar" id="profile-avatar"/>
        <label for="avatar-upload" class="change-avatar" title="Change Profile Picture">📷</label>
        <input type="file" id="avatar-upload" accept="image/*"/>
      </div>
      <div>
        <h1>User Full Name</h1>
        <p>Artisan - Woodwork & Decor</p>
      </div>
    </section>

    <div class="profile-columns">
      <section class="profile-info">
        <h2>About Me</h2>
        <p>Hello! I'm a passionate wood artisan based in Accra. I create handmade furniture and offer repairs.</p>

        <h2>Contact Info</h2>
        <ul>
          <li><strong>Email:</strong> user@email.com</li>
          <li><strong>Phone:</strong> +233 24 000 0000</li>
          <li><strong>Location:</strong> Kumasi, Ghana</li>
        </ul>
      </section>

      <section class="edit-profile">
        <h2>Edit Profile</h2>
        <form>
          <input type="text" placeholder="Full Name" value="User Full Name"/>
          <input type="email" placeholder="Email" value="user@email.com"/>
          <textarea placeholder="Bio">I'm a passionate wood artisan...</textarea>
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
  </script>
</body>
</html>