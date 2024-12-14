<?php
require_once 'config.php';
require_once 'includes/Products.php';

session_start();

// Get products
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products = Products::getInstance()->getAllProducts($page, ITEMS_PER_PAGE);

// Get featured products
$featuredProducts = Products::getInstance()->getFeaturedProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=<?php echo PAYPAL_CURRENCY; ?>&intent=capture"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?php echo SITE_NAME; ?></a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/cart.php">
                            Cart <span class="badge bg-primary"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Featured Products -->
        <?php if (!empty($featuredProducts)): ?>
        <h2 class="mb-4">Featured Products</h2>
        <div class="row mb-5">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="badge bg-primary position-absolute" style="top: 0.5rem; right: 0.5rem">Featured</div>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="card-text"><strong>Price: <?php echo PAYPAL_CURRENCY; ?> <?php echo number_format($product['price'], 2); ?></strong></p>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- All Products -->
        <h2 class="mb-4">All Products</h2>
        <div class="row">
            <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($product['stock'] < 5): ?>
                    <div class="badge bg-warning position-absolute" style="top: 0.5rem; right: 0.5rem">Low Stock</div>
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="card-text"><strong>Price: <?php echo PAYPAL_CURRENCY; ?> <?php echo number_format($product['price'], 2); ?></strong></p>
                        <p class="card-text"><small class="text-muted">Category: <?php echo htmlspecialchars($product['category']); ?></small></p>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary" <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                <?php echo $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 