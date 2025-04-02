<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <title>Login | EmpowerSkills Ghana</title>
</head>


<body>

    <!-- Login Form -->
    <div class="container">
        <h2>Login</h2>
        <form id="loginForm" onsubmit="return formvalidate()">
            <input type="email" id="email" placeholder="Email" required>
            <input type="password" id="password" placeholder="Password" required>
            <span id="passwordError" style="color:rgb(255, 3, 3);"></span><br><br>
            <div id="loginError" style="color: red;"></div>
            <div id="loginSuccess" style="color: green;"></div>
            <button type="submit">Login</button>
        </form>
        <a href="signup.php">Don't have an account? Sign Up</a>
    </div>

    <!-- Form Validation Script -->


    <script>
        function formvalidate() {
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;
            var errorMessages = [];
            var passwordError = document.getElementById("passwordError");
            passwordError.textContent = "";

            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (email === "") {
                errorMessages.push("Email is required!");
            } else if (!emailPattern.test(email)) {
                errorMessages.push("Invalid email format");
            }

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

            if (errorMessages.length > 0) {
                passwordError.innerHTML = errorMessages.join("<br>");
                return false;
            } else {
                return true;
            }
        }
    </script>

</body>
</html>
