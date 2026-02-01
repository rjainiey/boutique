<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php?redirect=bag.php');
    exit;
}

$current_user = getCurrentUser($pdo);
$user_points = getUserPoints($pdo, $_SESSION['customer_id']);

// Initialize bag from session
if (!isset($_SESSION['bag'])) {
    $_SESSION['bag'] = [];
}

$bag_items = $_SESSION['bag'];

// Handle bag actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $item_id = $_POST['item_id'];
        $quantity = (int)$_POST['quantity'];
        
        foreach ($bag_items as &$item) {
            if ($item['id'] == $item_id) {
                if ($quantity <= 0) {
                    // Remove item
                    $bag_items = array_filter($bag_items, fn($i) => $i['id'] != $item_id);
                } else {
                    $item['quantity'] = $quantity;
                }
                break;
            }
        }
        
        $_SESSION['bag'] = $bag_items;
        header('Location: bag.php');
        exit;
        
    } elseif (isset($_POST['remove_item'])) {
        $item_id = $_POST['item_id'];
        $bag_items = array_filter($bag_items, fn($item) => $item['id'] != $item_id);
        $_SESSION['bag'] = $bag_items;
        header('Location: bag.php');
        exit;
        
    } elseif (isset($_POST['checkout'])) {
        $redeem_points = (int)($_POST['redeem_points'] ?? 0);
        
        if ($redeem_points > $user_points) {
            $error = "Insufficient points";
        } elseif (empty($bag_items)) {
            $error = "Your bag is empty";
        } else {
            try {
                $pdo->beginTransaction();
                
                // Calculate totals
                $subtotal = 0;
                foreach ($bag_items as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                }
                
                $shipping = $subtotal >= 100 ? 0 : 15.00;
                
                // Calculate discount from points
                $discount_percent = ($redeem_points / 100) * 5; // 5% per 100 points
                $discount_amount = $subtotal * ($discount_percent / 100);
                $total = $subtotal + $shipping - $discount_amount;
                
                // Create item list string
                $item_names = array_map(fn($item) => $item['name'] . ' (x' . $item['quantity'] . ')', $bag_items);
                $item_list = implode(', ', $item_names);
                
                // 1. FIRST: Check stock availability before proceeding
                foreach ($bag_items as $item) {
                    $stmt = $pdo->prepare("SELECT stock_quantity FROM inventory WHERE item_id = ?");
                    $stmt->execute([$item['id']]);
                    $stock = $stmt->fetchColumn();
                    
                    if ($stock < $item['quantity']) {
                        throw new Exception("Sorry, '" . $item['name'] . "' only has " . $stock . " items left in stock.");
                    }
                }
                
                // 2. Create purchase record
                $stmt = $pdo->prepare("INSERT INTO purchase (customer_id, item_name, item_price, order_date, status) 
                                     VALUES (?, ?, ?, CURDATE(), 'pending')");
                $stmt->execute([
                    $_SESSION['customer_id'],
                    $item_list,
                    $total
                ]);
                $purchase_id = $pdo->lastInsertId();
                
                // 3. UPDATE INVENTORY - DECREASE STOCK FOR EACH ITEM
                foreach ($bag_items as $item) {
                    $stmt = $pdo->prepare("UPDATE inventory 
                                         SET stock_quantity = stock_quantity - ? 
                                         WHERE item_id = ?");
                    $stmt->execute([$item['quantity'], $item['id']]);
                    
                    // Optional: Verify the update was successful
                    if ($stmt->rowCount() === 0) {
                        // This shouldn't happen since we checked stock earlier, but good to have
                        throw new Exception("Failed to update inventory for: " . $item['name']);
                    }
                }
                
                // 4. Create transaction
                $stmt = $pdo->prepare("INSERT INTO transaction (purchase_id, payment_method, payment_status) 
                                     VALUES (?, 'credit_card', 'pending')");
                $stmt->execute([$purchase_id]);
                
                // 5. Update points if redeemed
                if ($redeem_points > 0) {
                    $stmt = $pdo->prepare("INSERT INTO points (customer_id, total_price) 
                                         VALUES (?, -?)");
                    $stmt->execute([$_SESSION['customer_id'], $redeem_points]);
                }
                
                // 6. Add earned points (1 point per RM 1 spent)
                $earned_points = floor($total);
                $stmt = $pdo->prepare("INSERT INTO points (customer_id, total_price) 
                                     VALUES (?, ?)");
                $stmt->execute([$_SESSION['customer_id'], $earned_points]);
                
                $pdo->commit();
                
                // Clear bag and show success
                $_SESSION['bag'] = [];
                $_SESSION['checkout_success'] = [
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'discount' => $discount_amount,
                    'total' => $total,
                    'redeemed_points' => $redeem_points,
                    'earned_points' => $earned_points,
                    'purchase_id' => $purchase_id
                ];
                
                header('Location: bag.php?success=1');
                exit;
                
            } catch(Exception $e) {
                $pdo->rollBack();
                $error = "Checkout failed: " . $e->getMessage();
            }
        }
    } // <-- ADDED THIS CLOSING BRACE
} // <-- This closes the main POST method check

// Check for success message
$success_data = $_SESSION['checkout_success'] ?? null;
if ($success_data && isset($_GET['success'])) {
    unset($_SESSION['checkout_success']);
}

$page_title = "My Bag | KEKABOO BOUTIQUE";
include 'header.php';
?>

<div class="bag-container">
    <!-- Success Modal -->
    <?php if ($success_data): ?>
    <div class="message success" style="margin-bottom: 30px; animation: slideDown 0.3s ease;">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 10px;">Order Placed Successfully!</h3>
        <p style="margin-bottom: 5px; font-weight: 600;">Order #<?php echo $success_data['purchase_id']; ?></p>
        <p style="margin-bottom: 5px;">Subtotal: RM <?php echo number_format($success_data['subtotal'], 2); ?></p>
        <p style="margin-bottom: 5px;">Shipping: <?php echo $success_data['shipping'] == 0 ? 'FREE' : 'RM ' . number_format($success_data['shipping'], 2); ?></p>
        <?php if ($success_data['discount'] > 0): ?>
        <p style="margin-bottom: 5px; color: var(--green);">Discount: -RM <?php echo number_format($success_data['discount'], 2); ?></p>
        <?php endif; ?>
        <p style="font-weight: 700; margin: 10px 0;">Total: RM <?php echo number_format($success_data['total'], 2); ?></p>
        <p style="margin-top: 15px; font-size: 13px;">
            Points Redeemed: <?php echo $success_data['redeemed_points']; ?> | 
            Points Earned: +<?php echo $success_data['earned_points']; ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
    <div class="message error" style="margin-bottom: 30px;"><?php echo $error; ?></div>
    <?php endif; ?>

    <h1 style="font-size: 36px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; text-align: center; margin-bottom: 40px;">
        My Bag
    </h1>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
        <!-- Bag Items -->
        <div>
            <?php if (empty($bag_items)): ?>
            <div style="text-align: center; padding: 80px 0;">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 16px; color: var(--gray-700);">Your bag is empty</h2>
                <p style="color: var(--gray-500); margin-bottom: 30px;">Add some beautiful monochrome pieces to your collection</p>
                <a href="product.php" class="btn">Shop Now</a>
            </div>
            <?php else: ?>
            <?php 
            $subtotal = 0;
            foreach ($bag_items as $item):
                $item_total = $item['price'] * $item['quantity'];
                $subtotal += $item_total;
            ?>
            <div class="bag-item">
                <div class="bag-item-image">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                </div>
                <div class="bag-item-details">
                    <div>
                        <h3 class="bag-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="bag-item-price">RM <?php echo number_format($item['price'], 2); ?></div>
                        
                        <form method="POST" class="quantity-controls">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="update_quantity" value="1" class="quantity-btn" 
                                    onclick="this.form.querySelector('input[name=quantity]').value = <?php echo max(1, $item['quantity'] - 1); ?>">-</button>
                            <input type="hidden" name="quantity" value="<?php echo $item['quantity']; ?>">
                            <span class="quantity"><?php echo $item['quantity']; ?></span>
                            <button type="submit" name="update_quantity" value="1" class="quantity-btn"
                                    onclick="this.form.querySelector('input[name=quantity]').value = <?php echo $item['quantity'] + 1; ?>">+</button>
                        </form>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-weight: 700;">RM <?php echo number_format($item_total, 2); ?></div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="remove_item" value="1" class="btn-remove">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Checkout Summary -->
        <?php if (!empty($bag_items)): ?>
        <div>
            <form method="POST" class="checkout-summary">
                <h2 class="summary-title">Order Summary</h2>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal-display">RM <?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <?php 
                $shipping = $subtotal >= 100 ? 0 : 15.00;
                $shipping_display = $shipping == 0 ? 'FREE' : 'RM ' . number_format($shipping, 2);
                ?>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="shipping-display"><?php echo $shipping_display; ?></span>
                </div>

                <!-- Points Redemption -->
                <div class="points-section">
                    <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--black);">Redeem Points</h3>
                    <p style="font-size: 12px; color: var(--gray-600); margin-bottom: 16px;">
                        Available: <strong><?php echo $user_points; ?> points</strong>
                    </p>
                    
                    <div class="points-options">
                        <?php
                        $point_options = [100, 200, 300, 400, 500];
                        foreach ($point_options as $points):
                            $disabled = $points > $user_points;
                        ?>
                        <div class="point-option <?php echo $disabled ? 'disabled' : ''; ?>" 
                             onclick="if(!this.classList.contains('disabled')) selectPoints(<?php echo $points; ?>)">
                            <div class="point-value"><?php echo $points; ?></div>
                            <div class="point-discount"><?php echo ($points/100)*5; ?>% OFF</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <input type="hidden" name="redeem_points" id="redeem-points" value="0">
                    
                    <div style="display: flex; gap: 8px; margin-top: 12px;">
                        <button type="button" class="btn" style="flex: 1; padding: 10px; font-size: 11px;" 
                                onclick="applyPoints()" id="apply-btn">
                            Apply Points
                        </button>
                        <button type="button" class="btn btn-outline" style="padding: 10px; font-size: 11px;" 
                                onclick="resetPoints()" id="reset-btn">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Discount Row -->
                <div id="discount-row" class="summary-row" style="display: none;">
                    <span>Points Discount</span>
                    <span id="discount-display" style="color: var(--green);">-RM 0.00</span>
                </div>
                
                <div class="summary-row total">
                    <span>TOTAL</span>
                    <span id="total-display">RM <?php echo number_format($subtotal + $shipping, 2); ?></span>
                </div>
                
                <button type="submit" name="checkout" value="1" class="btn-checkout">
                    Proceed to Checkout
                </button>
                
                <p style="text-align: center; font-size: 12px; color: var(--gray-500); margin-top: 16px;">
                    Free shipping on orders over RM 100
                </p>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
let selectedPoints = 0;
let subtotal = <?php echo $subtotal ?? 0; ?>;
let shipping = <?php echo $shipping ?? 15.00; ?>;
let userPoints = <?php echo $user_points; ?>;

function selectPoints(points) {
    if (points > userPoints) return;
    
    selectedPoints = points;
    
    // Update UI
    document.querySelectorAll('.point-option').forEach(option => {
        const optionPoints = parseInt(option.querySelector('.point-value').textContent);
        if (optionPoints === points) {
            option.classList.add('selected');
        } else {
            option.classList.remove('selected');
        }
    });
    
    document.getElementById('redeem-points').value = points;
    document.getElementById('apply-btn').textContent = 'Apply ' + points + ' Points';
    document.getElementById('reset-btn').style.display = 'block';
}

function applyPoints() {
    if (selectedPoints === 0) {
        alert('Please select points to redeem');
        return;
    }
    
    const discountPercent = (selectedPoints / 100) * 5;
    const discountAmount = subtotal * (discountPercent / 100);
    const total = subtotal + shipping - discountAmount;
    
    // Update display
    document.getElementById('discount-display').textContent = '-RM ' + discountAmount.toFixed(2);
    document.getElementById('discount-row').style.display = 'flex';
    document.getElementById('total-display').textContent = 'RM ' + total.toFixed(2);
    
    // Show success message
    alert(selectedPoints + ' points applied! You saved RM ' + discountAmount.toFixed(2));
}

function resetPoints() {
    selectedPoints = 0;
    document.querySelectorAll('.point-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    document.getElementById('redeem-points').value = 0;
    document.getElementById('apply-btn').textContent = 'Apply Points';
    document.getElementById('discount-row').style.display = 'none';
    document.getElementById('total-display').textContent = 'RM ' + (subtotal + shipping).toFixed(2);
    document.getElementById('reset-btn').style.display = 'none';
}
</script>

<?php include 'footer.php'; ?>