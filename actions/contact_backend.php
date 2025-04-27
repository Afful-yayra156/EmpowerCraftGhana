<?php
// Include the database configuration
include '../db/config.php';  // Make sure this file is correctly included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $category = htmlspecialchars($_POST['category']);
    $artisan = htmlspecialchars($_POST['artisan']);
    $message = htmlspecialchars($_POST['message']);
    
    // Use the connection variable from the config
    try {
        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO contact_messages (first_name, last_name, email, phone_number, category, artisan, message) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters to the prepared statement
            $stmt->bind_param('sssssss', $first_name, $last_name, $email, $phone_number, $category, $artisan, $message);
            
            // Execute the statement
            $stmt->execute();


            // Redirect to a success page after submission
            header("Location: ../view/messages.php");
            exit();
        } else {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
    } catch (Exception $e) {
        // Handle database error (display a message or log it)
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect if form is not submitted via POST
    header("Location: ../view/messages.php");
    exit();
}
?>
