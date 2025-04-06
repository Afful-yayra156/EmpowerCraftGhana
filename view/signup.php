<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Signup | EmpowerCraft Ghana</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, #4CAF50, #2196F3);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logo span {
            color: #e3f2fd;
        }

        nav ul {
            display: flex;
            list-style-type: none;
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            color: #e3f2fd;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        .intro-text {
            padding: 15px 0;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.5;
        }

        /* Main Content Styles */
        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
        }

        .page-wrapper {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background: transparent;
            position: relative;
            z-index: 2;
        }

        /* Side Columns with Floating Tools */
        .side-column {
            width: 150px;
            background-color: #4CAF50;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        /* Floating tools animation */
        .floating-tools {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .tool {
            position: absolute;
            color: rgba(255, 255, 255, 0.7);
            animation: float 15s infinite linear;
            font-size: 24px;
            opacity: 0.8;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Center Form Section */
        .form-section {
            flex: 1;
            margin: 0 20px;
            background: white;
            /*background: url("artss.jpg") no-repeat center/cover;*/
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 30px;
            position: relative;
            z-index: 2;
         
        }

        h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 30px;
            font-size: 28px;
        }

        form {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: rgba(249, 249, 249, 0.8);
        }

        input:focus, select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
            background-color: #fff;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234CAF50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            padding-right: 40px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4CAF50, #2196F3);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        button:hover {
            background: linear-gradient(135deg, #45a049, #1e88e5);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2196F3;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
            color: #4CAF50;
        }

        .form-row {
            margin-bottom: 15px;
        }

        /* Positioning for tools around the form */
        .tool:nth-child(1) { top: 5%; left: 30%; animation-duration: 20s; font-size: 30px; }
        .tool:nth-child(2) { top: 12%; right: 35%; animation-duration: 25s; animation-delay: 1s; font-size: 28px; }
        .tool:nth-child(3) { top: 18%; left: 45%; animation-duration: 18s; animation-delay: 2s; font-size: 32px; }
        .tool:nth-child(4) { top: 25%; right: 25%; animation-duration: 22s; animation-delay: 3s; font-size: 26px; }
        .tool:nth-child(5) { top: 32%; left: 20%; animation-duration: 24s; animation-delay: 4s; font-size: 28px; }
        .tool:nth-child(6) { top: 40%; right: 40%; animation-duration: 19s; animation-delay: 5s; font-size: 30px; }
        .tool:nth-child(7) { top: 48%; left: 15%; animation-duration: 21s; animation-delay: 6s; font-size: 26px; }
        .tool:nth-child(8) { top: 55%; right: 10%; animation-duration: 23s; animation-delay: 7s; font-size: 32px; }
        .tool:nth-child(9) { top: 62%; left: 18%; animation-duration: 17s; animation-delay: 8s; font-size: 28px; }
        .tool:nth-child(10) { top: 70%; right: 30%; animation-duration: 24s; animation-delay: 9s; font-size: 30px; }
        .tool:nth-child(11) { top: 78%; left: 6%; animation-duration: 22s; animation-delay: 10s; font-size: 26px; }
        .tool:nth-child(12) { top: 85%; right: 20%; animation-duration: 19s; animation-delay: 11s; font-size: 28px; }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-20px) rotate(10deg);
            }
            50% {
                transform: translateY(0) rotate(0deg);
            }
            75% {
                transform: translateY(20px) rotate(-10deg);
            }
            100% {
                transform: translateY(0) rotate(0deg);
            }
        }

        #passwordError {
            color: #ff3333;
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        #successMessage {
            color: #4CAF50;
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
            font-weight: 600;
        }

        /* Footer Styles */
        footer {
            background: linear-gradient(135deg, #4CAF50, #2196F3);
            color: white;
            padding: 30px 0;
            margin-top: auto;
        }

        .footer-content {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .footer-column {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
        }

        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 3px;
            background-color: white;
        }

        .footer-column p, .footer-column ul {
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 8px;
        }

        .footer-column ul li a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 0;
            text-align: left;
        }

        .footer-column ul li a:hover {
            color: #e3f2fd;
            text-decoration: none;
        }

        .social-media {
            display: flex;
            margin-top: 15px;
        }

        .social-media a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin-right: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
        }

        .social-media a:hover {
            background-color: white;
            color: #4CAF50;
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto 0;
        }

        @media (max-width: 992px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                margin-top: 15px;
                justify-content: center;
            }

            nav ul li {
                margin: 0 10px;
            }

            .page-wrapper {
                flex-direction: column;
            }

            .side-column {
                width: 100%;
                height: 80px;
                margin-bottom: 20px;
                border-radius: 15px;
            }

            .form-section {
                margin: 0;
            }

            .footer-column {
                flex: 0 0 50%;
                padding: 0 15px;
            }
        }

        @media (max-width: 576px) {
            .intro-text {
                padding: 15px;
            }

            .footer-column {
                flex: 0 0 100%;
            }

            nav ul li {
                margin: 0 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">Empower<span>Craft</span> Ghana</div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    
                </ul>
            </nav>
        </div>
        <div class="intro-text">
            <p>Connect with skilled artisans, teachers, stylists, and more! Find new opportunities or offer your services. Join our diverse community today and be part of Ghana's growing skilled workforce.</p>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <div class="page-wrapper">
            <!-- Left Side Column -->
            <div class="side-column">
                <div class="floating-tools">
                    <div class="tool"><i class="fas fa-hammer"></i></div>
                    <div class="tool"><i class="fas fa-paint-roller"></i></div>
                    <div class="tool"><i class="fas fa-wrench"></i></div>
                    <div class="tool"><i class="fas fa-screwdriver"></i></div>
                    <div class="tool"><i class="fas fa-ruler-combined"></i></div>
                    <div class="tool"><i class="fas fa-tape"></i></div>
                    <div class="tool"><i class="fas fa-wrench"></i></div>
                    <div class="tool"><i class="fas fa-screwdriver"></i></div>
                    <div class="tool"><i class="fas fa-ruler-combined"></i></div>
                    <div class="tool"><i class="fas fa-tape"></i></div>
                    <div class="tool"><i class="fas fa-cut"></i></div>
                    <div class="tool"><i class="fas fa-spray-can"></i></div>
                    <div class="tool"><i class="fas fa-book"></i></div>
                    <div class="tool"><i class="fas fa-graduation-cap"></i></div>
                </div>
            </div>
            
            <!-- Form Section -->
            <div class="form-section">
                <h2>Create Your Account</h2>
                <form id="signupForm" onsubmit="return validateForm()">
                    <div class="form-row">
                        <input type="text" id="username" placeholder="First Name" required>
                    </div>
                    <div class="form-row">
                        <input type="text" id="lastname" placeholder="Last Name" required>
                    </div>
                    <div class="form-row">
                        <input type="email" id="email" placeholder="Email" required>
                    </div>
                    <div class="form-row">
                        <input type="tel" id="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="form-row">
                        <select id="userType">
                            <option value="artisan">Artisan</option>
                            <option value="client">Client</option>
                            <option value="stylist">Hairstylist/Beautician</option>
                            <option value="teacher">Teacher</option>
                            <option value="weaver">Weaver/Textile Artist</option>
                            <option value="digital">Digital Professional</option>
                            <option value="other">Other Professional</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <input type="password" id="password" placeholder="Password" required>
                    </div>
                    <div class="form-row">
                        <input type="password" id="confirmPassword" placeholder="Confirm Password" required>
                    </div>
                    
                    <!-- Display error messages for validation -->
                    <div id="passwordError"></div>
                    <div id="successMessage"></div>
                    
                    <button type="submit">Sign Up</button>
                </form>
                <a href="login.html">Already have an account? Login</a>
            </div>
            
            <!-- Right Side Column -->
            <div class="side-column">
                <div class="floating-tools">
                    <div class="tool"><i class="fas fa-cut"></i></div>
                    <div class="tool"><i class="fas fa-spray-can"></i></div>
                    <div class="tool"><i class="fas fa-wrench"></i></div>
                    <div class="tool"><i class="fas fa-screwdriver"></i></div>
                    <div class="tool"><i class="fas fa-ruler-combined"></i></div>
                    <div class="tool"><i class="fas fa-tape"></i></div>
                    <div class="tool"><i class="fas fa-book"></i></div>
                    <div class="tool"><i class="fas fa-graduation-cap"></i></div>
                    <div class="tool"><i class="fas fa-screwdriver"></i></div>
                    <div class="tool"><i class="fas fa-ruler-combined"></i></div>
                    <div class="tool"><i class="fas fa-tape"></i></div>
                    <div class="tool"><i class="fas fa-wrench"></i></div>
                    <div class="tool"><i class="fas fa-screwdriver"></i></div>
                    <div class="tool"><i class="fas fa-ruler-combined"></i></div>
                    <div class="tool"><i class="fas fa-laptop-code"></i></div>
                    <div class="tool"><i class="fas fa-camera"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>About Us</h3>
                <p>EmpowerCraft Ghana connects skilled professionals with clients looking for </p>
                <p>their services. Our platform helps artisans, teachers, stylists, and many </p>
                <p>other professionals showcase their skills and find new opportunities.</p>
            </div>
            
            
            <div class="footer-column">
                <h3>Contact Us</h3>
                <p>1 University Avenue, Accra, Ghana</p>
                <p>Email: empowerCraft@gmail.com</p>
                <p>Phone: +233 50 65 25 001</p>
                <!-- <div class="social-media">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div> -->
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 EmpowerCraft Ghana. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function validateForm() {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const confirmPassword = document.getElementById("confirmPassword").value.trim();
            const errorMessages = [];
            const passwordError = document.getElementById("passwordError");
            const successMessage = document.getElementById("successMessage");
        
            // Clear previous messages
            passwordError.innerHTML = "";
            successMessage.textContent = "";
        
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            // Validate email format
            if (email === "") {
                errorMessages.push("Email is required!");
            } else if (!emailPattern.test(email)) {
                errorMessages.push("Invalid email format. Please enter a valid email address.");
            }
            
            // Password validations
            if (password.length < 8) {
                errorMessages.push("Password must be at least 8 characters long!");
            }
            if (!/[A-Z]/.test(password)) {
                errorMessages.push("Password must contain at least one uppercase letter!");
            }
            if ((password.match(/\d/g) || []).length < 3) {
                errorMessages.push("Password must include at least three digits!");
            }
            if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                errorMessages.push("Password must contain at least one special character!");
            }
        
            // Ensure passwords match
            if (password !== confirmPassword) {
                errorMessages.push("Passwords do not match.");
            }
        
            // Display errors or success message
            if (errorMessages.length > 0) {
                passwordError.innerHTML = errorMessages.join("<br>");
                return false; // Prevent form submission if errors exist
            } else {
                // Show success message and redirect after a delay
                successMessage.textContent = "Registration successful! Redirecting to login page...";
                
                // Set a timeout to redirect after showing the message
                setTimeout(function() {
                    window.location.href = "login.php"; // Redirect to login page
                }, 3000); // Redirect after 3 seconds
                
                return false; // Prevent default form submission since we're handling the redirect
            }
        }
    </script>
</body>
</html>
