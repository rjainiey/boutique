<?php
require_once 'config.php';

$orders = $pdo->query("
    SELECT p.*, c.cust_name, c.email 
    FROM purchase p 
    JOIN customer c ON p.customer_id = c.customer_id 
    ORDER BY p.order_date DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Report - <?php echo date('Y-m-d'); ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .report-title { font-size: 24px; font-weight: bold; margin: 0; }
        .date { color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; text-align: left; }
        td { border: 1px solid #dee2e6; padding: 10px; font-size: 13px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        
        .status-badge { text-transform: uppercase; font-size: 11px; font-weight: bold; }
        
        /* This hides buttons when saving to PDF */
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #fff3cd; padding: 15px; margin-bottom: 20px; border: 1px solid #ffeeba; border-radius: 5px; text-align: center;">
        <strong>PDF Generation:</strong> Please select <strong>"Save as PDF"</strong> as the Destination in the print window.
        <br><br>
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Save as PDF Now</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Back to Orders</button>
    </div>

    <div class="header">
        <h1 class="report-title">KEKABOO BOUTIQUE</h1>
        <p class="date">Order Management Report | Generated: <?php echo date('d M Y, h:i A'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Item Details</th>
                <th>Total</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['purchase_id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($order['cust_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['email']); ?>
                </td>
                <td><?php echo htmlspecialchars($order['item_name']); ?></td>
                <td>RM <?php echo number_format($order['item_price'], 2); ?></td>
                <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                <td class="status-badge"><?php echo $order['status']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Automatically trigger the print/save dialog when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>