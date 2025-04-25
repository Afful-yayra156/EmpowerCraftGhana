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
  
  <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLW1lc3NhZ2Utc3F1YXJlIj48cGF0aCBkPSJNMjEgMTVhMiAyIDAgMCAxLTIgMkg3bC00IDR2LTRhMiAyIDAgMCAxLTItMlY1YTIgMiAwIDAgMSAyLTJoMTRhMiAyIDAgMC