<?php
$page_title = 'Inventory Management';
require_once 'includes/header.php';

if (isset($_GET['filter']) && $_GET['filter'] == 'low') {
    // Show only low stock items (less than 10)
    $sql = "SELECT * FROM inventory WHERE stock_quantity < 10 ORDER BY stock_quantity ASC";
    $is_low_stock_view = true;
} else {
    // Show all items (normal behavior)
    $sql = "SELECT * FROM inventory ORDER BY item_id DESC";
    $is_low_stock_view = false;
}

$stmt = $pdo->query($sql);
$items = $stmt->fetchAll();

// Handle actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

// Add/Edit Product
if (in_array($action, ['add', 'edit']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $stock_quantity = (int)$_POST['stock_quantity'];
    $price = (float)$_POST['price'];
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    $image_url = $_POST['image_url'];
    
    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO inventory (item_name, stock_quantity, price, is_on_sale, image_url) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$item_name, $stock_quantity, $price, $is_on_sale, $image_url]);
        $message = "Product added successfully!";
    } else {
        $stmt = $pdo->prepare("UPDATE inventory SET item_name=?, stock_quantity=?, price=?, is_on_sale=?, image_url=? 
                              WHERE item_id=?");
        $stmt->execute([$item_name, $stock_quantity, $price, $is_on_sale, $image_url, $id]);
        $message = "Product updated successfully!";
    }
    
    echo "<script>alert('$message'); window.location='inventory.php';</script>";
}

// Delete Product
if ($action == 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE item_id = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Product deleted successfully!'); window.location='inventory.php';</script>";
}

// Fetch product for editing
$product = null;
if ($action == 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}
?>

<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="card">
    <h2><?php echo $action == 'add' ? 'Add New Product' : 'Edit Product'; ?></h2>
    
    <form method="POST" action="" class="form-container">
        <div class="form-group">
            <label for="item_name">Product Name *</label>
            <input type="text" id="item_name" name="item_name" required 
                   value="<?php echo $product['item_name'] ?? ''; ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" required 
                       value="<?php echo $product['stock_quantity'] ?? 0; ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Price (RM) *</label>
                <input type="number" step="0.01" id="price" name="price" required 
                       value="<?php echo $product['price'] ?? 0.00; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="image_url">Image URL</label>
            <input type="text" id="image_url" name="image_url" 
                   value="<?php echo $product['image_url'] ?? ''; ?>" 
                   placeholder="e.g., image/hijab1.jpg">
        </div>
        
        <div class="form-group checkbox">
            <input type="checkbox" id="is_on_sale" name="is_on_sale" value="1" 
                   <?php echo ($product['is_on_sale'] ?? 0) ? 'checked' : ''; ?>>
            <label for="is_on_sale">On Sale</label>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $action == 'add' ? 'Add Product' : 'Update Product'; ?>
            </button>
            <a href="inventory.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php else: ?>
<div class="card">
    <div class="table-header">
        <h2>Inventory Management</h2>
        <a href="?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    <!-- Simple Filter Buttons -->
<div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px;">
    <a href="inventory.php" class="btn <?php echo !$is_low_stock_view ? 'btn-primary' : 'btn-outline-primary'; ?>">All Items</a>
    <a href="inventory.php?filter=low" class="btn <?php echo $is_low_stock_view ? 'btn-warning' : 'btn-outline-warning'; ?>">
        <i class="fas fa-exclamation-triangle"></i> Low Stock
    </a>
</div>

<table id="inventoryTable" class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Sale</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($items as $item):
            $stock_class = $item['stock_quantity'] < 10 ? 'low-stock' : ($item['stock_quantity'] < 20 ? 'medium-stock' : '');
        ?>
        <tr>
            <td><?php echo $item['item_id']; ?></td>
            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
            <td class="<?php echo $stock_class; ?>"><?php echo $item['stock_quantity']; ?></td>
            <td>RM <?php echo number_format($item['price'], 2); ?></td>
            <td>
                <?php if ($item['is_on_sale']): ?>
                    <span class="badge badge-success">On Sale</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Regular</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="Product" class="thumbnail">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </td>
            <td>
                <a href="?action=edit&id=<?php echo $item['item_id']; ?>" 
                   class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="?action=delete&id=<?php echo $item['item_id']; ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Delete this product?')">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>