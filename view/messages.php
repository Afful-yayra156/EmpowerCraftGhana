<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmpowerCraft Ghana - Send a Message</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
        }
        
        .header {
            background: linear-gradient(90deg, #25c883 0%, #1e90ff 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .nav {
            display: flex;
            gap: 2rem;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            gap: 2rem;
        }
        
        .image-pane {
            flex: 1;
            background-color: #f2f2f2;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .image-pane img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .message-form {
            flex: 1;
            padding: 2rem;
        }
        
        .message-form h1 {
            color: #1e3a8a;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f5f5f5;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #25c883;
            box-shadow: 0 0 0 2px rgba(37, 200, 131, 0.2);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .select-wrapper {
            position: relative;
        }
        
        .select-wrapper:after {
            content: '▼';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #777;
            font-size: 0.8rem;
        }
        
        .btn {
            background: linear-gradient(90deg, #25c883 0%, #1e90ff 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .privacy-note {
            font-size: 0.9rem;
            color: #666;
            margin-top: 1rem;
        }
        
        .privacy-note a {
            color: #1e90ff;
            text-decoration: none;
        }
        
        .footer {
            background: linear-gradient(90deg, #25c883 0%, #1e90ff 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .footer-links {
            display: flex;
            gap: 2rem;
        }
        
        .footer-links a {
            color: white;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .form-row {
                flex-direction: column;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
                padding: 1rem;
            }
            
            .nav {
                gap: 1rem;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">EmpowerCraft Ghana</div>
        <nav class="nav">
            <a href="index.html">Home</a>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
        </nav>
    </header>
    
    <div class="container">
        <div class="image-pane">
            
            <img src="../assets/images/woman.jpg" alt="EmpowerCraft Ghana Artisan">
        </div>
        
        <div class="message-form">
            <h1>Send a Message</h1>
            
            <div class="form-row">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="*First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="*Last Name" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="*Email Address" required>
                </div>
                <div class="form-group">
                    <input type="tel" class="form-control" placeholder="*Phone Number" required>
                </div>
            </div>
            
            <div class="form-group">
                <div class="select-wrapper">
                    <select class="form-control">
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
                    <select class="form-control">
                        <option value="" disabled selected>*Specific Artisan (if known)</option>
                        <option value="any">Any available artisan</option>
                        <!-- These would be populated dynamically -->
                        <option value="kwame">Kwame Addo - Woodcarving</option>
                        <option value="ama">Ama Serwaa - Bead Jewelry</option>
                        <option value="kofi">Kofi Mensah - Kente Weaving</option>
                        <option value="abena">Abena Osei - Makeup Artistry</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <textarea class="form-control" placeholder="Write your message here..."></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Send Message</button>
            </div>
            
            <p class="privacy-note">
                By clicking submit, I agree with EmpowerCraft Ghana's <a href="#">Privacy Policy</a> and consent to receive updates about my inquiry.
            </p>
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
</body>
</html>