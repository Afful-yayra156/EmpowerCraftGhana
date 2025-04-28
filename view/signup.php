<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Signup | EmpowerCraft Ghana</title>
<link rel = "stylesheet" href = "../assets/css/signup.css">


<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">Empower<span>Craft</span> Ghana</div>
            <nav>
                <ul>
                    <li><a href="../index.">Home</a></li>
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
                <form id="signupForm" action="../actions/register_process.php" method="post" onsubmit="return validateForm()">
                    <div class="form-row">
                        <input type="text" id="username" name = "firstname"placeholder="First Name" required>
                    </div>
                    <div class="form-row">
                        <input type="text" id="lastname"  name = "lastname" placeholder="Last Name" required>
                    </div>
                    <div class="form-row">
                        <input type="email" id="email" name ="email" placeholder="Email" required>
                    </div>
                    <div class="form-row">
                        <input type="tel" id="phone" name ="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="form-row">
                        <select id="userType" name="userType">
                            <option value="artisan">Artisan</option>
                            <option value="client">Client</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <input type="password" id="password" name ="password" placeholder="Password" required>
                    </div>
                    <div class="form-row">
                        <input type="password" id="confirmPassword" name ="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    
                    <!-- Display error messages for validation -->
                    <div id="passwordError"></div>
                    <div id="successMessage"></div>
                    
                    <button type="submit">Sign Up</button>
                </form>
                <a href=" login.php">Already have an account? Login</a>
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

    // Display errors or allow submission
    if (errorMessages.length > 0) {
        passwordError.innerHTML = errorMessages.join("<br>");
        return false; // Prevent form submission if errors exist
    }
    return true; // Allow form submission to register_process.php
}
    </script>
</body>
</html>
