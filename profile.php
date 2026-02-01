<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$current_user = getCurrentUser($pdo);
$user_points = getUserPoints($pdo, $_SESSION['customer_id']);

// Handle profile update
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate required fields
    if (empty($name) || empty($phone) || empty($email) || empty($address)) {
        $error = 'All fields are required';
    } else {
        try {
            // Check if email is already taken by another user
            $stmt = $pdo->prepare("SELECT * FROM customer WHERE email = ? AND customer_id != ?");
            $stmt->execute([$email, $_SESSION['customer_id']]);
            if ($stmt->rowCount() > 0) {
                $error = 'Email already taken by another account';
            } else {
                // Update basic info
                $stmt = $pdo->prepare("UPDATE customer SET cust_name = ?, num_phone = ?, email = ?, address = ? WHERE customer_id = ?");
                $stmt->execute([$name, $phone, $email, $address, $_SESSION['customer_id']]);
                
                // Update password if provided
                if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
                    if ($new_password !== $confirm_password) {
                        $error = 'New passwords do not match';
                    } elseif (strlen($new_password) < 6) {
                        $error = 'Password must be at least 6 characters';
                    } else {
                        // Verify current password
                        $stmt = $pdo->prepare("SELECT password FROM customer WHERE customer_id = ?");
                        $stmt->execute([$_SESSION['customer_id']]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (password_verify($current_password, $user['password'])) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE customer SET password = ? WHERE customer_id = ?");
                            $stmt->execute([$hashed_password, $_SESSION['customer_id']]);
                            $message .= ' Password updated successfully.';
                        } else {
                            $error = 'Current password is incorrect';
                        }
                    }
                }
                
                if (empty($error)) {
                    $message = 'Profile updated successfully' . ($message ?: '');
                    // Refresh user data
                    $current_user = getCurrentUser($pdo);
                }
            }
        } catch(Exception $e) {
            $error = 'Update failed: ' . $e->getMessage();
        }
    }
}

$page_title = "Profile | KEKABOO BOUTIQUE";
include 'header.php';
?>

<div class="profile-container">
    <!-- Messages -->
    <?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="profile-header">
        <h1 class="profile-title">My Profile</h1>
        <p class="profile-subtitle">Platinum Member</p>
    </div>

    <!-- Points Display -->
    <div class="points-display">
        <div class="points-value"><?php echo $user_points; ?></div>
        <div class="points-label">Available Points</div>
        <p style="margin-top: 16px; font-size: 14px; color: var(--gray-600);">
            Earn 1 point for every RM 1 spent. Redeem 100 points for 5% discount.
        </p>
    </div>

    <!-- Profile Form -->
    <form method="POST" class="profile-card">
        <h2 class="section-title">Personal Information</h2>
        
        <div class="info-grid">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($current_user['cust_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($current_user['num_phone']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Member Since</label>
                <input type="text" class="form-input" value="<?php echo date('F j, Y', strtotime($current_user['created_at'])); ?>" readonly style="background: var(--gray-50);">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-input" rows="3" required><?php echo htmlspecialchars($current_user['address']); ?></textarea>
        </div>

        <h2 class="section-title" style="margin-top: 32px;">Change Password</h2>
        <p style="font-size: 13px; color: var(--gray-500); margin-bottom: 20px;">Leave blank to keep current password</p>
        
        <div class="info-grid">
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-input">
            </div>
        </div>

        <button type="submit" class="btn" style="margin-top: 32px;">Update Profile</button>
    </form>

    <!-- Order History -->
    <div class="profile-card">
        <h2 class="section-title">Order History</h2>
        
        <?php
        $stmt = $pdo->prepare("SELECT * FROM purchase WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$_SESSION['customer_id']]);
        $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($purchases)): ?>
            <p style="text-align: center; color: var(--gray-500); padding: 20px 0;">No orders yet</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--gray-200);">
                            <th style="padding: 12px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Date</th>
                            <th style="padding: 12px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Items</th>
                            <th style="padding: 12px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Total</th>
                            <th style="padding: 12px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $purchase): ?>
                        <tr style="border-bottom: 1px solid var(--gray-100);">
                            <td style="padding: 12px; font-size: 13px;">
                                <?php echo date('M d, Y', strtotime($purchase['order_date'])); ?>
                            </td>
                            <td style="padding: 12px; font-size: 13px;">
                                <?php echo htmlspecialchars($purchase['item_name']); ?>
                            </td>
                            <td style="padding: 12px; font-size: 13px; font-weight: 600;">
                                RM <?php echo number_format($purchase['item_price'], 2); ?>
                            </td>
                            <td style="padding: 12px;">
                                <span style="
                                    font-size: 10px;
                                    font-weight: 600;
                                    letter-spacing: 0.05em;
                                    text-transform: uppercase;
                                    padding: 4px 8px;
                                    border-radius: 4px;
                                    background: <?php echo $purchase['status'] == 'completed' ? 'var(--green)' : 'var(--gray-300)'; ?>;
                                    color: <?php echo $purchase['status'] == 'completed' ? 'var(--white)' : 'var(--gray-700)'; ?>;">
                                    <?php echo ucfirst($purchase['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="bag.php" class="btn btn-outline">View All Orders</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Account Actions -->
    <div style="display: flex; gap: 16px; justify-content: center; margin-top: 40px;">
        <a href="bag.php" class="btn">My Bag</a>
        <a href="product.php" class="btn btn-outline">Continue Shopping</a>
    </div>
</div>

<?php include 'footer.php'; ?>