<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/signup.css">
    <title>Signup | EmpowerSkills Ghana</title>
</head>
<body>

    <!-- Signup Form Container -->
    <div class="container">
        <h2>Signup</h2>
        <!-- The signup form with validation on submit -->
        <form id="signupForm" action="../actions/register_process.php" method="post" onsubmit="return validateForm()">
            <input type="text" id="firstname" name="firstname" placeholder="First Name" required>
            <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
            <select id="userType" name="userType">
                <option value="artisan">Artisan</option>
                <option value="client">Client</option>
            </select>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm Password" required>
        
            <!-- Error message container for password-related issues -->
            <div id="passwordError" style="color: red;"></div>
            <!-- Success message container -->
            <div id="successMessage" style="color: green;"></div>
        
            <!-- Submit button for the form -->
            <button type="submit">Sign Up</button>
        </form>
        
        <!-- Link to login page if the user already has an account -->
        <a href="../view/login.php">Already have an account? Login</a>
    </div>

    <!-- Form Validation Script -->
    <script>
        function validateForm() {
            // Get form values
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const confirmPassword = document.getElementById("confirmPassword").value.trim();
            
            // Arrays for error messages
            const errorMessages = [];
            
            // Access error message containers
            const passwordError = document.getElementById("passwordError");
            const successMessage = document.getElementById("successMessage");
        
            // Clear previous messages
            passwordError.innerHTML = "";
            successMessage.textContent = "";
        
            // Regular expression for email validation
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
        
            // Check if passwords match
            if (password !== confirmPassword) {
                errorMessages.push("Passwords do not match.");
            }
        
            // If there are errors, display them and prevent form submission
            if (errorMessages.length > 0) {
                passwordError.innerHTML = errorMessages.join("<br>");
                return false; // Prevent form submission if errors exist
            } else {
                // Success message if the form is valid
                successMessage.textContent = "Registered successfully!";
                // Clear success message after 20 seconds
                setTimeout(function () {
                    successMessage.textContent = ""; 
                }, 20000);
        
                return true; // Allow form submission if no errors
            }
        }
    </script>

</body>
</html>
