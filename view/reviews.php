<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews & Ratings | EmpowerSkills Ghana</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4CAF50, #087F23);
            color: white;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            color: black;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            margin-top: 50px;
        }
        h2 {
            color: #087F23;
        }
        .review-box {
            margin-top: 20px;
            padding: 15px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        .review-box strong {
            color: #087F23;
        }
        .rating {
            color: gold;
        }
        .review-form {
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
            text-align: left;
        }
        textarea, select, button, input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #087F23;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #1B5E20;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reviews & Ratings</h2>

    <!-- Reviews Section -->
    <div id="reviewsSection">
        <!-- Reviews will be dynamically loaded here -->
    </div>

    <!-- Add a Review Form -->
    <div class="review-form">
        <h3>Leave a Review</h3>
        <select id="reviewType">
            <option value="service">Service</option>
            <option value="checkout">checkout</option>
            <option value="user">User</option>
        </select>
        <input type="text" id="reviewerName" placeholder="Your Name">
        <select id="rating">
            <option value="5">★★★★★ - Excellent</option>
            <option value="4">★★★★☆ - Very Good</option>
            <option value="3">★★★☆☆ - Average</option>
            <option value="2">★★☆☆☆ - Poor</option>
            <option value="1">★☆☆☆☆ - Terrible</option>
        </select>
        <textarea id="reviewText" placeholder="Write your review..."></textarea>
        <button onclick="submitReview()">Submit Review</button>
    </div>
</div>

<script>
// Load existing reviews from the database when the page loads
window.onload = function() {
    loadReviews();
};

function loadReviews() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "load_reviews.php", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            let reviews = JSON.parse(xhr.responseText);
            let reviewsSection = document.getElementById("reviewsSection");
            reviews.forEach(review => {
                let newReview = document.createElement("div");
                newReview.classList.add("review-box");
                newReview.innerHTML = `<strong>${review.reviewer_name}</strong> 
                                       <span class="rating">${"★".repeat(review.rating)}${"☆".repeat(5 - review.rating)}</span>
                                       <p>${review.comment}</p>`;
                reviewsSection.appendChild(newReview);
            });
        }
    };
    xhr.send();
}

function submitReview() {
    let name = document.getElementById("reviewerName").value.trim();
    let rating = document.getElementById("rating").value;
    let reviewText = document.getElementById("reviewText").value.trim();
    let reviewType = document.getElementById("reviewType").value;

    if (name === "" || reviewText === "") {
        alert("Please fill out all fields before submitting.");
        return;
    }

    let formData = new FormData();
    formData.append('reviewer_name', name);
    formData.append('rating', rating);
    formData.append('reviewType', reviewType);
    formData.append('reviewText', reviewText);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "submit_review.php", true);
    xhr.onload = function () {
        if (xhr.status == 200) {
            if (xhr.responseText.includes("successfully")) {
                loadReviews();  // Reload reviews after successful submission
                document.getElementById("reviewerName").value = "";
                document.getElementById("rating").value = "5";
                document.getElementById("reviewText").value = "";
            } else {
                alert("Error: " + xhr.responseText);
            }
        } else {
            alert("Server error. Please try again later.");
        }
    };
    xhr.send(formData);
}
</script>

</body>
</html>