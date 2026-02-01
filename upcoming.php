<?php
$page_title = 'Upcoming Products';
require_once 'includes/header.php';

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

// Add/Edit Upcoming Product
if (in_array($action, ['add', 'edit']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $image_url = $_POST['image_url'];
    
    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO upcoming (name, price, image_url) VALUES (?, ?, ?)");
        $stmt->execute([$name, $price, $image_url]);
        $message = "Upcoming product added successfully!";
    } else {
        $stmt = $pdo->prepare("UPDATE upcoming SET name=?, price=?, image_url=? WHERE id=?");
        $stmt->execute([$name, $price, $image_url, $id]);
        $message = "Upcoming product updated successfully!";
    }
    
    echo "<script>alert('$message'); window.location='upcoming.php';</script>";
}

// Delete Upcoming Product
if ($action == 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM upcoming WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Upcoming product deleted successfully!'); window.location='upcoming.php';</script>";
}

// Fetch product for editing
$product = null;
if ($action == 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM upcoming WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}
?>

<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="card">
    <h2><?php echo $action == 'add' ? 'Add New Upcoming Product' : 'Edit Upcoming Product'; ?></h2>
    
    <form method="POST" action="" class="form-container">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo $product['name'] ?? ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="price">Price (RM) *</label>
            <input type="number" step="0.01" id="price" name="price" required 
                   value="<?php echo $product['price'] ?? 0.00; ?>">
        </div>
        
        <div class="form-group">
            <label for="image_url">Image URL</label>
            <input type="text" id="image_url" name="image_url" 
                   value="<?php echo $product['image_url'] ?? ''; ?>" 
                   placeholder="e.g., image/hijab1.jpg">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $action == 'add' ? 'Add Product' : 'Update Product'; ?>
            </button>
            <a href="upcoming.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php else: ?>
<div class="card">
    <div class="table-header">
        <h2>Upcoming Products Management</h2>
        <a href="?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Upcoming Product
        </a>
    </div>
    
    <table id="upcomingTable" class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Image</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $products = $pdo->query("SELECT * FROM upcoming ORDER BY id DESC")->fetchAll();
            foreach ($products as $item):
            ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>RM <?php echo number_format($item['price'], 2); ?></td>
                <td>
                    <?php if ($item['image_filename']): ?>
                        <img src="<?php echo htmlspecialchars($item['image_filename']); ?>" 
                             alt="Product" class="thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <br>
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    if (!empty($item['created_at'])) {
                        echo date('M d, Y', strtotime($item['created_at']));
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
                <td>
                    <a href="?action=edit&id=<?php echo $item['id']; ?>" 
                       class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="?action=delete&id=<?php echo $item['id']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Delete this upcoming product?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add DataTables initialization script -->
<script>
$(document).ready(function() {
    $('#upcomingTable').DataTable({
        "pageLength": 10,
        "order": [[0, 'desc']],
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "zeroRecords": "No matching records found"
        }
    });
});
</script>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>