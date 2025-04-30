<?php
session_start(); // Start the session
include '../db/config.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Fetch orders from database
$buyer_id = $_SESSION['user_id'];
$sql = "SELECT o.order_id, o.order_date, o.total_amount, o.status, o.shipping_address,
       o.shipping_method, o.tracking_number, o.notes,
       s.title as service_name, s.price as service_price,
       u.first_name, u.last_name, u.bio, u.profile_picture
FROM orders o
LEFT JOIN services s ON o.service_id = s.service_id
LEFT JOIN users u ON s.user_id = u.user_id
WHERE o.buyer_id = ?
ORDER BY o.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();

// Create an array to store the orders
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Close statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | EmpowerSkills Ghana</title>
    <link rel="stylesheet" href="../assets/css/orders.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Order History</h2>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="services.php">Services</a>
            <a href="checkout.php">checkout</a>
            <a href="orders.php" class="active">Orders</a>
            <a href="messages.php">Messages</a>
        </div>

        <div class="content">
            <div class="filter-container">
                <div class="filter-controls">
                    <select id="statusFilter" class="filter-select">
                        <option value="all">All Orders</option>
                        <option value="delivered">Delivered</option>
                        <option value="processing">Processing</option>
                        <option value="cart">In Cart</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select id="dateFilter" class="filter-select">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
                <div class="search-container">
                    <svg class="search-icon" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                    </svg>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search orders...">
                </div>
            </div>

            <div id="orderList" class="order-list"></div>
        </div>
    </div>

    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Order Details</h3>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // Get orders data from PHP
        let orders = <?php echo json_encode($orders); ?>;
        
        // Get elements
        const orderList = document.getElementById("orderList");
        const statusFilter = document.getElementById("statusFilter");
        const dateFilter = document.getElementById("dateFilter");
        const searchInput = document.getElementById("searchInput");
        const modalContent = document.getElementById("modalContent");
        const closeBtn = document.querySelector(".close-btn");

        
        // Add event listeners
        statusFilter.addEventListener("change", filterOrders);
        dateFilter.addEventListener("change", filterOrders);
        searchInput.addEventListener("input", filterOrders);
        
        // Function to get status class
        function getStatusClass(status) {
            status = status.toLowerCase();
            switch(status) {
                case "delivered":
                    return "status-delivered";
                case "processing":
                    return "status-processing";
                case "cart":
                    return "status-cart";
                case "cancelled":
                    return "status-cancelled";
                default:
                    return "";
            }
        }
        
        // Function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }
        
        // Function to render orders
        function renderOrders(ordersToRender) {
            orderList.innerHTML = "";
            
            if (ordersToRender.length === 0) {
                orderList.innerHTML = `
                    <div class="empty-state">
                        <svg class="empty-icon" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                            <path d="M14.5 12.75l1.25-2.25-2.25-1.25L12 7l-1.5 2.25-2.25 1.25 1.25 2.25L9 15l3 1.5L15 15z"/>
                        </svg>
                        <h3 class="empty-title">No orders found</h3>
                        <p class="empty-text">Try adjusting your filters or search term</p>
                        <button class="btn btn-outline" onclick="resetFilters()">Reset Filters</button>
                    </div>
                `;
                return;
            }
            
            ordersToRender.forEach(order => {
                const orderItem = document.createElement("div");
                orderItem.classList.add("order-item");
                
                const paymentMethod = order.notes || "Not specified";
                const serviceName = order.service_name || "Unknown Service";
                
                orderItem.innerHTML = `
                    <div class="order-header">
                        <span class="order-id">Order #${order.order_id}</span>
                        <span class="order-date">${formatDate(order.order_date)}</span>
                    </div>
                    <div class="order-content">
                        <h3 class="order-title">${serviceName}</h3>
                        <div class="order-details">
                            <div class="order-detail">
                                <div class="detail-label">Price</div>
                                <div class="detail-value">GHS ${parseFloat(order.total_amount).toFixed(2)}</div>
                            </div>
                            <div class="order-detail">
                                <div class="detail-label">Quantity</div>
                                <div class="detail-value">1</div>
                            </div>
                            <div class="order-detail">
                                <div class="detail-label">Payment Method</div>
                                <div class="detail-value">${paymentMethod}</div>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <span class="status-badge ${getStatusClass(order.status)}">${order.status}</span>
                        <div>
                            ${order.status.toLowerCase() === "cart" ? 
                                `<button class="btn btn-primary" onclick="checkoutOrder(${order.order_id})">Checkout</button>` : ''}
                            ${order.status.toLowerCase() === "processing" ? 
                                `<button class="btn btn-danger" onclick="cancelOrder(${order.order_id})">Cancel Order</button>` : 
                                `<button class="btn btn-outline" onclick="viewOrderDetails(${order.order_id})">View Details</button>`
                            }
                        </div>
                        <button class="btn btn-danger" onclick="cancelOrder(${order.order_id})">Cancel Order</button>

                    </div>
                `;

                
                
                orderList.appendChild(orderItem);
            });
        }
        

        // Function to filter orders
        function filterOrders() {
            const statusValue = statusFilter.value.toLowerCase();
            const dateValue = dateFilter.value;
            const searchValue = searchInput.value.toLowerCase();
            
            let filteredOrders = [...orders];
            
            // Filter by status
            if (statusValue !== "all") {
                filteredOrders = filteredOrders.filter(order => 
                    order.status.toLowerCase() === statusValue
                );
            }
            
            // Filter by search term
            if (searchValue) {
                filteredOrders = filteredOrders.filter(order => 
                    (order.service_name && order.service_name.toLowerCase().includes(searchValue)) || 
                    order.order_id.toString().includes(searchValue)
                );
            }
            
            // Sort by date
            filteredOrders.sort((a, b) => {
                const dateA = new Date(a.order_date);
                const dateB = new Date(b.order_date);
                return dateValue === "newest" ? dateB - dateA : dateA - dateB;
            });
            
            renderOrders(filteredOrders);
        }
        
        // Function to cancel order
        function cancelOrder(orderId) {
            if (confirm("Are you sure you want to cancel this order?")) {
                // Send AJAX request to cancel the order
                fetch('../actions/update_order_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `order_id=${orderId}&status=cancelled`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update local data
                        const orderIndex = orders.findIndex(o => o.order_id == orderId);
                        if (orderIndex >= 0) {
                            orders[orderIndex].status = "cancelled";
                            filterOrders(); // Re-render with updated data
                        }
                        alert('Order has been cancelled');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the order.');
                });
            }
        }
        
        // Function to checkout order
        function checkoutOrder(orderId) {
            window.location.href = `checkout.php?order_id=${orderId}`;
        }
        
        // Function to view order details
        function viewOrderDetails(orderId) {
            window.location.href = `order-details.php?id=${orderId}`;
        }

         // Function to view order details
         function viewOrderDetails(orderId) {
            const order = orders.find(o => o.order_id === orderId);
            if (!order) return;

            const sellerName = `${order.first_name} ${order.last_name}`;
            const bio = order.bio || "No bio available.";
            const profileImage = order.profile_image;

            modalContent.innerHTML = `
                <h4>Service: ${order.service_name}</h4>
                <p><strong>Order ID:</strong> ${order.order_id}</p>
                <p><strong>Status:</strong> ${order.status}</p>
                <p><strong>Total:</strong> GHS ${parseFloat(order.total_amount).toFixed(2)}</p>
                <hr>
                <div class="seller-info">
                    <img src="${profileImage}" alt="Seller profile" class="seller-profile">
                    <div>
                        <p><strong>Offered By:</strong> ${sellerName}</p>
                        <p><strong>Bio:</strong> ${bio}</p>
                    </div>
                </div>
            `;

            document.getElementById("orderModal").style.display = "block";
        }
        
        // Close modal
        closeBtn.onclick = function() {
            document.getElementById('orderModal').style.display = "none";
        }
        
        // Function to reset filters
        function resetFilters() {
            statusFilter.value = "all";
            dateFilter.value = "newest";
            searchInput.value = "";
            filterOrders();
        }
        
        // Initial render
        filterOrders();



    </script>
</body>
</html>