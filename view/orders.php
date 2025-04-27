<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | EmpowerSkills Ghana</title>
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
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
            --info: #2196f3;
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
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
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
        
        .content {
            padding: 30px;
        }
        
        .order-list {
            margin-top: 20px;
        }
        
        .order-item {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px var(--shadow);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .order-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background-color: var(--neutral-light);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .order-id {
            font-size: 14px;
            color: var(--neutral-mid);
        }
        
        .order-date {
            font-size: 14px;
            font-weight: 500;
        }
        
        .order-content {
            padding: 20px;
        }
        
        .order-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--neutral-dark);
        }
        
        .order-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 16px;
        }
        
        .order-detail {
            flex: 1;
            min-width: 200px;
        }
        
        .detail-label {
            font-size: 13px;
            color: var(--neutral-mid);
            margin-bottom: 4px;
        }
        
        .detail-value {
            font-size: 16px;
            font-weight: 500;
        }
        
        .order-footer {
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .status-delivered {
            background-color: var(--success);
            color: white;
        }
        
        .status-processing {
            background-color: var(--info);
            color: white;
        }
        
        .status-cancelled {
            background-color: var(--danger);
            color: white;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #d32f2f;
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }
        
        .btn-outline:hover {
            background-color: var(--primary-light);
        }
        
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .filter-controls {
            display: flex;
            gap: 10px;
        }
        
        .filter-select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #dce1e4;
            font-size: 14px;
            color: var(--neutral-dark);
        }
        
        .search-container {
            position: relative;
            width: 250px;
        }
        
        .search-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border-radius: 6px;
            border: 1px solid #dce1e4;
            font-size: 14px;
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            fill: var(--neutral-mid);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--neutral-mid);
        }
        
        .empty-icon {
            width: 60px;
            height: 60px;
            fill: #ccc;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .empty-text {
            font-size: 15px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .order-details {
                flex-direction: column;
                gap: 10px;
            }
            
            .order-detail {
                min-width: 100%;
            }
            
            .filter-container {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .filter-controls {
                flex-wrap: wrap;
            }
            
            .search-container {
                width: 100%;
            }
            
            .nav {
                overflow-x: auto;
                justify-content: flex-start;
            }
            
            .nav a {
                flex: 0 0 auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h2>Order History</h2>
        </div>
        
        <div class="nav">
            <a href="dashboard.html">Dashboard</a>
            <a href="services.html">Services</a>
            <a href="listings.html">Listings</a>
            <a href="orders.php" class="active">Orders</a>
            <a href="messages.php">Messages</a>
        </div>

        <div class="content">
            <div class="filter-container">
                <div class="filter-controls">
                    <select id="statusFilter" class="filter-select">
                        <option value="all">All Orders</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Processing">Processing</option>
                        <option value="Cancelled">Cancelled</option>
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

    <script>
        // Check if user is logged in
        // let user = JSON.parse(localStorage.getItem("loggedInUser"));
        
        // if (!user) {
        //     alert("Please log in first!");
        //     window.location.href = "login.php";
        // }
        
        // Sample orders data (Replace with API call in production)
        let orders = [
            { 
                id: "ORD-2025-001", 
                item: "Handmade Beaded Necklace", 
                price: "50.00", 
                status: "Delivered", 
                date: "2025-02-10",
                quantity: 1,
                paymentMethod: "Mobile Money"
            },
            { 
                id: "ORD-2025-002", 
                item: "Wooden Carving - Ashanti Stool", 
                price: "200.00", 
                status: "Processing", 
                date: "2025-02-12",
                quantity: 1,
                paymentMethod: "Card Payment"
            },
            { 
                id: "ORD-2025-003", 
                item: "Traditional Kente Cloth Bag", 
                price: "85.00", 
                status: "Processing", 
                date: "2025-03-25",
                quantity: 2,
                paymentMethod: "Mobile Money"
            }
        ];
        
        // Get elements
        const orderList = document.getElementById("orderList");
        const statusFilter = document.getElementById("statusFilter");
        const dateFilter = document.getElementById("dateFilter");
        const searchInput = document.getElementById("searchInput");
        
        // Add event listeners
        statusFilter.addEventListener("change", filterOrders);
        dateFilter.addEventListener("change", filterOrders);
        searchInput.addEventListener("input", filterOrders);
        
        // Function to get status class
        function getStatusClass(status) {
            switch(status) {
                case "Delivered":
                    return "status-delivered";
                case "Processing":
                    return "status-processing";
                case "Cancelled":
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
                
                orderItem.innerHTML = `
                    <div class="order-header">
                        <span class="order-id">${order.id}</span>
                        <span class="order-date">${formatDate(order.date)}</span>
                    </div>
                    <div class="order-content">
                        <h3 class="order-title">${order.item}</h3>
                        <div class="order-details">
                            <div class="order-detail">
                                <div class="detail-label">Price</div>
                                <div class="detail-value">GHS ${order.price}</div>
                            </div>
                            <div class="order-detail">
                                <div class="detail-label">Quantity</div>
                                <div class="detail-value">${order.quantity}</div>
                            </div>
                            <div class="order-detail">
                                <div class="detail-label">Payment Method</div>
                                <div class="detail-value">${order.paymentMethod}</div>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <span class="status-badge ${getStatusClass(order.status)}">${order.status}</span>
                        <div>
                            ${order.status === "Processing" ? 
                                `<button class="btn btn-danger" onclick="cancelOrder('${order.id}')">Cancel Order</button>` : 
                                `<button class="btn btn-outline" onclick="viewOrderDetails('${order.id}')">View Details</button>`
                            }
                        </div>
                    </div>
                `;
                
                orderList.appendChild(orderItem);
            });
        }
        
        // Function to filter orders
        function filterOrders() {
            const statusValue = statusFilter.value;
            const dateValue = dateFilter.value;
            const searchValue = searchInput.value.toLowerCase();
            
            let filteredOrders = [...orders];
            
            // Filter by status
            if (statusValue !== "all") {
                filteredOrders = filteredOrders.filter(order => order.status === statusValue);
            }
            
            // Filter by search term
            if (searchValue) {
                filteredOrders = filteredOrders.filter(order => 
                    order.item.toLowerCase().includes(searchValue) || 
                    order.id.toLowerCase().includes(searchValue)
                );
            }
            
            // Sort by date
            filteredOrders.sort((a, b) => {
                const dateA = new Date(a.date);
                const dateB = new Date(b.date);
                return dateValue === "newest" ? dateB - dateA : dateA - dateB;
            });
            
            renderOrders(filteredOrders);
        }
        
        // Function to cancel order
        function cancelOrder(orderId) {
            if (confirm("Are you sure you want to cancel this order?")) {
                const orderIndex = orders.findIndex(o => o.id === orderId);
                if (orderIndex >= 0) {
                    orders[orderIndex].status = "Cancelled";
                    filterOrders(); // Re-render with updated data
                }
            }
        }
        
        // Function to view order details
        function viewOrderDetails(orderId) {
            // In a real application, this would navigate to a detailed view of the order
            alert(`Viewing details for order ${orderId}`);
            // window.location.href = `order-details.html?id=${orderId}`;
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