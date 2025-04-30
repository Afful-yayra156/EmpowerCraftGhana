<?php
ob_start();
session_start();
include '../db/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['userType']);
    $phone_number = isset($_POST['phone']) ? trim($_POST['phone']) : null;

    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        die('Please fill in all required fields.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email format.');
    }

    if ($confirm_password !== $password) {
        die('Passwords do not match.');
    }

    $stmt = $conn->prepare('SELECT email FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $results = $stmt->get_result();

    if ($results->num_rows > 0) {
        echo '<script>alert("User already registered.");</script>';
        echo '<script>window.location.href = "../view/signup.php";</script>';
        exit();
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);


        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, user_type, phone_number, registration_date, bio, location) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), 'This is my bio.', 'Not specified')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssss', $fname, $lname, $email, $hashed_password, $role, $phone_number);

        if ($stmt->execute()) {
            $_SESSION['fname'] = $fname;
            header('Location: ../view/login.php');
            exit();
        } else {
            header('Location: ../view/signup.php'); 
            exit();
        }
    }

    $stmt->close();
}

$conn->close();
?>
