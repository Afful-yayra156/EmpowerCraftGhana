<?php
session_start();
require '../db/config.php'; // your database connection file

// Assuming you store user ID in session
$userId = $_SESSION['user_id'];

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = $_FILES['profile_picture']['type'];

    if (in_array($fileType, $allowedTypes)) {
        $uploadsDir = 'uploads/profile_pictures/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
        $targetFilePath = $uploadsDir . $fileName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
            // Save new file path to database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("si", $targetFilePath, $userId);

            if ($stmt->execute()) {
                header("Location: ../view/profile.php"); // redirect back to profile
                exit;
            } else {
                echo "Error updating profile picture.";
            }
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "Invalid file type.";
    }
} else {
    echo "No file uploaded or upload error.";
}
?>
