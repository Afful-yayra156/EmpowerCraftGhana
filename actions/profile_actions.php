<?php

require_once '../db/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to perform this action";
    header("Location: ../view/login.php");
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: ../view/profile.php");
        exit();
    }
    
    // Check if email already exists (excluding current user)
    $email_check_query = "SELECT * FROM users WHERE email='$email' AND user_id != $user_id";
    $result = mysqli_query($conn, $email_check_query);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = "Email already exists";
        header("Location: ../view/profile.php");
        exit();
    }
    
    // Update user profile
    $update_query = "UPDATE users SET 
                     first_name = '$first_name', 
                     last_name = '$last_name', 
                     email = '$email', 
                     bio = '$bio', 
                     phone_number = '$phone_number', 
                     location = '$location' 
                     WHERE user_id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Profile updated successfully";
        // Update session data
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;
    } else {
        $_SESSION['error'] = "Error updating profile: " . mysqli_error($conn);
    }
    
    header("Location: ../view/profile.php");
    exit();
}

// Function to get user profile data
function getUserProfile($conn, $user_id) {
    $query = "SELECT * FROM users WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

// Function to get user type label
function getUserTypeLabel($user_type) {
    switch ($user_type) {
        case 'artisan':
            return 'Artisan';
        case 'client':
            return 'Client';
        case 'admin':
            return 'Administrator';
        case 'account_manager':
            return 'Account Manager';
        default:
            return ucfirst($user_type);
    }
}

// Function to format user full name
function getUserFullName($first_name, $last_name) {
    return $first_name . ' ' . $last_name;
}

// Function to get default profile picture if none exists
function getProfilePicturePath($profile_picture) {
    if (!empty($profile_picture) && file_exists("../assets/images/" . $profile_picture)) {
        return "../assets/images/" . $profile_picture;
    }
    
    return "../assets/images/velma.jpg";
}