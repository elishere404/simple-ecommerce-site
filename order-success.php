<?php
require_once 'config.php';
require_once 'includes/db.php';

session_start();

if (!isset($_GET['order_id'])) {
    header('Location: /');
    exit;
}

$db = Database::getInstance()->getConnection();

// Save order to database
$paypal_order_id = $_GET['order_id'];
$total = 0;

// Calculate total from cart
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $query = "SELECT price FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $total += $product['price'] * $quantity;
}

// Insert order
$query = "INSERT INTO orders (paypal_order_id, total_amount, status) VALUES (?, ?, 'completed')";
$stmt = $db->prepare($query);
$stmt->bind_param('sd', $paypal_order_id, $total);
$stmt->execute();
$order_id = $db->insert_id;

// Insert order items
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $query = "SELECT price FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param('iiid', $order_id, $product_id, $quantity, $product['price']);
    $stmt->execute();
    
    // Update stock
    $query = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('ii', $quantity, $product_id);
    $stmt->execute();
}

// Clear the cart
$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?php echo SITE_NAME; ?></a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <h1 class="card-title text-success">Order Successful!</h1>
                        <p class="card-text">Thank you for your purchase. Your order ID is: <?php echo htmlspecialchars($paypal_order_id); ?></p>
                        <p class="card-text">We'll process your order shortly.</p>
                        <a href="/" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 