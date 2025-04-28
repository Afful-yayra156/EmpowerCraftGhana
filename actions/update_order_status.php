<?php
session_start();
include '../db/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if the order ID and status are provided
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
    exit;
}

$orderId = $_POST['order_id'];
$status = $_POST['status'];

// Prepare the SQL query to update the order status
$sql = "UPDATE orders SET status = ? WHERE order_id = ? AND buyer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $status, $orderId, $_SESSION['user_id']);

// Execute the update query
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
