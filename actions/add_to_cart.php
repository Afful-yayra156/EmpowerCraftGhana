<?php
session_start();
include '../db/config.php'; // Include your database connection

header('Content-Type: application/json'); // Tell browser it's JSON

// Verify the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get data from the AJAX request
$serviceId = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;

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

// Insert into orders
$sql = "INSERT INTO orders (buyer_id, service_id, order_date, total_amount, status) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $buyerId, $serviceId, $orderDate, $totalAmount, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Service added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $stmt->error]);
}

// Close resources
$stmtService->close();
$stmt->close();
$conn->close();
?>
