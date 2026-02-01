<?php
$page_title = 'Customer Management';
require_once 'includes/header.php';

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM customer WHERE customer_id = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Customer deleted successfully!'); window.location='customers.php';</script>";
}

$customers = $pdo->query("SELECT * FROM customer ORDER BY created_at DESC")->fetchAll();
?>

<div class="card">
    <div class="table-header">
        <h2>Orders</h2>
        <a href="generate_cust.php" target="_blank" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Generate PDF Report
        </a>
    </div>
    
    <table id="customersTable" class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?php echo $customer['customer_id']; ?></td>
                <td><?php echo htmlspecialchars($customer['cust_name']); ?></td>
                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                <td><?php echo htmlspecialchars($customer['num_phone']); ?></td>
                <td><?php echo htmlspecialchars($customer['address']); ?></td>
                <td><?php echo date('d M Y', strtotime($customer['created_at'])); ?></td>
                <td>
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $customer['num_phone']); ?>" 
                    target="_blank" 
                    class="btn btn-sm btn-success" 
                    title="WhatsApp Customer">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="?action=delete&id=<?php echo $customer['customer_id']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Delete this customer?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>