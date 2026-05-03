<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrix - Space Events Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top">
        <div class="container">
            <a class="navbar-brand text-uppercase fw-bold text-accent" href="<?php echo BASE_URL; ?>index.php">Antrix</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>events.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>news.php">News</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>gallery.php">Gallery</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard.php">Admin Panel</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>submit-event.php">Submit Event</a></li>
                        <li class="nav-item"><span class="nav-link text-white">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="<?php echo BASE_URL; ?>logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>login.php">Login</a></li>
                        <li class="nav-item"><a class="btn btn-outline-info ms-2" href="<?php echo BASE_URL; ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="main-content-wrapper">
        <!-- Flash messages -->
        <div class="container mt-5 pt-3">
            <?php if(isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
            <?php endif; ?>
        </div>
