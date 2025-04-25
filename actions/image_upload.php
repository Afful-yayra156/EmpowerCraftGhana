<?php
session_start();
require_once '../db/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile image upload via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $response = ['status' => 'error', 'message' => 'Unknown error occurred'];
    
    $file = $_FILES['profile_image'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $response = ['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG and GIF are allowed.'];
    } elseif ($file['size'] > $max_size) {
        $response = ['status' => 'error', 'message' => 'File too large. Maximum size is 5MB.'];
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $response = ['status' => 'error', 'message' => 'File upload failed. Error code: ' . $file['error']];
    } else {
        // Create upload directory if it doesn't exist
        $upload_dir = '../assets/images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $filename = $user_id . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Update user profile in database
            $filename_db = mysqli_real_escape_string($conn, $filename);
            
            // Get previous profile picture filename
            $query = "SELECT profile_picture FROM users WHERE user_id = $user_id";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $old_picture = $row['profile_picture'];
            
            // Update database with new profile picture
            $update_query = "UPDATE users SET profile_picture = '$filename_db' WHERE user_id = $user_id";
            
            if (mysqli_query($conn, $update_query)) {
                // Delete old profile picture if it exists
                if (!empty($old_picture) && file_exists($upload_dir . $old_picture)) {
                    unlink($upload_dir . $old_picture);
                }
                
                $response = [
                    'status' => 'success',
                    'message' => 'Profile picture updated successfully',
                    'image_path' => '../assets/images/' . $filename
                ];
            } else {
                $response = ['status' => 'error', 'message' => 'Database update failed: ' . mysqli_error($conn)];
                // Remove uploaded file if database update fails
                if (file_exists($destination)) {
                    unlink($destination);
                }
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to move uploaded file'];
        }
    }
    
    echo json_encode($response);
    exit();
}

// If not a POST request or no file uploaded
echo json_encode(['status' => 'error', 'message' => 'No file uploaded or invalid request']);