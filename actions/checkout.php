<?php
// Start a session for tracking user data
session_start();

// Database connection settings
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "empowerhub";

// Flutterwave API settings
$flw_public_key = "FLWPUBK_TEST-xxxxxxxxxxxxxxxxxxxxx-X"; // Replace with your actual Flutterwave public key
$flw_secret_key = "FLWSECK_TEST-xxxxxxxxxxxxxxxxxxxxx-X"; // Replace with your actual Flutterwave secret key

// Connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response array
$response = [
    'status' => 'error',
    'message' => '',
    'data' => null
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If this is an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $response['message'] = 'You must be logged in to proceed with checkout';
        echo json_encode($response);
        exit;
    } else {
        // Redirect to login page if not AJAX
        header("Location: login.php?redirect=checkout.php");
        exit;
    }
}

// Define functions for checkout process
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Basic validation for Ghana phone numbers
    return preg_match('/^(\+233|0)[0-9]{9}$/', $phone);
}

function generateReference() {
    return 'EmpowerSkills-' . time() . '-' . mt_rand(1000, 9999);
}

function createOrder($conn, $userId, $amount, $shippingAddress = null) {
    $sql = "INSERT INTO orders (buyer_id, total_amount, shipping_address) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $userId, $amount, $shippingAddress);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

function createPayment($conn, $paymentType, $referenceId, $payerId, $payeeId, $amount, $paymentMethod, $paymentDetails = null) {
    $paymentDetailsJson = $paymentDetails ? json_encode($paymentDetails) : null;
    $txRef = generateReference();
    
    $sql = "INSERT INTO payments (payment_type, reference_id, payer_id, payee_id, amount, payment_method, payment_details, transaction_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiidsss", $paymentType, $referenceId, $payerId, $payeeId, $amount, $paymentMethod, $paymentDetailsJson, $txRef);
    
    if ($stmt->execute()) {
        return [
            'payment_id' => $conn->insert_id,
            'tx_ref' => $txRef
        ];
    } else {
        return false;
    }
}

function verifyFlutterwavePayment($txId, $flw_secret_key) {
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txId}/verify",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $flw_secret_key,
            "Content-Type: application/json"
        ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        return [
            'status' => 'error',
            'message' => "cURL Error: " . $err
        ];
    } else {
        return json_decode($response, true);
    }
}

function updatePaymentStatus($conn, $paymentId, $status, $transactionId = null) {
    $sql = "UPDATE payments SET status = ?";
    $params = [$status];
    $types = "s";
    
    if ($transactionId) {
        $sql .= ", transaction_id = ?";
        $params[] = $transactionId;
        $types .= "s";
    }
    
    $sql .= " WHERE payment_id = ?";
    $params[] = $paymentId;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    return $stmt->execute();
}

function updateOrderStatus($conn, $orderId, $status) {
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $orderId);
    
    return $stmt->execute();
}

function getUserDetails($conn, $userId) {
    $sql = "SELECT user_id, email, phone_number, first_name, last_name, location FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function getServiceDetails($conn, $serviceId) {
    $sql = "SELECT s.*, u.user_id as artisan_id FROM services s 
            JOIN users u ON s.user_id = u.user_id 
            WHERE s.service_id = ? AND s.status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function getListingDetails($conn, $listingId) {
    $sql = "SELECT l.*, u.user_id as seller_id FROM listings l 
            JOIN users u ON l.user_id = u.user_id 
            WHERE l.listing_id = ? AND l.status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $listingId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function createOrderItem($conn, $orderId, $listingId, $sellerId, $quantity, $pricePerUnit) {
    $sql = "INSERT INTO order_items (order_id, listing_id, seller_id, quantity, price_per_unit) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiid", $orderId, $listingId, $sellerId, $quantity, $pricePerUnit);
    
    return $stmt->execute();
}

function createBooking($conn, $serviceId, $clientId, $artisanId, $startTime = null, $endTime = null, $notes = null) {
    $sql = "INSERT INTO bookings (service_id, client_id, artisan_id, start_time, end_time, notes) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisss", $serviceId, $clientId, $artisanId, $startTime, $endTime, $notes);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

function createNotification($conn, $userId, $type, $title, $message, $referenceType = null, $referenceId = null) {
    $sql = "INSERT INTO notifications (user_id, type, title, message, reference_type, reference_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $userId, $type, $title, $message, $referenceType, $referenceId);
    
    return $stmt->execute();
}

// Handle different payment scenarios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // For direct form submission to create payment
    if (isset($_POST['action']) && $_POST['action'] == 'initiate_payment') {
        $fullName = sanitizeInput($_POST['fullName']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $amount = floatval($_POST['amount']);
        $paymentMethod = sanitizeInput($_POST['payment_method']); // 'flutterwave' or 'mobile_money'
        
        // Validation
        if (empty($fullName) || empty($email) || empty($phone) || $amount <= 0) {
            $response['message'] = 'Please fill all fields with valid data';
            echo json_encode($response);
            exit;
        }
        
        if (!validateEmail($email)) {
            $response['message'] = 'Please enter a valid email address';
            echo json_encode($response);
            exit;
        }
        
        if (!validatePhone($phone)) {
            $response['message'] = 'Please enter a valid phone number';
            echo json_encode($response);
            exit;
        }
        
        // Get user details
        $userId = $_SESSION['user_id'];
        $user = getUserDetails($conn, $userId);
        
        if (!$user) {
            $response['message'] = 'User not found';
            echo json_encode($response);
            exit;
        }
        
        // Create order (assuming payment is for an order)
        $orderId = createOrder($conn, $userId, $amount);
        
        if (!$orderId) {
            $response['message'] = 'Failed to create order';
            echo json_encode($response);
            exit;
        }
        
        // If listing_id is provided, create order item
        if (isset($_POST['listing_id'])) {
            $listingId = intval($_POST['listing_id']);
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            
            $listing = getListingDetails($conn, $listingId);
            
            if (!$listing) {
                $response['message'] = 'Listing not found or inactive';
                echo json_encode($response);
                exit;
            }
            
            $sellerId = $listing['seller_id'];
            $pricePerUnit = $listing['price'];
            
            createOrderItem($conn, $orderId, $listingId, $sellerId, $quantity, $pricePerUnit);
            
            // For simplicity, assuming one seller per order
            $payeeId = $sellerId;
        } 
        // If service_id is provided, create booking
        elseif (isset($_POST['service_id'])) {
            $serviceId = intval($_POST['service_id']);
            $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;
            $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : null;
            $notes = isset($_POST['notes']) ? sanitizeInput($_POST['notes']) : null;
            
            $service = getServiceDetails($conn, $serviceId);
            
            if (!$service) {
                $response['message'] = 'Service not found or inactive';
                echo json_encode($response);
                exit;
            }
            
            $artisanId = $service['artisan_id'];
            
            $bookingId = createBooking($conn, $serviceId, $userId, $artisanId, $startTime, $endTime, $notes);
            
            if (!$bookingId) {
                $response['message'] = 'Failed to create booking';
                echo json_encode($response);
                exit;
            }
            
            $payeeId = $artisanId;
            $orderId = $bookingId; // Use booking ID as reference
            $paymentType = 'booking';
        } else {
            // Default case - direct payment without specific booking or order item
            $payeeId = 1; // Default admin/system ID for receiving payments
            $paymentType = 'order';
        }
        
        // Handle mobile money details if that's the payment method
        $paymentDetails = null;
        if ($paymentMethod == 'mobile_money' && isset($_POST['network'])) {
            $paymentDetails = [
                'network' => sanitizeInput($_POST['network']),
                'receiver_phone' => isset($_POST['receiverPhone']) ? sanitizeInput($_POST['receiverPhone']) : null
            ];
        }
        
        // Create payment record
        $paymentData = createPayment(
            $conn, 
            $paymentType, 
            $orderId, 
            $userId, 
            $payeeId, 
            $amount, 
            $paymentMethod, 
            $paymentDetails
        );
        
        if (!$paymentData) {
            $response['message'] = 'Failed to create payment record';
            echo json_encode($response);
            exit;
        }
        
        // Prepare Flutterwave transaction data
        $txRef = $paymentData['tx_ref'];
        $callbackUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/payment_callback.php";
        
        $flutterwaveData = [
            'tx_ref' => $txRef,
            'amount' => $amount,
            'currency' => 'GHS',
            'payment_options' => $paymentMethod == 'mobile_money' ? 'mobilemoneyghana' : 'card,banktransfer,ussd',
            'redirect_url' => $callbackUrl,
            'customer' => [
                'email' => $email,
                'phone_number' => $phone,
                'name' => $fullName,
            ],
            'meta' => [
                'payment_id' => $paymentData['payment_id'],
                'order_id' => $orderId,
                'user_id' => $userId
            ],
            'customizations' => [
                'title' => "EmpowerSkills Ghana",
                'description' => $paymentMethod == 'mobile_money' ? "Payment via Mobile Money" : "Payment via Card/Bank Transfer",
                'logo' => "https://yourwebsite.com/logo.png", // Update this with your actual logo URL
            ]
        ];
        
        // If mobile money, add specific details
        if ($paymentMethod == 'mobile_money' && isset($paymentDetails)) {
            $flutterwaveData['meta']['network'] = $paymentDetails['network'];
            $flutterwaveData['meta']['receiver_phone'] = $paymentDetails['receiver_phone'];
        }
        
        $response['status'] = 'success';
        $response['message'] = 'Payment initialized successfully';
        $response['data'] = [
            'flutterwave_data' => $flutterwaveData,
            'public_key' => $flw_public_key
        ];
        
        echo json_encode($response);
        exit;
    }
    
    // Handle Flutterwave webhook/callback
    else if (isset($_GET['action']) && $_GET['action'] == 'flutterwave_callback') {
        $status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
        $txRef = isset($_GET['tx_ref']) ? sanitizeInput($_GET['tx_ref']) : '';
        $transactionId = isset($_GET['transaction_id']) ? sanitizeInput($_GET['transaction_id']) : '';
        
        if ($status == 'successful' && !empty($txRef) && !empty($transactionId)) {
            // Verify payment with Flutterwave API
            $verification = verifyFlutterwavePayment($transactionId, $flw_secret_key);
            
            if ($verification['status'] == 'success' && $verification['data']['status'] == 'successful') {
                // Find the payment in our database
                $sql = "SELECT * FROM payments WHERE transaction_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $txRef);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $payment = $result->fetch_assoc();
                    $paymentId = $payment['payment_id'];
                    $referenceId = $payment['reference_id'];
                    $paymentType = $payment['payment_type'];
                    
                    // Update payment status
                    updatePaymentStatus($conn, $paymentId, 'completed', $transactionId);
                    
                    // Update order or booking status
                    if ($paymentType == 'order') {
                        updateOrderStatus($conn, $referenceId, 'processing');
                        
                        // Create notification for buyer
                        createNotification(
                            $conn,
                            $payment['payer_id'],
                            'order_payment',
                            'Payment Successful',
                            'Your payment for order #' . $referenceId . ' was successful.',
                            'order',
                            $referenceId
                        );
                        
                        // Create notification for seller(s)
                        $sql = "SELECT DISTINCT seller_id FROM order_items WHERE order_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $referenceId);
                        $stmt->execute();
                        $sellerResult = $stmt->get_result();
                        
                        while ($seller = $sellerResult->fetch_assoc()) {
                            createNotification(
                                $conn,
                                $seller['seller_id'],
                                'new_order',
                                'New Order Received',
                                'You have received a new order #' . $referenceId,
                                'order',
                                $referenceId
                            );
                        }
                    } else if ($paymentType == 'booking') {
                        // Update booking status
                        $sql = "UPDATE bookings SET status = 'confirmed' WHERE booking_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $referenceId);
                        $stmt->execute();
                        
                        // Get booking details
                        $sql = "SELECT * FROM bookings WHERE booking_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $referenceId);
                        $stmt->execute();
                        $bookingResult = $stmt->get_result();
                        $booking = $bookingResult->fetch_assoc();
                        
                        // Notify client
                        createNotification(
                            $conn,
                            $booking['client_id'],
                            'booking_confirmed',
                            'Booking Confirmed',
                            'Your booking #' . $referenceId . ' has been confirmed.',
                            'booking',
                            $referenceId
                        );
                        
                        // Notify artisan
                        createNotification(
                            $conn,
                            $booking['artisan_id'],
                            'new_booking',
                            'New Booking Received',
                            'You have received a new booking #' . $referenceId,
                            'booking',
                            $referenceId
                        );
                    }
                    
                    // Redirect to success page
                    header("Location: payment_success.php?reference=" . $txRef);
                    exit;
                } else {
                    // Payment not found in our database
                    header("Location: payment_failed.php?error=payment_not_found");
                    exit;
                }
            } else {
                // Payment verification failed
                header("Location: payment_failed.php?error=verification_failed");
                exit;
            }
        } else {
            // Payment was not successful
            header("Location: payment_failed.php?error=payment_failed");
            exit;
        }
    }
    
    // Invalid request
    else {
        $response['message'] = 'Invalid request';
        echo json_encode($response);
        exit;
    }
}

// If we're displaying the checkout form
else {
    // Get user details if logged in
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $user = getUserDetails($conn, $_SESSION['user_id']);
    }
    
    // Get service or listing details if provided in GET parameters
    $service = null;
    $listing = null;
    $itemType = null;
    $itemId = null;
    $amount = null;
    
    if (isset($_GET['service_id'])) {
        $serviceId = intval($_GET['service_id']);
        $service = getServiceDetails($conn, $serviceId);
        if ($service) {
            $itemType = 'service';
            $itemId = $serviceId;
            $amount = $service['price'];
        }
    } else if (isset($_GET['listing_id'])) {
        $listingId = intval($_GET['listing_id']);
        $listing = getListingDetails($conn, $listingId);
        if ($listing) {
            $itemType = 'listing';
            $itemId = $listingId;
            $amount = $listing['price'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | EmpowerSkills Ghana</title>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <style>
        :root {
            --primary: #1e8765;
            --primary-light: #e9f5f1;
            --primary-dark: #145c44;
            --accent: #3a7ca5;
            --accent-light: #d6ebf7;
            --neutral-dark: #333333;
            --neutral-mid: #717171;
            --neutral-light: #f7f9fa;
            --shadow: rgba(0, 0, 0, 0.08);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f3f9f7, #e3f2fd);
            color: var(--neutral-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 550px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px var(--shadow);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(to right, var(--primary), var(--accent));
            padding: 25px 0;
            text-align: center;
            position: relative;
        }
        
        .header h2 {
            color: white;
            font-weight: 600;
            font-size: 24px;
            margin: 0;
            letter-spacing: 0.3px;
        }
        
        .nav {
            display: flex;
            justify-content: center;
            background-color: white;
            padding: 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .nav a {
            text-decoration: none;
            color: var(--neutral-mid);
            font-weight: 500;
            padding: 16px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
            border-bottom: 2px solid transparent;
        }
        
        .nav a:hover, .nav a.active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }
        
        .form-container {
            padding: 25px 30px 30px;
        }
        
        .form-title {
            margin-bottom: 20px;
            font-size: 18px;
            color: var(--neutral-dark);
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--neutral-dark);
            margin-bottom: 6px;
        }
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dce1e4;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.2s;
            background-color: white;
            color: var(--neutral-dark);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 135, 101, 0.12);
        }
        
        input::placeholder {
            color: #b0b7bc;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 135, 101, 0.15);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .payment-options {
            margin-bottom: 15px;
        }
        
        .payment-options-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .payment-method-selector {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .payment-method-option {
            flex: 1;
            text-align: center;
            padding: 12px;
            border: 1px solid #dce1e4;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        
        .payment-method-option.active {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }
        
        .payment-method-option:hover:not(.active) {
            background-color: var(--neutral-light);
        }
        
        .hidden {
            display: none;
        }
        
        .section-divider {
            height: 1px;
            background-color: #f0f0f0;
            margin: 25px 0;
        }
        
        .item-details {
            background-color: var(--neutral-light);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .item-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .item-price {
            color: var(--primary);
            font-weight: 600;
            font-size: 18px;
        }
        
        .secured-by {
            text-align: center;
            font-size: 12px;
            color: var(--neutral-mid);
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .lock-icon {
            width: 12px;
            height: 12px;
            fill: var(--neutral-mid);
        }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }
        
        @media (max-width: 600px) {
            .container {
                border-radius: 0;
                box-shadow: none;
                height: 100%;
            }
            
            body {
                padding: 0;
            }
            
            .nav {
                overflow-x: auto;
                justify-content: flex-start;
            }
            
            .nav a {
                flex: 0 0 auto;
                white-space: nowrap;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h2>Checkout</h2>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="services.html">Services</a>
            <a href="listings.html">Listings</a>
            <a href="orders.php">Orders</a>
            <a href="#" class="active">Checkout</a>
        </div>

        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($itemType && ($service || $listing)): ?>
                <div class="item-details">
                    <div class="item-title">
                        <?php if ($itemType == 'service'): ?>
                            <?php echo htmlspecialchars($service['title']); ?> (Service)
                        <?php else: ?>
                            <?php echo htmlspecialchars($listing['title']); ?> (Product)
                        <?php endif; ?>
                    </div>
                    <div class="item-price">GHS <?php echo number_format($amount, 2); ?></div>
                </div>
            <?php endif; ?>
            
            <h3 class="form-title">Payment Information</h3>
            
            <form id="payment-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="action" value="initiate_payment">
                
                <?php if ($itemType == 'service'): ?>
                    <input type="hidden" name="service_id" value="<?php echo $itemId; ?>">
                <?php elseif ($itemType == 'listing'): ?>
                    <input type="hidden" name="listing_id" value="<?php echo $itemId; ?>">
                    <input type="hidden" name="quantity" value="<?php echo isset($_GET['quantity']) ? intval($_GET['quantity']) : 1; ?>">
                <?php endif; ?>
                
                <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required placeholder="Enter your full name" value="<?php echo $user ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email address" value="<?php echo $user ? htmlspecialchars($user['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number (e.g. 0240000000)" value="<?php echo $user ? htmlspecialchars($user['phone_number']) : ''; ?>">
                </div>
                
                <div class="section-divider"></div>
                
                <div class="payment-options">
                    <div class="payment-options-title">Select Payment Method</div>
                    <div class="payment-method-selector">
                        <div class="payment-method-option active" data-method="flutterwave">
                            Credit/Debit Card
                        </div>
                        <div class="payment-method-option" data-method="mobile_money">
                            Mobile Money
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" id="payment_method" value="flutterwave">
                </div>
                
                <div id="mobile-money-fields" class="hidden">
                    <div class="form-group">
                        <label for="network">Mobile Money Network</label>
                        <select id="network" name="network">
                            <option value="MTN">MTN Mobile Money</option>
                            <option value="VODAFONE">Vodafone Cash</option>
                            <option value="AIRTEL">AirtelTigo Money</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="receiverPhone">Mobile Money Number</label>
                        <input type="tel" id="receiverPhone" name="receiverPhone" placeholder="Enter your mobile money number">
                    </div>
                </div>
                
                <button type="submit" class="btn" id="pay-button">Pay GHS <?php echo number_format($amount, 2); ?></button>
            </form>
            
            <div class="secured-by">
                <svg class="lock-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19 10h-2V7c0-3.3-2.7-6-6-6S5 3.7 5 7v3H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm-8 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H7.9V7c0-1.7 1.4-3.1 3.1-3.1 1.7 0 3.1 1.4 3.1 3.1v3z"/>
                </svg>
                Secured by Flutterwave
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method selection
            const paymentOptions = document.querySelectorAll('.payment-method-option');
            const paymentMethodInput = document.getElementById('payment_method');
            const mobileMoneyFields = document.getElementById('mobile-money-fields');
            
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    paymentOptions.forEach(opt => opt.classList.remove('active'));
                    
                    // Add active class to clicked option
                    this.classList.add('active');
                    
                    // Set payment method value
                    const method = this.getAttribute('data-method');
                    paymentMethodInput.value = method;
                    
                    // Show/hide mobile money fields
                    if (method === 'mobile_money') {
                        mobileMoneyFields.classList.remove('hidden');
                    } else {
                        mobileMoneyFields.classList.add('hidden');
                    }
                });
            });
            
            // Form validation
            const paymentForm = document.getElementById('payment-form');
            
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const fullName = document.getElementById('fullName').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                
                if (!fullName || !email || !phone) {
                    alert('Please fill all required fields');
                    return;
                }
                
                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Please enter a valid email address');
                    return;
                }
                
                // Phone validation for Ghana numbers
                const phoneRegex = /^(\+233|0)[0-9]{9}$/;
                if (!phoneRegex.test(phone)) {
                    alert('Please enter a valid Ghana phone number');
                    return;
                }
                
                // If payment method is mobile money, validate those fields too
                if (paymentMethodInput.value === 'mobile_money') {
                    const network = document.getElementById('network').value;
                    const receiverPhone = document.getElementById('receiverPhone').value.trim();
                    
                    if (!network || !receiverPhone) {
                        alert('Please fill all mobile money fields');
                        return;
                    }
                }
                
                // Submit form via AJAX
                const formData = new FormData(paymentForm);
                
                fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // If Flutterwave
                        if (paymentMethodInput.value === 'flutterwave') {
                            // Initialize Flutterwave payment
                            const flutterwaveData = data.data.flutterwave_data;
                            const publicKey = data.data.public_key;
                            
                            FlutterwaveCheckout({
                                public_key: publicKey,
                                tx_ref: flutterwaveData.tx_ref,
                                amount: flutterwaveData.amount,
                                currency: flutterwaveData.currency,
                                payment_options: flutterwaveData.payment_options,
                                redirect_url: flutterwaveData.redirect_url,
                                customer: flutterwaveData.customer,
                                meta: flutterwaveData.meta,
                                customizations: flutterwaveData.customizations,
                                callback: function(response) {
                                    // Handle callback if needed
                                    console.log("Payment successful:", response);
                                },
                                onclose: function() {
                                    // Handle close event if needed
                                    console.log("Payment closed");
                                }
                            });
                        } 
                        // If mobile money
                        else if (paymentMethodInput.value === 'mobile_money') {
                            // Redirect to payment processing page
                            window.location.href = 'mobile_money_instruction.php?tx_ref=' + data.data.flutterwave_data.tx_ref;
                        }
                    } else {
                        // Show error message
                        alert(data.message || 'An error occurred. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
    </script>
</body>
</html>               