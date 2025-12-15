<?php
session_start();
include "includes/config.php";

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: Login-Signup-Logout/login.php");
    exit;
}

// Get car ID from URL
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch car details
$car_query = "SELECT * FROM cars WHERE id = $car_id";
$car_result = mysqli_query($conn, $car_query);

if (!$car_result || mysqli_num_rows($car_result) == 0) {
    header("Location: index.php");
    exit;
}

$car = mysqli_fetch_assoc($car_result);

// Handle rental request submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $with_driver = 'no'; // Always no driver
    
    // Calculate total days
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days = $start->diff($end)->days + 1;
    
    // Calculate total price
    $total_price = $days * $car['price_per_day'];
    
    // Insert rental request
    $user_id = $_SESSION['user']['id'];
    $insert_query = "INSERT INTO rental_requests (user_id, car_id, start_date, end_date, with_driver, total_price, status) 
                    VALUES ($user_id, $car_id, '$start_date', '$end_date', '$with_driver', $total_price, 'pending')";
    
    if (mysqli_query($conn, $insert_query)) {
    // Update car status to rented
    $update_car = "UPDATE cars SET status = 'rented' WHERE id = $car_id";
    mysqli_query($conn, $update_car);
    
    $success = '<p class="fw-bold text-center m-0">Rental request submitted successfully!</p>';
} else {
    $error = "Error submitting rental request. Please try again.";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Car - <?= htmlspecialchars($car['name']) ?></title>
    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Include CSS stylesheets -->
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/rent_car.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <!-- Website Header Section -->
    <header class="navbar navbar-expand-lg bg-body-tertiary d-flex justify-content-center align-items-center">
        <nav class="container-fluid d-flex justify-content-center align-items-center">
            <h1 class="navbar-brand">Car Rental Service</h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/DashboardAdmin.php">Admin Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="Login-Signup-Logout/logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
<main>
    <div class="container my-4 d-flex flex-column align-items-center">

        <!-- success message -->
        <?php if (!empty($success)): ?>
            <div class="message success-message text-center">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <!-- error message -->
        <?php if (!empty($error)): ?>
            <div class="message error-message text-center">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="rental-request">
            <?php if (!empty($car['image'])): ?>
                <img src="images/<?= htmlspecialchars($car['image']) ?>" 
                    alt="<?= htmlspecialchars($car['name']) ?>" 
                    class="car-image mb-3">
            <?php endif; ?>
            
            <h2>Rent Car: <?= htmlspecialchars($car['name']) ?></h2>
            
            <form method="POST" class="text-center" action="rent_car.php?id=<?= $car_id ?>">
                <div class="mb-3">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="mb-3">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="price-calculation mb-3">
                    <p>Total Price: $<span id="totalPrice"><?= number_format($car['price_per_day'], 2) ?></span></p>
                </div>

                <span id="pricePerDay" data-price="<?= $car['price_per_day'] ?>" style="display:none;"></span>
                
                <button type="submit" class="submit-button">Submit Rental Request</button>
            </form>
        </div>
    </div>
</main>


    <!-- Footer Section -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>Contact Us</h3>
                <a href="mailto:info@carrentalservice.com">Email: info@carrentalservice.com</a>
                <a href="01234567890">Phone: 01234567890</a>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <ul class="social-links">
                    <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                    <li><a href="https://github.com/Youssef-M-Salama/CarRentalSystemProject"><i class="fa-brands fa-github"></i></a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Subscribe</h3>
                <form>
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 Car Rental Service. All rights reserved.</p>
        </div>
    </footer>

    <script>
document.addEventListener('DOMContentLoaded', function () {

    const form           = document.getElementById('rentForm');
    const startDateInput = document.getElementById('start_date');
    const endDateInput   = document.getElementById('end_date');
    const totalPriceEl   = document.getElementById('totalPrice');
    const pricePerDayEl  = document.getElementById('pricePerDay');
    const submitBtn      = document.querySelector('.submit-button');

    const pricePerDay = parseFloat(pricePerDayEl.dataset.price);

    function calculatePrice() {
        if (!startDateInput.value || !endDateInput.value) {
            totalPriceEl.innerText = '0.00';
            return;
        }

        const start = new Date(startDateInput.value);
        const end   = new Date(endDateInput.value);

        if (end < start) {
            totalPriceEl.innerText = '0.00';
            return;
        }

        const diffTime = end - start;
        const days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        const total = days * pricePerDay;

        totalPriceEl.innerText = total.toFixed(2);
    }

    startDateInput.addEventListener('input', calculatePrice);
    endDateInput.addEventListener('input', calculatePrice);

    // ✅ الصح: submit على الفورم
    form.addEventListener('submit', function (e) {

        if (!startDateInput.value || !endDateInput.value) {
            alert('Please select rental dates');
            e.preventDefault();
            return;
        }

        const start = new Date(startDateInput.value);
        const end   = new Date(endDateInput.value);

        if (end < start) {
            alert('End date must be after start date');
            e.preventDefault();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerText = 'Processing...';
    });

});
</script>




    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>