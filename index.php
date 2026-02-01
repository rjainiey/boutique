<?php
session_start();
require_once 'config.php';
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check against admin table
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    // Compare plain text passwords (since that's how it's stored in your DB)
    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['admin_id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hijab Store</title>
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="image/icon.png"/>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-user-shield"></i> Hijab Store Admin</h1>
            <p>Administrator Login</p>
        </div>
        
        <form method="POST" action="" class="login-form">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Enter username">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div class="login-footer">
                <a href="/boutique/index.php" class="btn btn-secondary btn-block" 
                   style="margin-top: 15px;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <p style="margin-top: 20px; font-size: 13px; color: #1c1c1c;">
                    &copy; <?php echo date('Y'); ?> Hijab Store. All rights reserved.
                </p>
            </div>
            
    </div>
</body>
</html>