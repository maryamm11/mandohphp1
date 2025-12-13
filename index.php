<?php
session_start();
include "includes/config.php";

// Check if user is admin, if not redirect to login
if (!isLoggedIn() || !isAdmin()) {
    header("Location: Login-Signup-Logout/login.php");
    exit;
}

// Fetch available cars
$query = "SELECT * FROM cars WHERE status = 'available' ORDER BY id DESC";
$cars_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental Service</title>
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
    <link rel="stylesheet" href="css/index.css">
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
                    <li class="nav-item"><a href="index.php" class="nav-link active" aria-current="page">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/DashboardAdmin.php">Admin Dashboard</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item"><a class="nav-link" href="Login-Signup-Logout/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="Login-Signup-Logout/login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="Login-Signup-Logout/signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content Section -->
    <main>
        <div class="car-grid">
            <div class="row">
                <?php if ($cars_result && mysqli_num_rows($cars_result) > 0): ?>
                    <?php while ($car = mysqli_fetch_assoc($cars_result)): ?>
                        <div class="card">
                            <div class="card-body">
                                <h3><?= htmlspecialchars($car['name']) ?></h3>
                                <p><?= htmlspecialchars($car['model']) ?></p>
                                <p><strong>$<?= number_format($car['price_per_day'], 2) ?></strong> / day</p>
                                <p>Type: <?= htmlspecialchars($car['type']) ?></p>
                                <p>Status: <?= htmlspecialchars($car['status']) ?></p>
                                <a href="rent_car.php?id=<?= $car['id'] ?>" class="btn btn-primary">Rent This Car</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No cars available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
    <footer>
        <div class="footer-container">
            <!-- Contact Information -->
            <div class="footer-section">
                <h3>Contact Us</h3>
                <a href="mailto:info@carrentalservice.com">Email: info@carrentalservice.com</a>
                <a href="01234567890">Phone: 01234567890</a>
            </div>

            <!-- Social Media Links -->
            <div class="footer-section">
                <h3>Follow Us</h3>
                <ul class="social-links">
                    <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                    <li><a href="https://github.com/Youssef-M-Salama/CarRentalSystemProject"><i class="fa-brands fa-github"></i></a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                </ul>
            </div>

            <!-- Newsletter Subscription -->
            <div class="footer-section">
                <h3>Subscribe</h3>
                <form>
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <!-- Copyright Notice -->
        <div class="copyright">
            <p>&copy; 2025 Car Rental Service. All rights reserved.</p>
        </div>
    </footer>

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
