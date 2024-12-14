<?php
require_once 'config.php';
require_once 'includes/Products.php';

session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productsManager = Products::getInstance();

// Handle Add to Cart
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $product = $productsManager->getProductById($product_id);
    
    if ($product && $product['stock'] > 0) {
        if (!isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = 1;
        } else {
            $_SESSION['cart'][$product_id]++;
        }
    }
    header('Location: cart.php');
    exit;
}

// Handle Remove from Cart
if (isset($_POST['remove_from_cart']) && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
    header('Location: cart.php');
    exit;
}

// Get cart items
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $products = $productsManager->getProductsByIds($ids);

    foreach ($products as $product) {
        $quantity = (int)$_SESSION['cart'][$product['id']];
        $price = (float)$product['price'];
        $subtotal = $price * $quantity;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=<?php echo PAYPAL_CURRENCY; ?>&intent=capture"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?php echo SITE_NAME; ?></a>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Shopping Cart</h1>
        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">Your cart is empty.</div>
            <a href="/" class="btn btn-primary">Continue Shopping</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;" class="me-2">
                                <?php echo htmlspecialchars($item['product']['name']); ?>
                            </td>
                            <td><?php echo PAYPAL_CURRENCY; ?> <?php echo number_format($item['product']['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo PAYPAL_CURRENCY; ?> <?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                    <button type="submit" name="remove_from_cart" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong><?php echo PAYPAL_CURRENCY; ?> <?php echo number_format($total, 2); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="/" class="btn btn-secondary">Continue Shopping</a>
                </div>
                <div class="col-md-6 text-end">
                    <div id="paypal-button-container"></div>
                </div>
            </div>

            <script>
                paypal.Buttons({
                    style: {
                        layout: 'vertical',
                        color:  'blue',
                        shape:  'rect',
                        label:  'paypal'
                    },
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    currency_code: '<?php echo PAYPAL_CURRENCY; ?>',
                                    value: '<?php echo number_format($total, 2, '.', ''); ?>'
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            window.location.href = 'order-success.php?order_id=' + details.id;
                        });
                    },
                    onError: function(err) {
                        console.error('PayPal error:', err);
                        alert('There was an error processing your payment. Please try again.');
                    }
                }).render('#paypal-button-container');
            </script>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 