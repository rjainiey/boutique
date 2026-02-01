<?php
require_once 'config.php';
$page_title = "Sale | KEKABOO BOUTIQUE";
include 'header.php';
?>

<div class="container">
    <div style="padding: 60px 0 40px;">
        <h1 style="font-size: 36px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; text-align: center; margin-bottom: 8px; color: var(--red);">Sale</h1>
        <p style="text-align: center; font-size: 14px; color: var(--gray-500);">Limited time offers on select pieces</p>
    </div>

    <div class="product-grid">
        <?php
        // CORRECTED QUERY: Only show items where is_on_sale = 1
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE is_on_sale = 1 AND stock_quantity > 0 ORDER BY price ASC");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($products)) {
            echo '<p style="text-align: center; grid-column: 1 / -1; padding: 60px 0; color: var(--gray-500);">No sale items at the moment.</p>';
        } else {
            foreach ($products as $product):
                $image_url = $product['image_url'] ?: 'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?auto=format&fit=crop&w=800';
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($product['item_name']); ?>">
                <div class="product-badge badge-sale">Sale</div>
            </div>
            <div class="product-info">
                <h3 class="product-title"><?php echo htmlspecialchars($product['item_name']); ?></h3>
                <div class="product-price">
                    <?php 
                    // Database price is the ORIGINAL price
                    $original_price = $product['price']; // From database
                    $sale_price = $original_price * 0.8; // Calculate 20% off
                    ?>
                    <span class="product-price old">RM <?php echo number_format($original_price, 2); ?></span>
                    <span class="product-price sale">RM <?php echo number_format($sale_price, 2); ?></span>
                </div>
                <div style="font-size: 11px; color: var(--gray-500); margin-bottom: 12px;">
                    Save RM <?php echo number_format($original_price - $sale_price, 2); ?> (20%)
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