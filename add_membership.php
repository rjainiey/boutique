<?php
$page_title = 'Add New Membership';
require_once 'includes/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['cust_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['num_phone']);
    $address = trim($_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $check = $pdo->prepare("SELECT email FROM customer WHERE email = ?");
    $check->execute([$email]);
    
    if ($check->rowCount() > 0) {
        $message = "<div class='alert error' style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>Email already registered!</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO customer (cust_name, email, num_phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([$name, $email, $phone, $address, $password])) {
            echo "<script>alert('Membership created successfully!'); window.location='customers.php';</script>";
        }
    }
}
?>

<div class="card" style="max-width: 800px; margin: 20px auto;">
    <div class="table-header">
        <h2><i class="fas fa-user-plus"></i> New Membership Form</h2>
    </div>
    
    <?php echo $message; ?>

    <form method="POST" class="standard-form">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="cust_name" class="form-input" required placeholder="Enter full name">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-input" required placeholder="customer@email.com">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="num_phone" class="form-input" required placeholder="e.g. 60123456789">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Set a temporary password">
            </div>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label>Full Address</label>
            <textarea name="address" class="form-input" rows="3" required placeholder="Enter complete mailing address"></textarea>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Register Member</button>
            <a href="dashboard.php" class="btn btn-secondary" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>