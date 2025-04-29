<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | EmpowerSkills Ghana</title>
    <script src="https://js.paystack.co/v2/inline.js"></script>
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
            <a href="services.php">Services</a>
            <a href="checkout.php">Checkout</a>
            <a href="orders.php">Orders</a>
        </div>

        <div class="form-container">
            <h3 class="form-title">Payment Information</h3>
            
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" placeholder="Enter your phone number">
            </div>

            <div class="form-group">
                <label for="amount">Amount (GHS)</label>
                <input type="number" id="amount" placeholder="Enter amount to pay">
            </div>

            <div class="section-divider"></div>
            
            <div class="payment-options">
                <div class="payment-options-title">Select Payment Method</div>
                <div class="payment-method-selector">
                    <div class="payment-method-option active" id="cardOption" onclick="selectPaymentMethod('card')">
                        Card/Bank
                    </div>
                    <div class="payment-method-option" id="mobileOption" onclick="selectPaymentMethod('mobile_money')">
                        Mobile Money
                    </div>
                </div>
            </div>

            <button class="btn" onclick="makePayment()">Proceed to Payment</button>
            
            <div class="secured-by">
                <svg class="lock-icon" viewBox="0 0 24 24">
                    <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z" />
                </svg>
                Secured by Paystack
            </div>
        </div>
    </div>

    <script>
        let selectedPaymentMethod = "card";
        
        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;
            let cardOption = document.getElementById("cardOption");
            let mobileOption = document.getElementById("mobileOption");
            
            if (method === "mobile_money") {
                cardOption.classList.remove("active");
                mobileOption.classList.add("active");
            } else {
                cardOption.classList.add("active");
                mobileOption.classList.remove("active");
            }
        }

        function makePayment() {
            let fullName = document.getElementById("fullName").value.trim();
            let email = document.getElementById("email").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let amount = document.getElementById("amount").value.trim();

            if (fullName === "" || email === "" || phone === "" || amount === "") {
                alert("Please fill all fields before proceeding.");
                return;
            }

            // Convert amount to kobo (Paystack expects amount in smallest currency unit, i.e., pesewas for GHS)
            let amountInPesewas = parseFloat(amount) * 100;

            let handler = PaystackPop.setup({
                key: "pk_live_2c0d8c6c49ed7ed7587fd4b755b4bbff5d5dbad6", // Replace with your Paystack public key
                email: email,
                amount: amountInPesewas,
                currency: "GHS",
                ref: "EmpowerSkills-" + Math.floor(Math.random() * 1000000),
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Full Name",
                            variable_name: "full_name",
                            value: fullName
                        },
                        {
                            display_name: "Phone Number",
                            variable_name: "phone_number",
                            value: phone
                        }
                    ]
                },
                callback: function(response) {
                    // Handle successful payment
                    window.location.href = "payment_success.html?reference=" + response.reference;
                },
                onClose: function() {
                    alert("Payment window closed.");
                }
            });

            handler.openIframe();
        }
    </script>
</body>
</html>