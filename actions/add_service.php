<?php
// Database connection
include '../db/config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 1;
    $duration = isset($_POST['duration']) ? trim($_POST['duration']) : 'Not specified';
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $category = $_POST['category']; // Ensure this is sanitized/validated
    $status = 'active';
    $image_path = null;

    // Validate input
    if (empty($title) || empty($description) || $price <= 0) {
        echo "Please complete all required fields.";
        exit;
    }

    try {
        // Handle image upload (only one image)
       // Handle multiple image uploads
        if (isset($_FILES['service_image']) && is_array($_FILES['service_image']['name'])) {
            $upload_dir = '../uploads/services/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Process the first image only (or you can process all and store multiple paths)
            $file_count = count($_FILES['service_image']['name']);
            
            if ($file_count > 0 && $_FILES['service_image']['error'][0] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['service_image']['tmp_name'][0]; // First file
                $name = basename($_FILES['service_image']['name'][0]);
                $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                $new_filename = 'service_' . time() . '_' . uniqid() . '.' . $file_extension;
                $destination = $upload_dir . $new_filename;
                
                if (move_uploaded_file($tmp_name, $destination)) {
                    $image_path = 'uploads/services/' . $new_filename; // relative path
                } else {
                    throw new Exception("Failed to upload image.");
                }
            }
        }

        // Insert service into services table including image path
        $stmt = $conn->prepare("INSERT INTO services 
        (user_id, category_id, title, description ,category, price, duration, availability, is_featured, creation_date, last_updated, status, image_path) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $creation_date = date('Y-m-d H:i:s');
        $last_updated = date('Y-m-d H:i:s');

        // Set image path to null if not set
        $image_path = isset($image_path) ? $image_path : null;

        $stmt->bind_param("iisssdssissss", $user_id, $category_id, $title, $description, $category, $price, $duration, $availability, $is_featured, $creation_date, $last_updated, $status, $image_path);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting service: " . $stmt->error);
        }

        // Redirect after successful insertion
        $_SESSION['service_added_message'] = "Service added successfully.";
        header("Location: ../view/services.php");
        exit;

    } catch (Exception $e) {
        // Log error for troubleshooting
        error_log($e->getMessage(), 3, '/path/to/error.log');
        echo "Error: " . $e->getMessage();
        exit;
    } finally {
        $stmt->close();
        $conn->close();
    }
}
?>
