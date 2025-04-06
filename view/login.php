<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login | EmpowerCraft Ghana</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
           /* background-image: url('art.jpg');*/
            background-image: white;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        header {
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .header-content {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-logo {
            display: flex;
            align-items: center;
        }

        .header-logo-icon {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .header-logo-icon span {
            font-size: 24px;
            font-weight: bold;
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
        }

        .header-nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .header-nav a:hover {
            opacity: 0.85;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 0;
            margin-top: 30px;
        }

        .login-container {
            display: flex;
            overflow: hidden;
            width: 1000px;
            height: 600px;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .brand-section {
            width: 50%;
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .brand-section h1 {
            font-size: 32px;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .brand-section p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 35px;
            opacity: 0.9;
        }

        .brand-logo {
            width: 100px;
            height: 100px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }

        .brand-logo span {
            font-size: 48px;
            font-weight: bold;
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-section {
            width: 50%;
            background-color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-section h2 {
            color: #185a9d;
            font-size: 28px;
            margin-bottom: 35px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: #43cea2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 206, 162, 0.2);
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #185a9d;
            font-size: 16px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: #43cea2;
            text-decoration: underline;
        }

        .login-button {
            background: linear-gradient(to right, #43cea2, #185a9d);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(67, 206, 162, 0.3);
        }

        .signup-link {
            text-align: center;
            margin-top: 35px;
            font-size: 16px;
            color: #666;
        }

        .signup-link a {
            color: #185a9d;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .signup-link a:hover {
            color: #43cea2;
            text-decoration: underline;
        }

        #statusMessage {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
            display: none;
            font-size: 16px;
        }

        .success {
            background-color: rgba(67, 206, 162, 0.2);
            color: #2c7e5c;
            border: 1px solid rgba(67, 206, 162, 0.5);
        }

        .error {
            background-color: rgba(255, 99, 71, 0.2);
            color: #d63030;
            border: 1px solid rgba(255, 99, 71, 0.5);
        }

        footer {
            background: linear-gradient(135deg, #4CAF50, #2196F3);
            color: white;
            padding: 15px 0;
            width: 100%;
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
            margin-bottom: 10px;
        }

        .footer-column h3 {
            font-size: 16px;
            margin-bottom: 10px;
            position: relative;
            padding-bottom: 8px;
        }

        .footer-column h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background-color: white;
        }

        .footer-column p, .footer-column ul {
            font-size: 13px;
            line-height: 1.5;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 6px;
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

        .copyright {
            text-align: center;
            padding-top: 10px;
            margin-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 12px;
            width: 90%;
            max-width: 1200px;
            margin: 10px auto 0;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 90%;
                height: auto;
            }

            .brand-section, .form-section {
                width: 100%;
                padding: 30px;
            }

            .brand-section {
                padding-bottom: 40px;
                padding-top: 40px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="header-logo">
                <div class="header-logo-icon">
                    <span>E</span>
                </div>
                <div class="header-title">EmpowerCraft Ghana</div>
            </div>
            <nav class="header-nav">
                <a href="index.html">Home</a>
                <a href="signup.php">Sign Up</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="login-container">
            <div class="brand-section">
                <div class="brand-logo">
                    <span>E</span>
                </div>
                <h1>EmpowerCraft Ghana</h1>
                <p>Building better futures through skills development and education. Join our community today.</p>
            </div>

            <div class="form-section">
                <h2>Welcome Back</h2>
                <div id="statusMessage"></div>
                <form id="loginForm">
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" placeholder="your@email.com" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" placeholder="••••••••" required>
                    </div>

                    <div class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit" class="login-button">Log In</button>

                    <div class="signup-link">
                        Don't have an account? <a href="signup.html">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>About Us</h3>
                <p>EmpowerCraft Ghana connects skilled professionals with clients looking for their services.</p>
            </div>

            <div class="footer-column">
                <h3>Contact Us</h3>
                <p>1 University Avenue, Accra, Ghana</p>
                <p>Email: empowerCraft@gmail.com</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 EmpowerCraft Ghana. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function initializeSampleUser() {
            if (!localStorage.getItem("userData")) {
                const sampleUser = {
                    name: "Test User",
                    email: "you@example.com",
                    password: "password123"
                };
                localStorage.setItem("userData", JSON.stringify(sampleUser));
                console.log("Sample user created for testing");
            }
        }

        initializeSampleUser();

        function showStatus(message, type) {
            const statusElement = document.getElementById("statusMessage");
            statusElement.textContent = message;
            statusElement.className = type;
            statusElement.style.display = "block";

            if (type === "success") {
                setTimeout(() => {
                    statusElement.style.display = "none";
                }, 3000);
            }
        }

        document.getElementById("loginForm").addEventListener("submit", function(event) {
            event.preventDefault();

            let email = document.getElementById("email").value;
            let password = document.getElementById("password").value;

            let storedUser = localStorage.getItem("userData");
            if (!storedUser) {
                showStatus("No user found. Please sign up.", "error");
                return;
            }

            let userData = JSON.parse(storedUser);

            if (userData.email === email && userData.password === password) {
                showStatus("Login successful! Redirecting to dashboard...", "success");
                localStorage.setItem("loggedInUser", JSON.stringify(userData));
                setTimeout(() => {
                    window.location.href = "dashboard.html";
                }, 1500);
            } else {
                showStatus("Incorrect email or password!", "error");
            }
        });
    </script>
</body>
</html>
