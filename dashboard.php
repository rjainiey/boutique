<?php
$page_title = 'Dashboard';
require_once 'includes/header.php';

// Fetch stats
$stmt = $pdo->query("SELECT COUNT(*) as total_items FROM inventory");
$total_items = $stmt->fetch()['total_items'];

$stmt = $pdo->query("SELECT COUNT(*) as total_customers FROM customer");
$total_customers = $stmt->fetch()['total_customers'];

$stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM purchase");
$total_orders = $stmt->fetch()['total_orders'];

$stmt = $pdo->query("SELECT COUNT(*) as low_stock FROM inventory WHERE stock_quantity < 10");
$low_stock = $stmt->fetch()['low_stock'];

$stmt = $pdo->query("SELECT COUNT(*) as total_upcoming FROM upcoming");
$total_upcoming = $stmt->fetch()['total_upcoming'];


$total_upcoming = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_upcoming FROM upcoming");
    $result = $stmt->fetch();
    $total_upcoming = $result['total_upcoming'] ?? 0;
} catch (PDOException $e) {
    // Table might not exist yet
    $total_upcoming = 0;
    error_log("Upcoming table error: " . $e->getMessage());
}

// Recent orders
$recent_orders = $pdo->query("
    SELECT p.*, c.cust_name 
    FROM purchase p 
    JOIN customer c ON p.customer_id = c.customer_id 
    ORDER BY p.created_at DESC 
    LIMIT 5
")->fetchAll();
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #111827;">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_items; ?></h3>
            <p>Total Products</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #374151;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_customers; ?></h3>
            <p>Total Customers</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #6b7280;">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_orders; ?></h3>
            <p>Total Orders</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #ef4444;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $low_stock; ?></h3>
            <p>Low Stock Items</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background-color: #8b5cf6;">
            <i class="fas fa-bell"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $total_upcoming; ?></h3>
            <p>Upcoming Products</p>
        </div>
    </div>
</div>

<div class="dashboard-content">
    <div class="recent-orders card">
        <h2>Recent Orders</h2>
        <br>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['purchase_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['cust_name']); ?></td>
                    <td><?php echo htmlspecialchars(substr($order['item_name'], 0, 50)) . '...'; ?></td>
                    <td>RM <?php echo number_format($order['item_price'], 2); ?></td>
                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($order['status']); ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="quick-actions card">
        <h2>Quick Actions</h2>
        <br>
        <div class="action-buttons">
            <a href="inventory.php?action=add" class="btn btn-primary" style="background-color: #7e7e7e">
                <i class="fas fa-plus"></i> Add New Product
            </a>
            <a href="add_membership.php" class="btn btn-secondary" style="background-color: #929292">
                <i class="fas fa-id-card"></i> Add Membership
            </a>
            <a href="inventory.php?filter=low" class="btn btn-warning" style="background-color: #bebebe">
                <i class="fas fa-exclamation-triangle"></i> View Low Stock
            </a>
            <a href="orders.php" class="btn btn-danger" style="background-color: #c0c0c0">
                <i class="fas fa-eye"></i> View All Orders
            </a>
            <a href="customers.php" class="btn btn-info" style="background-color: #b0b0b0">
                <i class="fas fa-user-plus"></i> View Customers
            </a>
            <a href="upcoming.php" class="btn btn-success" style="background-color: #e0e0e0">
                <i class="fas fa-bell"></i> View Upcoming
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>