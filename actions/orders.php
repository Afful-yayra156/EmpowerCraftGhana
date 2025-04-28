<?php
// Start session to maintain user login state
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "empowerhub";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Return error if not logged in
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in', 'redirect' => 'login.php']);
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getOrders($conn, $userId);
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'cancel':
                    cancelOrder($conn, $userId, $data['order_id']);
                    break;
                case 'filter':
                    filterOrders($conn, $userId, $data);
                    break;
                default:
                    echo json_encode(['error' => 'Invalid action']);
            }
        } else {
            echo json_encode(['error' => 'Action not specified']);
        }
        break;
    default:
        echo json_encode(['error' => 'Method not allowed']);
}

/**
 * Get all orders for the user
 */
function getOrders($conn, $userId) {
    // SQL query to get orders and their items
    $sql = "SELECT o.order_id, o.order_date, o.total_amount, o.status, o.shipping_address, 
                   o.shipping_method, o.tracking_number, o.notes,
                   oi.order_item_id, oi.checkout_id, oi.quantity, oi.price_per_unit, oi.status as item_status,
                   l.title as item_name, 
                   p.payment_method
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN checkout l ON oi.checkout_id = l.checkout_id
            LEFT JOIN payments p ON p.reference_id = o.order_id AND p.payment_type = 'order'
            WHERE o.buyer_id = ?
            ORDER BY o.order_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    $orderMap = [];
    
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['order_id'];
        
        // If this is the first time we're seeing this order
        if (!isset($orderMap[$orderId])) {
            $orderMap[$orderId] = count($orders);
            
            // Format the order ID for display (ORD-2025-XXX)
            $formattedOrderId = 'ORD-' . date('Y', strtotime($row['order_date'])) . '-' . 
                                str_pad($orderId, 3, '0', STR_PAD_LEFT);
            
            // Create the basic order structure
            $orders[] = [
                'id' => $formattedOrderId,
                'raw_id' => $orderId,
                'date' => $row['order_date'],
                'total_amount' => $row['total_amount'],
                'status' => mapOrderStatus($row['status']),
                'shipping_address' => $row['shipping_address'],
                'shipping_method' => $row['shipping_method'],
                'tracking_number' => $row['tracking_number'],
                'notes' => $row['notes'],
                'payment_method' => formatPaymentMethod($row['payment_method']),
                'items' => []
            ];
        }
        
        // Add this item to the order
        $orders[$orderMap[$orderId]]['items'][] = [
            'order_item_id' => $row['order_item_id'],
            'checkout_id' => $row['checkout_id'],
            'item' => $row['item_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price_per_unit'],
            'status' => $row['item_status']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode(['orders' => $orders]);
}

/**
 * Cancel an order
 */
function cancelOrder($conn, $userId, $orderId) {
    // First, check if the order belongs to the user
    $sql = "SELECT order_id FROM orders WHERE order_id = ? AND buyer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Order not found or not authorized']);
        return;
    }
    
    // Only allow cancellation of orders that are in pending or processing status
    $sql = "UPDATE orders SET status = 'cancelled' 
            WHERE order_id = ? AND (status = 'pending' OR status = 'processing')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $success = $stmt->execute();
    
    if ($success && $stmt->affected_rows > 0) {
        // Also update order items
        $sql = "UPDATE order_items SET status = 'cancelled' WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        
        // Create a notification for the seller
        createCancellationNotification($conn, $orderId);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Failed to services.php or order not in cancellable state']);
    }
}

/**
 * Filter orders based on search criteria
 */
function filterOrders($conn, $userId, $filterData) {
    $status = isset($filterData['status']) ? $filterData['status'] : 'all';
    $sortBy = isset($filterData['sortBy']) ? $filterData['sortBy'] : 'newest';
    $search = isset($filterData['search']) ? $filterData['search'] : '';
    
    // Base SQL query
    $sql = "SELECT o.order_id, o.order_date, o.total_amount, o.status, o.shipping_address, 
                   o.shipping_method, o.tracking_number, o.notes,
                   oi.order_item_id, oi.checkout_id, oi.quantity, oi.price_per_unit, oi.status as item_status,
                   l.title as item_name, 
                   p.payment_method
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN checkout l ON oi.checkout_id = l.checkout_id
            LEFT JOIN payments p ON p.reference_id = o.order_id AND p.payment_type = 'order'
            WHERE o.buyer_id = ?";
    
    $params = [$userId];
    $types = "i";
    
    // Add status filter if not 'all'
    if ($status !== 'all') {
        $sql .= " AND o.status = ?";
        $params[] = strtolower($status);
        $types .= "s";
    }
    
    // Add search term filter
    if (!empty($search)) {
        $searchTerm = "%$search%";
        $sql .= " AND (l.title LIKE ? OR CONCAT('ORD-', YEAR(o.order_date), '-', LPAD(o.order_id, 3, '0')) LIKE ?)";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }
    
    // Add sorting
    $sql .= " ORDER BY o.order_date " . ($sortBy === 'newest' ? 'DESC' : 'ASC');
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    $orderMap = [];
    
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['order_id'];
        
        // If this is the first time we're seeing this order
        if (!isset($orderMap[$orderId])) {
            $orderMap[$orderId] = count($orders);
            
            // Format the order ID for display
            $formattedOrderId = 'ORD-' . date('Y', strtotime($row['order_date'])) . '-' . 
                                str_pad($orderId, 3, '0', STR_PAD_LEFT);
            
            // Create the basic order structure
            $orders[] = [
                'id' => $formattedOrderId,
                'raw_id' => $orderId,
                'date' => $row['order_date'],
                'total_amount' => $row['total_amount'],
                'status' => mapOrderStatus($row['status']),
                'shipping_address' => $row['shipping_address'],
                'shipping_method' => $row['shipping_method'],
                'tracking_number' => $row['tracking_number'],
                'notes' => $row['notes'],
                'payment_method' => formatPaymentMethod($row['payment_method']),
                'items' => []
            ];
        }
        
        // Add this item to the order
        $orders[$orderMap[$orderId]]['items'][] = [
            'order_item_id' => $row['order_item_id'],
            'checkout_id' => $row['checkout_id'],
            'item' => $row['item_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price_per_unit'],
            'status' => $row['item_status']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode(['orders' => $orders]);
}

/**
 * Create notification for sellers when an order is cancelled
 */
function createCancellationNotification($conn, $orderId) {
    // Get all unique sellers for this order
    $sql = "SELECT DISTINCT seller_id FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $sellerId = $row['seller_id'];
        
        // Create notification
        $title = "Order Cancellation";
        $message = "Order #ORD-" . date('Y') . '-' . str_pad($orderId, 3, '0', STR_PAD_LEFT) . " has been cancelled by the customer.";
        $type = "order_cancelled";
        
        $sql = "INSERT INTO notifications (user_id, type, title, message, reference_type, reference_id) 
                VALUES (?, ?, ?, ?, 'order', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $sellerId, $type, $title, $message, $orderId);
        $stmt->execute();
    }
}

/**
 * Map database status to display status
 */
function mapOrderStatus($status) {
    switch ($status) {
        case 'pending':
            return 'Processing';
        case 'processing':
            return 'Processing';
        case 'shipped':
            return 'Shipped';
        case 'delivered':
            return 'Delivered';
        case 'cancelled':
            return 'Cancelled';
        default:
            return 'Processing';
    }
}

/**
 * Format payment method for display
 */
function formatPaymentMethod($method) {
    switch ($method) {
        case 'mobile_money':
            return 'Mobile Money';
        case 'card':
            return 'Card Payment';
        case 'bank_transfer':
            return 'Bank Transfer';
        default:
            return 'Mobile Money';
    }
}

// Close the database connection
$conn->close();
?>