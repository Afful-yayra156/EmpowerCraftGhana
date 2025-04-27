<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link  rel="stylesheet" href="../assets/css/contact.css">
    <title>EmpowerCraft Ghana - Send a Message</title>

</head>
<body>
    <header class="header">
        <div class="logo">EmpowerCraft Ghana</div>
        <nav class="nav">
            <a href="index.html">Home</a>
            <a href="dashboard.php">Dashboard</a>
        </nav>
    </header>
    
    <div class="container">
        <div class="image-pane">
            <img src="../assets/images/woman.jpg" alt="EmpowerCraft Ghana Artisan">
        </div>
        
        <div class="message-form">
            <h1>Send a Message</h1>
            <!-- Form to submit message -->
            <form id="contactForm" action="../actions/contact_backend.php" method="POST" onsubmit="showSuccessMessage(event)">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" class="form-control" name="first_name" placeholder="*First Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="last_name" placeholder="*Last Name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="*Email Address" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" class="form-control" name="phone_number" placeholder="*Phone Number" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="select-wrapper">
                        <select class="form-control" name="category" required>
                            <option value="" disabled selected>*Select Artisan Category</option>
                            <option value="crafts">Crafts & Handmade Products</option>
                            <option value="tutoring">Tutoring & Education</option>
                            <option value="performance">Performance & Entertainment</option>
                            <option value="digital">Digital Services</option>
                            <option value="culinary">Culinary Arts</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="select-wrapper">
                        <select class="form-control" name="artisan" required>
                            <option value="" disabled selected>*Specific Artisan (if known)</option>
                            <option value="any">Any available artisan</option>
                            <option value="kwame">Kwame Addo - Woodcarving</option>
                            <option value="ama">Ama Serwaa - Bead Jewelry</option>
                            <option value="kofi">Kofi Mensah - Kente Weaving</option>
                            <option value="abena">Abena Osei - Makeup Artistry</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <textarea class="form-control" name="message" placeholder="Write your message here..." required></textarea>
                </div>
                                       <!-- Success message hidden by default -->
                <div id="successMessage" class="success-message">
                    Your contact message was sent successfully!
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Send Message</button>
                </div>
     
                <p class="privacy-note">
                    By clicking submit, I agree with EmpowerCraft Ghana's <a href="#">Privacy Policy</a> and consent to receive updates about my inquiry.
                </p>
            </form>


        </div>
    </div>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="services.php">Services</a>
                <a href="index.html">About Us</a>
            </div>
        </div>
        <div style="margin-top: 1rem;">
            &copy; 2025 EmpowerCraft Ghana. All rights reserved.
        </div>
    </footer>

    <script>
        function showSuccessMessage(event) {
            event.preventDefault(); // Prevent form submission to show the success message

            // Show the success message
            document.getElementById('successMessage').style.display = 'block';
            
            // Optionally, submit the form after showing the message
            setTimeout(function() {
                document.getElementById('contactForm').submit(); // Submit the form after a brief delay
            }, 2000); // Delay for 2 seconds (you can adjust this)
        }
    </script>
</body>
</html>
