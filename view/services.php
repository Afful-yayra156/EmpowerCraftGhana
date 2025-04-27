<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | EmpowerSkills Ghana</title>
    <link rel="stylesheet" href="../assets/css/services.css">
    
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Available Services</h2>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="services.html" class="active">Services</a>
            <a href="listings.html">Listings</a>
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
                <div class="col-md-4">
                    <input type="text" class="form-control w-100" id="searchService" placeholder="Search for Services"  style="width: 200px";>
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

            <!-- Add this right after the Add Service form -->
            <div class="service-listings  grid-2-cols" style="margin-top: 20px;">
                <?php
                include '../db/config.php';
                include '../assets/functions/service_functions.php'; // optional: if functions are modularized
                session_start();

                $sql = "SELECT s.service_id, s.title, s.price, s.image_path s.category
                        FROM services s 
                        GROUP BY s.service_id";  // Fetch one image per service (if available)


                $result = $conn->query($sql);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        ?>
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                            <?php if (!empty($row['image_path'])): ?>
                                <img src="../<?= $row['image_path'] ?>" alt="<?= htmlspecialchars($row['title']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
                            <div>
                                <h4 style="margin: 0;"><?= htmlspecialchars($row['title']) ?></h4>
                                <p style="margin: 4px 0;">Category: <?= htmlspecialchars($row['title']) ?></p>
                                <p style="margin: 4px 0;">GHS <?= number_format($row['price'], 2) ?></p>
                            </div>
                        </div>
                    <?php
                    endwhile;
                else:
                    echo "<p>No services available.</p>";
                endif;

                $conn->close();
                ?>
            </div>

        </div>
    </div>

    <script>
        // Get elements
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

        document.getElementById('searchService').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const listings = document.querySelectorAll('.service-listings > div');

        listings.forEach(service => {
            const title = service.querySelector('h4')?.textContent.toLowerCase() || '';
            if (title.includes(searchTerm)) {
                service.style.display = 'flex'; // or 'block' if you used block layout
            } else {
                service.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>