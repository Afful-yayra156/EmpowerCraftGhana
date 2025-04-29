<?php
session_start();
include('../db/config.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Error: You must be logged in to submit a review.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $comment = trim(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING));
    $referenceId = filter_input(INPUT_POST, 'reference', FILTER_VALIDATE_INT);
    $reviewerId = $_SESSION['user_id'];

    // Validate inputs
    if (!$rating || $rating < 1 || $rating > 5) {
        http_response_code(400);
        echo "Error: Rating must be between 1 and 5.";
        exit;
    }

    if (empty($comment) || strlen($comment) > 1000) {
        http_response_code(400);
        echo "Error: Comment is required and must be less than 1000 characters.";
        exit;
    }

    if (!$referenceId) {
        http_response_code(400);
        echo "Error: Invalid service selected.";
        exit;
    }

    // Verify service exists and get the service provider's user_id (reviewed_id)
    $sql = "SELECT user_id FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $referenceId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(400);
        echo "Error: Selected service does not exist.";
        exit;
    }

    $service = $result->fetch_assoc();
    $reviewedId = $service['user_id'];

    // Save the review to the database
    $sql = "INSERT INTO reviews (reviewer_id, reviewed_id, reference_type, reference_id, rating, comment) VALUES (?, ?, 'service', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $reviewerId, $reviewedId, $referenceId, $rating, $comment);

    if ($stmt->execute()) {
        http_response_code(200);
        echo "Review submitted successfully!";
    } else {
        http_response_code(500);
        echo "Error submitting review: " . $conn->error;
    }

    $stmt->close();
}
$conn->close();
?>