<?php
session_start();
include('../db/config.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $referenceId = $_POST['reference'];
    $reviewerId = $_SESSION['user_id']; // Assuming logged-in user is the reviewer

    // Save the review to the database
    $sql = "INSERT INTO reviews (reviewer_id, reference_type, reference_id, rating, comment) VALUES (?, 'service', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $reviewerId, $referenceId, $rating, $comment);

    if ($stmt->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Error submitting review!";
    }
}
?>