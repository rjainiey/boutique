<?php
require_once 'config.php';

// Fetch all customers (matching the logic in customers.php)
$customers = $pdo->query("SELECT * FROM customer ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Report - <?php echo date('Y-m-d'); ?></title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .report-title { font-size: 24px; font-weight: bold; margin: 0; }
        .date { color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; text-align: left; font-size: 14px; }
        td { border: 1px solid #dee2e6; padding: 10px; font-size: 12px; vertical-align: top; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        
        /* This hides buttons when saving to PDF */
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #fff3cd; padding: 15px; margin-bottom: 20px; border: 1px solid #ffeeba; border-radius: 5px; text-align: center;">
        <strong>Customer PDF Report:</strong> Please select <strong>"Save as PDF"</strong> as the Destination.
        <br><br>
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Save as PDF Now</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <h1 class="report-title">KEKABOO BOUTIQUE</h1>
        <p class="date">Customer Directory Report | Generated: <?php echo date('d M Y, h:i A'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Date Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
            <tr>
                <td>#<?php echo $customer['customer_id']; ?></td>
                <td><strong><?php echo htmlspecialchars($customer['cust_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                <td><?php echo htmlspecialchars($customer['num_phone']); ?></td>
                <td><?php echo htmlspecialchars($customer['address']); ?></td>
                <td><?php echo date('d M Y', strtotime($customer['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>