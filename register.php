<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$page_title = "Register | KEKABOO BOUTIQUE";
include 'header.php';

// Handle registration
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $error = 'Please enter a valid phone number (10-11 digits)';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM customer WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Email already registered';
        } else {
            try {
                // Hash password using the hashPassword function
                $hashed_password = hashPassword($password);
                
                // Insert new customer with hashed password
                $stmt = $pdo->prepare("INSERT INTO customer (cust_name, num_phone, email, address, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $phone, $email, $address, $hashed_password]);
                
                $customer_id = $pdo->lastInsertId();
                
                // Give 100 welcome points
                $stmt = $pdo->prepare("INSERT INTO points (customer_id, total_price) VALUES (?, 100)");
                $stmt->execute([$customer_id]);
                
                // Auto-login after registration
                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['customer_name'] = $name;
                
                header('Location: index.php?registered=1');
                exit;
                
            } catch(Exception $e) {
                $error = 'Registration failed: ' . $e->getMessage();
            }
        }
    }
}

// Check if redirected from successful registration
if (isset($_GET['registered'])) {
    $success = 'Registration successful! Welcome to KEKABOO BOUTIQUE.';
}
?>

<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">Register</h1>
        <p style="text-align: center; font-size: 14px; color: var(--gray-500); margin-bottom: 32px;">
            Join KEKABOO and enjoy exclusive benefits
        </p>
        
        <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" id="register-form">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-input" required
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-input" required 
                       pattern="[0-9]{10,11}" title="10-11 digit phone number"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" name="address" class="form-input" rows="3" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required minlength="6">
            </div>
            
            <!-- Membership Benefits -->
            <div style="background: var(--gray-50); padding: 20px; border-radius: 6px; margin-bottom: 24px;">
                <h3 style="font-size: 12px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 12px; color: var(--gray-700);">
                    Membership Benefits
                </h3>
                <ul style="font-size: 13px; color: var(--gray-600); list-style: none; padding-left: 0;">
                    <li style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--green);">✓</span> 100 welcome points
                    </li>
                    <li style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--green);">✓</span> Earn 1 point per RM 1 spent
                    </li>
                    <li style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--green);">✓</span> Redeem points for discounts
                    </li>
                    <li style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--green);">✓</span> Early access to new collections
                    </li>
                    <li style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--green);">✓</span> Free shipping over RM 100
                    </li>
                </ul>
            </div>
            
            <button type="submit" class="btn-submit">Create Account</button>
        </form>
        
        <div class="auth-link">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>