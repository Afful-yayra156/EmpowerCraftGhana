<?php
session_start();
require_once '../db/config.php';
require_once '../actions/profile_actions.php';
require_once '../actions/review_actions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view this page";
    header("Location: login.php");
    exit();
}

// Determine which user's profile to display
$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
$user_data = getUserProfile($conn, $profile_user_id);

// If user not found, redirect to own profile
if (!$user_data) {
    $_SESSION['error'] = "User not found";
    header("Location: profile.php");
    exit();
}

// Check if viewing own profile
$is_own_profile = ($profile_user_id == $_SESSION['user_id']);

// Get reviews for this user
$reviews = getUserReviews($conn, $profile_user_id);

// Check if logged-in user has already reviewed this user
$existing_review = $is_own_profile ? false : hasUserReviewed($conn, $_SESSION['user_id'], $profile_user_id);

// Get profile picture path
$profile_picture = getProfilePicturePath($user_data['profile_picture']);

// Page title
$page_title = $is_own_profile ? "My Profile" : $user_data['first_name'] . "'s Profile";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $page_title; ?> | EmpowerCraft</title>
  <link rel="stylesheet" href="../assets/css/profile.css">
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
  
  <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLW1lc3NhZ2Utc3F1YXJlIj48cGF0aCBkPSJNMjEgMTVhMiAyIDAgMCAxLTIgMkg3bC00IDR2LTRhMiAyIDAgMCAxLTItMlY1YTIgMiAwIDAgMSAyLTJoMTRhMiAyIDAgMCAxIDIgMnYxMFoiLz48L3N2Zz4=" alt="Message Icon" class="floating-icon icon-message">

  <!-- Main container -->
  <div class="container">
    <?php include_once '../components/header.php'; ?>
    
    <main class="profile-container">
      <!-- Alert messages -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
          ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
          <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>
      
      <div class="profile-header">
        <div class="profile-image-container">
          <img src="<?php echo $profile_picture; ?>" alt="<?php echo htmlspecialchars($user_data['first_name']); ?>'s profile picture" class="profile-image">
          
          <?php if ($is_own_profile): ?>
            <button id="change-photo-btn" class="btn btn-small btn-outline">Change Photo</button>
            
            <form id="photo-upload-form" action="../actions/image_upload.php" method="post" enctype="multipart/form-data" style="display: none;">
              <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
              <button type="submit" class="btn btn-primary">Upload</button>
            </form>
          <?php endif; ?>
        </div>
        
        <div class="profile-info">
          <h1 class="profile-name"><?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?></h1>
          
          <?php if ($user_data['average_rating'] > 0): ?>
            <div class="rating-stars">
              <?php 
                $avg_rating = round($user_data['average_rating'] * 2) / 2; // Round to nearest 0.5
                for ($i = 1; $i <= 5; $i++) {
                  if ($i <= $avg_rating) {
                    echo '<span class="star full">★</span>';
                  } elseif ($i - 0.5 == $avg_rating) {
                    echo '<span class="star half">★</span>';
                  } else {
                    echo '<span class="star empty">☆</span>';
                  }
                }
              ?>
              <span class="rating-number"><?php echo number_format($user_data['average_rating'], 1); ?> (<?php echo $user_data['review_count']; ?> reviews)</span>
            </div>
          <?php else: ?>
            <div class="rating-stars">
              <span class="star empty">☆</span>
              <span class="star empty">☆</span>
              <span class="star empty">☆</span>
              <span class="star empty">☆</span>
              <span class="star empty">☆</span>
              <span class="rating-number">No ratings yet</span>
            </div>
          <?php endif; ?>
          
          <div class="profile-meta">
            <div class="meta-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="meta-icon">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
              <span><?php echo htmlspecialchars($user_data['location']); ?></span>
            </div>
            
            <div class="meta-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="meta-icon">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
              <span>Member since <?php echo date('F Y', strtotime($user_data['created_at'])); ?></span>
            </div>
            
            <?php if (!empty($user_data['website'])): ?>
              <div class="meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="meta-icon">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="2" y1="12" x2="22" y2="12"></line>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                <a href="<?php echo htmlspecialchars($user_data['website']); ?>" target="_blank" rel="noopener noreferrer">
                  <?php echo htmlspecialchars($user_data['website']); ?>
                </a>
              </div>
            <?php endif; ?>
          </div>
          
          <?php if ($is_own_profile): ?>
            <button id="edit-profile-btn" class="btn btn-primary">Edit Profile</button>
          <?php else: ?>
            <a href="message.php?user_id=<?php echo $profile_user_id; ?>" class="btn btn-secondary">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              Message
            </a>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Profile content -->
      <div class="profile-content">
        <div class="profile-section">
          <h2>About</h2>
          <div class="profile-bio">
            <?php echo nl2br(htmlspecialchars($user_data['bio'])); ?>
          </div>
        </div>
        
        <div class="profile-section">
          <h2>Skills</h2>
          <div class="skills-container">
            <?php 
              $skills = explode(',', $user_data['skills']);
              foreach ($skills as $skill): 
                if (trim($skill) !== ''):
            ?>
              <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
            <?php 
                endif; 
              endforeach; 
            ?>
          </div>
        </div>
        
        <div class="profile-section">
          <h2>Reviews</h2>
          
          <?php if (!$is_own_profile && !$existing_review): ?>
            <div class="add-review-form">
              <h3>Leave a Review</h3>
              <form action="../actions/review_actions.php" method="post">
                <input type="hidden" name="action" value="add_review">
                <input type="hidden" name="user_id" value="<?php echo $profile_user_id; ?>">
                
                <div class="form-group">
                  <label for="rating">Rating:</label>
                  <div class="star-rating-input">
                    <input type="radio" id="star5" name="rating" value="5">
                    <label for="star5">★</label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4">★</label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3">★</label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2">★</label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1">★</label>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="comment">Your review:</label>
                  <textarea id="comment" name="comment" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Review</button>
              </form>
            </div>
          <?php endif; ?>
          
          <div class="reviews-list">
            <?php if (empty($reviews)): ?>
              <p class="no-reviews">No reviews yet.</p>
            <?php else: ?>
              <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                  <div class="review-header">
                    <div class="reviewer-info">
                      <img src="<?php echo getProfilePicturePath($review['reviewer_profile_picture']); ?>" alt="Reviewer" class="reviewer-img">
                      <div>
                        <a href="profile.php?id=<?php echo $review['reviewer_id']; ?>" class="reviewer-name">
                          <?php echo htmlspecialchars($review['reviewer_name']); ?>
                        </a>
                        <div class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                      </div>
                    </div>
                    <div class="review-rating">
                      <?php 
                        for ($i = 1; $i <= 5; $i++) {
                          echo $i <= $review['rating'] ? '<span class="star full">★</span>' : '<span class="star empty">☆</span>';
                        }
                      ?>
                    </div>
                  </div>
                  <div class="review-content">
                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
    
    <?php include_once '../components/footer.php'; ?>
  </div>
  
  <!-- Profile Edit Modal -->
  <?php if ($is_own_profile): ?>
  <div id="edit-profile-modal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h2>Edit Profile</h2>
      
      <form action="../actions/profile_actions.php" method="post">
        <input type="hidden" name="action" value="update_profile">
        
        <div class="form-group">
          <label for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
        </div>
        
        <div class="form-group">
          <label for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
        </div>
        
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user_data['location']); ?>">
        </div>
        
        <div class="form-group">
          <label for="website">Website</label>
          <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($user_data['website']); ?>" placeholder="https://...">
        </div>
        
        <div class="form-group">
          <label for="skills">Skills (comma separated)</label>
          <input type="text" id="skills" name="skills" value="<?php echo htmlspecialchars($user_data['skills']); ?>" placeholder="Sewing, Jewelry, Pottery, etc.">
        </div>
        
        <div class="form-group">
          <label for="bio">Bio</label>
          <textarea id="bio" name="bio" rows="5"><?php echo htmlspecialchars($user_data['bio']); ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>
  <?php endif; ?>
  
  <script>
    // Profile picture upload
    document.addEventListener('DOMContentLoaded', function() {
      const changePhotoBtn = document.getElementById('change-photo-btn');
      const photoUploadForm = document.getElementById('photo-upload-form');
      const fileInput = document.getElementById('profile_picture');
      
      if (changePhotoBtn) {
        changePhotoBtn.addEventListener('click', function() {
          fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
          if (fileInput.files.length > 0) {
            photoUploadForm.submit();
          }
        });
      }
      
      // Profile edit modal
      const editProfileBtn = document.getElementById('edit-profile-btn');
      const editProfileModal = document.getElementById('edit-profile-modal');
      const closeModalBtn = document.querySelector('.close-modal');
      
      if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
          editProfileModal.style.display = 'flex';
        });
        
        closeModalBtn.addEventListener('click', function() {
          editProfileModal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
          if (event.target === editProfileModal) {
            editProfileModal.style.display = 'none';
          }
        });
      }
      
      // Star rating input
      const starInputs = document.querySelectorAll('.star-rating-input input');
      const starLabels = document.querySelectorAll('.star-rating-input label');
      
      starInputs.forEach(function(input, index) {
        input.addEventListener('change', function() {
          starLabels.forEach(function(label, labelIndex) {
            if (labelIndex <= index) {
              label.classList.add('active');
            } else {
              label.classList.remove('active');
            }
          });
        });
      });
    });
  </script>
</body>
</html>