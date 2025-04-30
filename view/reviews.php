<?php
// Database connection
include('../db/config.php');

// Fetch categories from services table
$categoryResult = $conn->query("SELECT DISTINCT category FROM services WHERE category IS NOT NULL AND category != ''");

// Fetch names (users who submitted reviews or available users)
$nameResult = $conn->query("SELECT DISTINCT CONCAT(first_name, ' ', last_name) AS full_name FROM users");

// Fetch references (services)
$referenceResult = $conn->query("SELECT service_id, title FROM services");

// Fetch reviews (for review list section)
$reviewResult = $conn->query("SELECT r.rating, r.comment, u.first_name, u.last_name, r.creation_date, s.title, s.category, CONCAT(u.first_name, ' ', u.last_name) AS full_name
                              FROM reviews r 
                              JOIN users u ON r.reviewer_id = u.user_id 
                              JOIN services s ON r.reference_id = s.service_id
                              ORDER BY r.creation_date DESC");

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$nameFilter = isset($_GET['name']) ? $_GET['name'] : '';

$query = "SELECT r.rating, r.comment, u.first_name, u.last_name, r.creation_date, s.title, s.category, CONCAT(u.first_name, ' ', u.last_name) AS full_name
          FROM reviews r
          JOIN users u ON r.reviewer_id = u.user_id
          JOIN services s ON r.reference_id = s.service_id
          WHERE r.reference_type = 'service'";

if ($categoryFilter) {
    $query .= " AND s.category LIKE '%" . $conn->real_escape_string($categoryFilter) . "%'";
}

if ($nameFilter) {
    $query .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE '%" . $conn->real_escape_string($nameFilter) . "%'";
}

$query .= " ORDER BY r.creation_date DESC";

$reviewResult = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews & Ratings | EmpowerSkills Ghana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/reviews.css">
</head>
<body>
    <section class="content">
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-paint-brush"></i></div>
                <h2>EmpowerCraft</h2>
            </div>
            <nav>
                <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i><span class="nav-text">Dashboard</span></a>
                <a href="profile.php" class="nav-item"><i class="fas fa-user"></i><span class="nav-text">My Profile</span></a>
                <a href="reviews.php" class="nav-item active"><i class="fas fa-star"></i><span class="nav-text">Reviews</span></a>
                <a href="services.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span class="nav-text">Services</span></a>
                <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span class="nav-text">Orders</span></a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="analytics.php" class="nav-item"><i class="fas fa-chart-line"></i><span class="nav-text">Analytics</span></a>
                <?php endif; ?>
                <a href="messages.php" class="nav-item"><i class="fas fa-envelope"></i><span class="nav-text">Messages</span></a>
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span class="nav-text">Logout</span></a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <h1>Reviews & Ratings</h1>

            <!-- Reviews List -->
            <section class="reviews-list">
                <?php while ($review = $reviewResult->fetch_assoc()): ?>
                    <div class="review-item" data-category="<?php echo htmlspecialchars($review['category']); ?>" data-name="<?php echo htmlspecialchars($review['full_name']); ?>">
                        <h3><?php echo htmlspecialchars($review['title']); ?></h3>
                        <div class="review-rating">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                ⭐
                            <?php endfor; ?>
                        </div>
                        <p class="review-author">By: <?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></p>
                        <p class="review-date">Posted on: <?php echo date('F j, Y', strtotime($review['creation_date'])); ?></p>
                        <p class="review-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                <?php endwhile; ?>
            </section>
        </main>

        <!-- Right Sidebar -->
        <aside class="right-sidebar">
            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="searchReview" placeholder="Search Reviews or Users...">
            </div>

            <!-- Filter by Name -->
            <div class="filter-name">
                <label for="nameFilter">Filter by Name:</label>
                <select id="nameFilter">
                    <option value="">Select a Name</option>
                    <?php while ($name = $nameResult->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($name['full_name']); ?>"><?php echo htmlspecialchars($name['full_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Filter by Category -->
            <div class="filter-category">
                <label for="categoryFilter">Filter by Category:</label>
                <select id="categoryFilter">
                    <option value="">Select a Category</option>
                    <?php while ($category = $categoryResult->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($category['category']); ?>"><?php echo htmlspecialchars($category['category']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Review Submission Form -->
            <div class="review-form">
                <h3>Submit a Review</h3>
                <form action="../actions/submit_review.php" method="POST">
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <select id="rating" name="rating" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea id="comment" name="comment" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="reference">Review for (Service):</label>
                        <select id="reference" name="reference" required>
                            <option value="">Select a Service</option>
                            <?php 
                            $referenceResult->data_seek(0); // Reset pointer
                            while ($reference = $referenceResult->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($reference['service_id']); ?>">
                                    <?php echo htmlspecialchars($reference['title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit">Submit Review</button>
                </form>
            </div>
        </aside>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('searchReview');
            const categoryFilter = document.getElementById('categoryFilter');
            const nameFilter = document.getElementById('nameFilter');
            const reviews = document.querySelectorAll('.review-item');

            // Function to filter reviews
            function filterReviews() {
                const category = categoryFilter.value.toLowerCase();
                const name = nameFilter.value.toLowerCase();

                reviews.forEach(review => {
                    const reviewCategory = review.getAttribute('data-category')?.toLowerCase() || '';
                    const reviewName = review.getAttribute('data-name')?.toLowerCase() || '';

                    // Check if review matches selected filters
                    const matchesCategory = category ? reviewCategory.includes(category) : true;
                    const matchesName = name ? reviewName.includes(name) : true;

                    if (matchesCategory && matchesName) {
                        review.style.display = 'block';
                    } else {
                        review.style.display = 'none';
                    }
                });
            }

            // Listen for filter changes
            categoryFilter.addEventListener('change', filterReviews);
            nameFilter.addEventListener('change', filterReviews);

            // Implement search functionality for instant filtering
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                reviews.forEach(review => {
                    const reviewText = review.textContent.toLowerCase();
                    if (reviewText.includes(searchTerm)) {
                        review.style.display = 'block';
                    } else {
                        review.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>