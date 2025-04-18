<?php
// Include the database configuration file to establish a connection
include '../db/config.php';
global $conn;

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to store user data after login
session_start();

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure email and password are provided
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Prepare SQL statement to check if the email exists
        $stmt = $conn->prepare('SELECT user_id, first_name, email, password_hash, user_type FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $results = $stmt->get_result();

        // Verify if a user with the provided email exists
        if ($results->num_rows > 0) {
            $user = $results->fetch_assoc();

            // Validate password against the stored hashed password
            if (password_verify($password, $user['password_hash'])) {
                // Store user details in session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['fname'] = $user['first_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['user_type'];

                // Redirect to the dashboard
                header('Location: ../view/dashboard.php');
                exit(); // Ensure script execution stops after redirection
            } else {
                // Incorrect password error message
                echo '<script>alert("Incorrect password. Please try again.");</script>';
                echo '<script>window.location.href = "../view/login.php";</script>';
                exit();
            }
        } else {
            // Email not found in the database
            echo '<script>alert("Email not found. Please register first.");</script>';
            echo '<script>window.location.href = "../view/signup.php";</script>';
            exit();
        }

        $stmt->close(); // Close prepared statement
    } else {
        // Handle missing form data
        echo '<script>alert("Form data missing. Please try again.");</script>';
        echo '<script>window.location.href = "../view/login.php";</script>';
        exit();
    }
}

// Close database connection
$conn->close();
?>
