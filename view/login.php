<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login | EmpowerCraft Ghana</title>
    <link rel="stylesheet" href="../assets/css/login.css">
 
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
                <a href="../index.html">Home</a>
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
                <form id="loginForm" action="../actions/login_process.php" method="post" onsubmit="return formvalidate()">

                    <div class="errorMessage">
                        <span id="passwordError"></span><br>
                        <div id="loginError"></div>
                        <div id="loginSuccess"></div>
                        <?php
                        session_start();
                        if (isset($_SESSION['login_error'])) {
                            echo '<span id="passwordError" style="color: red;">' . $_SESSION['login_error'] . '</span>';
                            unset($_SESSION['login_error']); // Clear the error after showing it
                        }
                        ?>
                    </div>


                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="******" required>
                    </div>

                    <div class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit" class="login-button">Log In</button>

                    <div class="signup-link">
                        Don't have an account? <a href="signup.php">Sign Up</a>
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
        function formvalidate() {
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;
            var errorMessages = [];
            var passwordError = document.getElementById("passwordError");
            passwordError.textContent = ""; // Clear previous error messages

            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            // Validate email format and presence
            if (email === "") {
                errorMessages.push("Email is required!");
            } else if (!emailPattern.test(email)) {
                errorMessages.push("Invalid email format");
            }

            // Password validations
            if (password.length < 8) {
                errorMessages.push("Password must be at least 8 characters!");
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

            // Display errors and prevent form submission if invalid
            if (errorMessages.length > 0) {
                passwordError.innerHTML = errorMessages.join("<br>");
                return false;
            } else {
                return true; // Proceed with form submission if valid
            }
        }
    </script>

</body>
</html>
