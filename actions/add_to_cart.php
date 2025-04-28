<?php
session_start();
include '../db/config.php'; // Include your database connection

// Verify the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get data from the AJAX request
$serviceId = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
var_dump($_POST['service_id']); // Check what service_id is being received

$buyerId = $_SESSION['user_id'];
$orderDate = date('Y-m-d'); // Current date
$status = 'cart'; // 'cart' status indicates item is in the cart

// Get the service price from the database
$serviceSql = "SELECT price FROM services WHERE service_id = ?";
$stmtService = $conn->prepare($serviceSql);
$stmtService->bind_param("i", $serviceId);
$stmtService->execute();
$result = $stmtService->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Service not found']);
    exit;
}

$serviceData = $result->fetch_assoc();
$totalAmount = $serviceData['price'];


$status = 'cart';
$sql = "INSERT INTO orders (buyer_id, service_id, order_date, total_amount, status) 
        VALUES (?, ?, ?, ?, ?)";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $buyerId, $serviceId, $orderDate, $totalAmount, $status);

// Execute the statement
if ($stmt->execute()) {
    // Success
    echo json_encode(['success' => true, 'message' => 'Service added to cart']);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to create order: ' . $stmt->error
    ]);
}

// Close the statement and connection
$stmtService->close();
$stmt->close();
$conn->close();
?>
