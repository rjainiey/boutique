<?php
$page_title = 'Order Management';
require_once 'includes/header.php';

// Handle status update
if (isset($_POST['update_status'])) {
    $purchase_id = (int)$_POST['purchase_id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE purchase SET status = ? WHERE purchase_id = ?");
    $stmt->execute([$status, $purchase_id]);
    
    echo "<script>alert('Order status updated!'); window.location='orders.php';</script>";
}

$orders = $pdo->query("
    SELECT p.*, c.cust_name, c.email, c.num_phone 
    FROM purchase p 
    JOIN customer c ON p.customer_id = c.customer_id 
    ORDER BY p.created_at DESC
")->fetchAll();
?>

<div class="card">
    <div class="table-header">
    <h2>Orders</h2>
    <a href="generate_orders.php" target="_blank" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Generate PDF Report
    </a>
</div>
    </div>
    
    <table id="ordersTable" class="data-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['purchase_id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($order['cust_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['email']); ?><br>
                    <?php echo htmlspecialchars($order['num_phone']); ?>
                </td>
                <td><?php echo htmlspecialchars($order['item_name']); ?></td>
                <td>RM <?php echo number_format($order['item_price'], 2); ?></td>
                <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                <td>
                    <form method="POST" class="status-form">
                        <input type="hidden" name="purchase_id" value="<?php echo $order['purchase_id']; ?>">
                        <select name="status" class="status-select" 
                                onchange="this.form.submit()">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
                <td>
                    <button class="btn btn-sm btn-info view-order" 
                            data-order='<?php echo json_encode($order); ?>'>
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Order Details</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body" id="orderDetails">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>