<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/signup.css">
    <title>Signup | EmpowerSkills Ghana</title>
</head>
<body>

    <div class="container">
        <h2>Signup</h2>
        <form id="signupForm" onsubmit="return validateForm()">
            <input type="text" id="username" placeholder="Username" required>
            <input type="email" id="email" placeholder="Email" required>
            <input type="tel" id="phone" placeholder="Phone Number" required>
            <select id="userType">
                <option value="artisan">Artisan</option>
                <option value="client">Client</option>
            </select>
            <input type="password" id="password" placeholder="Password" required>
            <input type="password" id="confirmPassword" placeholder="Confirm Password" required>
            
            <!-- Display error messages for validation -->
            <div id="passwordError" style="color: red;"></div>
            <div id="successMessage" style="color: green;"></div>
            
            <button type="submit">Sign Up</button>
        </form>
        <a href="../view/login.php">Already have an account? Login</a>
    </div>

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
                successMessage.textContent = "Registered successfully!";
                setTimeout(function () {
                    successMessage.textContent = ""; // Clear message after 20 seconds
                }, 20000);
        
                return true; // Allow form submission if no errors
            }
        }
    </script>

</body>
</html>

