<?php
session_start();
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

if (isset($_GET['item_id'])) {
    $item_id = (int)$_GET['item_id'];
    
    // Get product from database
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_id = ?");
    $stmt->execute([$item_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        // Initialize bag
        if (!isset($_SESSION['bag'])) {
            $_SESSION['bag'] = [];
        }
        
        // Check if item is on sale to use correct price
        if ($product['is_on_sale'] == 1) {
            $price_to_use = $product['price'] * 0.8; // Use sale price (20% off)
        } else {
            $price_to_use = $product['price']; // Use regular price
        }
        
        // Check if item already in bag
        $found = false;
        foreach ($_SESSION['bag'] as &$item) {
            if ($item['id'] == $item_id) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }
        
        // If not found, add new item
        if (!$found) {
            $_SESSION['bag'][] = [
                'id' => $product['item_id'],
                'name' => $product['item_name'],
                'price' => $price_to_use, // ← USE THE CORRECT PRICE HERE!
                'image' => $product['image_url'] ?: 'https://images.unsplash.com/photo-1583394060213-9426f491c12d?auto=format&fit=crop&w=800',
                'quantity' => 1
            ];
        }
        
        // Redirect back with success message
        $_SESSION['add_success'] = $product['item_name'];
        header('Location: ' . ($_GET['return'] ?? $_SERVER['HTTP_REFERER'] ?? 'product.php'));
        exit;
    }
}

header('Location: product.php');
?>