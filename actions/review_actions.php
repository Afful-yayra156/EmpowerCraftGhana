<?php
session_start();
require_once '../db/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to perform this action";
    header("Location: ../view/login.php");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_id = $_SESSION['user_id'];
    $reviewed_id = mysqli_real_escape_string($conn, $_POST['reviewed_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Validate input
    if (empty($reviewed_id) || empty($rating)) {
        $_SESSION['error'] = "Required fields are missing";
        header("Location: ../view/profile.php?id=" . $reviewed_id);
        exit();
    }
    
    // Validate rating
    $rating = floatval($rating);
    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = "Rating must be between 1 and 5";
        header("Location: ../view/profile.php?id=" . $reviewed_id);
        exit();
    }
    
    // Prevent self-review
    if ($reviewer_id == $reviewed_id) {
        $_SESSION['error'] = "You cannot review yourself";
        header("Location: ../view/profile.php?id=" . $reviewed_id);
        exit();
    }
    
    // Check if user already reviewed this person
    $check_query = "SELECT * FROM reviews 
                   WHERE reviewer_id = $reviewer_id 
                   AND reviewed_id = $reviewed_id 
                   AND reference_type = 'user'";
    $result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($result) > 0) {
        // Update existing review
        $review = mysqli_fetch_assoc($result);
        $review_id = $review['review_id'];
        
        $update_query = "UPDATE reviews 
                        SET rating = $rating, 
                            comment = '$comment' 
                        WHERE review_id = $review_id";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Review updated successfully";
        } else {
            $_SESSION['error'] = "Error updating review: " . mysqli_error($conn);
        }
    } else {
        // Insert new review
        $insert_query = "INSERT INTO reviews (reviewer_id, reviewed_id, reference_type, reference_id, rating, comment) 
                        VALUES ($reviewer_id, $reviewed_id, 'user', $reviewed_id, $rating, '$comment')";
        
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['success'] = "Review submitted successfully";
        } else {
            $_SESSION['error'] = "Error submitting review: " . mysqli_error($conn);
        }
    }
    
    // Update user's average rating
    updateUserAverageRating($conn, $reviewed_id);
    
    header("Location: ../view/profile.php?id=" . $reviewed_id);
    exit();
}

// Function to get user reviews
function getUserReviews($conn, $user_id) {
    $query = "SELECT r.*, u.first_name, u.last_name, u.profile_picture 
              FROM reviews r
              JOIN users u ON r.reviewer_id = u.user_id
              WHERE r.reviewed_id = $user_id 
              AND r.reference_type = 'user'
              ORDER BY r.creation_date DESC";
    
    $result = mysqli_query($conn, $query);
    $reviews = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reviews[] = $row;
        }
    }
    
    return $reviews;
}

// Function to check if user has already reviewed another user
function hasUserReviewed($conn, $reviewer_id, $reviewed_id) {
    $query = "SELECT * FROM reviews 
              WHERE reviewer_id = $reviewer_id 
              AND reviewed_id = $reviewed_id 
              AND reference_type = 'user'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

// Function to update user's average rating
function updateUserAverageRating($conn, $user_id) {
    $query = "SELECT AVG(rating) as avg_rating FROM reviews 
              WHERE reviewed_id = $user_id 
              AND reference_type = 'user'";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $avg_rating = $row['avg_rating'] ? $row['avg_rating'] : 0;
    
    $update_query = "UPDATE users SET average_rating = $avg_rating WHERE user_id = $user_id";
    mysqli_query($conn, $update_query);
}

// Function to get star rating HTML
function getStarRatingHTML($rating) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    
    $html = '';
    
    // Full stars
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '★';
    }
    
    // Half star
    if ($half_star) {
        $html .= '½';
    }
    
    // Empty stars
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '☆';
    }
    
    return $html;
}