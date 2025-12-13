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
        
        $success = "Rental request submitted successfully! Total price: $" . number_format($total_price, 2);
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
        <div class="rental-request">
            <h2>Rent Car: <?= htmlspecialchars($car['name']) ?></h2>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="rent_car.php?id=<?= $car_id ?>">
                <div class="mb-3">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="mb-3">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="price-calculation" id="price-calculation">
                    <p>Total Price: $<span id="total-price"><?= number_format($car['price_per_day'], 2) ?></span></p>
                </div>
                
                <button type="submit" class="submit-button">Submit Rental Request</button>
            </form>
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
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const totalPriceSpan = document.getElementById('total-price');
        const pricePerDay = <?= $car['price_per_day'] ?>;
        
        function calculatePrice() {
            if (startDate.value && endDate.value) {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                
                if (days > 0) {
                    let total = days * pricePerDay;
                    totalPriceSpan.textContent = total.toFixed(2);
                }
            }
        }
        
        startDate.addEventListener('change', calculatePrice);
        endDate.addEventListener('change', calculatePrice);
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>

