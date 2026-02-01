<?php
require_once 'config.php';
$page_title = "Products | KEKABOO BOUTIQUE";
include 'header.php';

// This shows all items (both sale and regular)
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE stock_quantity > 0");
?>

<div class="container">
    <div style="padding: 60px 0 40px;">
        <h1 style="font-size: 36px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; text-align: center; margin-bottom: 8px;">All Collections</h1>
        <p style="text-align: center; font-size: 14px; color: var(--gray-500);">Discover our complete monochrome range</p>
    </div>

    <div class="product-grid">
        <?php
        // Get all products
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE stock_quantity > 0 ORDER BY item_id DESC");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($products)) {
            echo '<p style="text-align: center; grid-column: 1 / -1; padding: 60px 0; color: var(--gray-500);">No products available yet.</p>';
        } else {
            foreach ($products as $product):
                $image_url = $product['image_url'] ?: 'https://images.unsplash.com/photo-1583394060213-9426f491c12d?auto=format&fit=crop&w=800';
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($product['item_name']); ?>">
            </div>
            <div class="product-info">
                <h3 class="product-title"><?php echo htmlspecialchars($product['item_name']); ?></h3>
                <div class="product-price">
                    RM <?php echo number_format($product['price'], 2); ?>
                </div>
                <div style="font-size: 11px; color: var(--gray-500); margin-bottom: 12px;">
                    Stock: <?php echo $product['stock_quantity']; ?>
                </div>
                <a href="addtobag.php?item_id=<?php echo $product['item_id']; ?>&return=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                class="btn-add-to-cart">
                    Add to Bag
                </a>
            </div>
        </div>
        <?php endforeach; } ?>
    </div>
</div>

<?php include 'footer.php'; ?>