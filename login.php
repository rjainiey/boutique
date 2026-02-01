<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$page_title = "Login | KEKABOO BOUTIQUE";
include 'header.php';

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM customer WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Use the verifyPassword function that works with both plain text and hashed passwords
            if (verifyPassword($password, $user['password'])) {
                // If password is plain text, hash it for future use
                if (!password_verify($password, $user['password']) && $password === $user['password']) {
                    $hashed_password = hashPassword($password);
                    $stmt = $pdo->prepare("UPDATE customer SET password = ? WHERE customer_id = ?");
                    $stmt->execute([$hashed_password, $user['customer_id']]);
                }
                
                $_SESSION['customer_id'] = $user['customer_id'];
                $_SESSION['customer_name'] = $user['cust_name'];
                
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'No account found with this email';
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">Login</h1>
        
        <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" id="login-form">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
        
        <div class="auth-link">
            Don't have an account? <a href="register.php">Create one</a>
        </div>
        <div class="auth-admin-section" style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ccc; text-align: center;">
            <p style="font-size: 0.85rem; color: #666;">
                Staff member? <a href="admin/index.php" style="color: #333; font-weight: bold;">Admin Portal</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>