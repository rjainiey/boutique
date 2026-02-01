<?php
require_once 'config.php';
$current_user = getCurrentUser($pdo);
$user_points = isLoggedIn() ? getUserPoints($pdo, $_SESSION['customer_id']) : 0;
$page_title = $page_title ?? 'KEKABOO BOUTIQUE';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="image/icon.png"/>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="index.php" class="logo">KEKABOO</a>
                <div class="nav-links">
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
                    <a href="product.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'product.php' ? 'active' : ''; ?>">Products</a>
                    <a href="sales.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : ''; ?>">Sales</a>
                    <a href="upcoming.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'upcoming.php' ? 'active' : ''; ?>">Upcoming</a>
                    <a href="bag.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bag.php' ? 'active' : ''; ?>">Bag</a>
                    
                    <?php if (isLoggedIn() && $current_user): ?>
                        <div class="user-info">
                            <div class="user-points">
                                <div class="points"><?php echo $user_points; ?> PTS</div>
                            </div>
                            <a href="profile.php" class="user-avatar">
                                <?php 
                                $initials = '';
                                $name_parts = explode(' ', $current_user['cust_name']);
                                if (count($name_parts) >= 2) {
                                    $initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($current_user['cust_name'], 0, 2));
                                }
                                echo $initials;
                                ?>
                            </a>
                        </div>
                        <a href="logout.php" class="nav-link">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a>
                        <a href="register.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Banner -->
    <div class="banner">
        <div class="marquee">
            <span class="banner-text">KEKABOO MONOCHROME EDITION 2026 • FREE SHIPPING OVER RM 100 • EARN POINTS ON EVERY PURCHASE •</span>
            <span class="banner-text">KEKABOO MONOCHROME EDITION 2026 • FREE SHIPPING OVER RM 100 • EARN POINTS ON EVERY PURCHASE •</span>
        </div>
    </div>