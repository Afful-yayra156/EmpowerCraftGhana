<?php
session_start(); // Start the session at the top before any HTML output
include '../db/config.php';
include '../assets/functions/service_functions.php'; // Optional: if functions are modularized
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | EmpowerSkills Ghana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/services.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Available Services</h2>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="services.php" class="active">Services</a>
            <a href="checkout.php">checkout</a>
            <a href="orders.php">Orders</a>
            <a href="messages.php">Messages</a>
        </div>

        <div class="content">
            <div class="actions-container">
                <h3>All Services</h3>
                <button id="showAddService" class="btn btn-primary">Add New Service</button>
            </div>
            
            <?php
            // Display success or error messages if they exist in the session
            if (isset($_SESSION['message'])) {
                $message_type = $_SESSION['message_type'] ?? 'success';
                echo "<div class='alert alert-{$message_type}'>{$_SESSION['message']}</div>";
                // Clear the message from session
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>



            <div class="container mt-5">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-6">
                        <input type="text" class="form-control w-100" id="searchServiceText" placeholder="Search for Services" style="width: 200px;">
                    </div>
                    <!-- Category Select -->
                    <div class="col-md-6">
                        <select id="categoryFilter" class="form-control" style="width: 200px;">
                            <option value="">Select Category</option>
                            <?php
                            // Fetch distinct categories from the database
                            $category_sql = "SELECT DISTINCT category FROM services";
                            $category_result = $conn->query($category_sql);

                            // Check if categories are available
                            if ($category_result->num_rows > 0) {
                                while ($category = $category_result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($category['category']) . "'>" . htmlspecialchars($category['category']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>No categories available</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>


        <div id="addServiceForm" class="add-service">
            <h3 style="margin-bottom: 16px;">Add a New Service</h3>
            <form id="serviceForm" action="../actions/add_service.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="serviceTitle" class="form-label">Service Title</label>
                    <input type="text" id="serviceTitle" name="title" class="form-control" placeholder="Enter service title" required>
                </div>
                <div class="form-group">
                    <label for="serviceDescription" class="form-label">Description</label>
                    <textarea id="serviceDescription" name="description" class="form-control" placeholder="Enter service description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" id="category" name="category" class="form-control" placeholder="Enter service category" required>
                </div>

                <div class="form-group">
                    <label for="service_image" class="form-label">Upload Descriptive Image(s)</label>
                    <input type="file" class="form-control" name="service_image[]" id="service_image" accept="image/*" multiple>
                </div>
                <div class="form-group">
                    <label for="servicePrice" class="form-label">Price (GHS)</label>
                    <input type="number" id="servicePrice" name="price" class="form-control" placeholder="Enter price" min="0" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Add Service</button>
                    <button type="button" onclick="cancelAdd()" class="btn btn-outline">Cancel</button>
                </div>
            </form>
        </div>

        <div class="service-checkout grid-2-cols" style="margin-top: 20px;">
            <?php
            // Fetch and display the services
            $sql = "SELECT s.service_id, s.title, s.price, s.image_path, s.category
                    FROM services s 
                    GROUP BY s.service_id"; 

            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    ?>
                    <div class="service-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px; display: flex; flex-direction: column; gap: 15px;">
                        <?php if (!empty($row['image_path'])): ?>
                            <div class="image-carousel" data-service-id="<?= $row['service_id'] ?>">
                                <div class="carousel-images">
                                    <img src="../<?= $row['image_path'] ?>" alt="<?= htmlspecialchars($row['title']) ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;">
                                </div>
                                <div class="carousel-controls" style="display: none;">
                                    <button class="prev-btn" onclick="moveCarousel(<?= $row['service_id'] ?>, -1)">❮</button>
                                    <button class="next-btn" onclick="moveCarousel(<?= $row['service_id'] ?>, 1)">❯</button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h4 style="margin: 0;"><?= htmlspecialchars($row['title']) ?></h4>
                            <p style="margin: 4px 0;">Category: <?= htmlspecialchars($row['category']) ?></p>
                            <p style="margin: 4px 0;">GHS <?= number_format($row['price'], 2) ?></p>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addToCart(<?= $row['service_id'] ?>)">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>


                    </div>
                <?php endwhile;
            else:
                echo "<p>No services available.</p>";
            endif;

            $conn->close();
            ?>
        </div>
    </div>

    <script>
        const addServiceForm = document.getElementById('addServiceForm');
        const showAddServiceBtn = document.getElementById('showAddService');

        // Show the form when Add New Service button is clicked
        showAddServiceBtn.addEventListener('click', function() {
            addServiceForm.style.display = 'block';
        });

        // Hide the form when Cancel button is clicked
        function cancelAdd() {
            addServiceForm.style.display = 'none';
            document.getElementById('serviceForm').reset();
        }

        document.getElementById('searchServiceText').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const checkout = document.querySelectorAll('.service-checkout > div');

            checkout.forEach(service => {
                const title = service.querySelector('h4')?.textContent.toLowerCase() || '';
                if (title.includes(searchTerm)) {
                    service.style.display = 'flex';
                } else {
                    service.style.display = 'none';
                }
            });
        });


        document.getElementById('categoryFilter').addEventListener('change', function() {
            const selectedCategory = this.value.toLowerCase();
            const checkout = document.querySelectorAll('.service-checkout > div');

            checkout.forEach(service => {
                const category = service.querySelector('p')?.textContent.toLowerCase() || '';
                if (selectedCategory === '' || category.includes(selectedCategory)) {
                    service.style.display = 'flex';
                } else {
                    service.style.display = 'none';
                }
            });
        });

        const carousels = {}; // to track index per service

        function moveCarousel(serviceId, direction) {
            const carousel = document.querySelector(`.image-carousel[data-service-id="${serviceId}"]`);
            if (!carousel) return;

            const images = carousel.querySelectorAll('.carousel-image');
            if (!images.length) return;

            if (carousels[serviceId] === undefined) {
                carousels[serviceId] = 0;
            }

            carousels[serviceId] = (carousels[serviceId] + direction + images.length) % images.length;

            images.forEach((img, index) => {
                img.style.display = (index === carousels[serviceId]) ? 'block' : 'none';
            });
        }

function addToCart(serviceId) {
    // Show loading indicator or disable the button
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    // Make an AJAX request to add the item to cart
    fetch('../actions/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `service_id=${serviceId}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Service added to cart successfully!');
            
            // Redirect to orders page after a short delay
            setTimeout(() => {
                window.location.href = 'orders.php';
            }, 1000); // Wait 1 second before redirecting
        } else {
            alert('Error: ' + data.message);
            // Reset button
            button.disabled = false;
            button.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding to cart. Please try again.');
        // Reset button
        button.disabled = false;
        button.innerHTML = originalText;
    });
}


    </script>
</body>
</html>
