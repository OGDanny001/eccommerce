<?php
// Include database connection
require 'config/db.php';

// Get and sanitize product ID from URL parameter
$productId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Initialize product data
$product = null;
$allProducts = [];

// Fetch all products for appState and related products
if ($conn) {
    $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.image, p.category_id, p.stock, c.name as category_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.id ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $allProducts[] = $row;
    }
    $stmt->close();

    // Fetch the specific product
    if ($productId) {
        foreach ($allProducts as $p) {
            if ($p['id'] === $productId) {
                $product = $p;
                break;
            }
        }
    }
}

// Set page title
$pageTitle = $product ? htmlspecialchars($product['name']) : "Product Details";
include 'includes/header.php';
?>

<style>
    .product-detail-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
    }

    .product-gallery {
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10rem;
        box-shadow: var(--shadow-sm);
        color: var(--text-muted);
    }

    .product-card {
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .qty-selector {
        display: flex;
        gap: 1rem;
        margin: 1.5rem 0;
    }

    .qty-input {
        display: flex;
        align-items: center;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .qty-input button {
        padding: 0.75rem 1rem;
        border: none;
        background: var(--bg-secondary);
        cursor: pointer;
    }

    .qty-input input {
        border: none;
        width: 60px;
        text-align: center;
    }

    .product-tabs {
        margin-top: 3rem;
        border-bottom: 1px solid var(--border-color);
    }

    .tab-buttons {
        display: flex;
        gap: 2rem;
    }

    .tab-btn {
        padding: 1rem 0;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        color: var(--text-secondary);
        border-bottom: 3px solid transparent;
    }

    .tab-btn.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }

    .tab-content {
        padding: 2rem 0;
    }

    @media (max-width: 768px) {
        .product-detail-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Product Detail -->
<section>
    <div class="container">
        <?php if ($product): ?>
            <div class="product-detail-layout">
                <div class="product-gallery">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width:100%; max-height:400px; object-fit:contain;" />
                    <?php else: ?>
                        <i class="fas fa-box"></i>
                    <?php endif; ?>
                </div>
                <div class="product-info-section">
                    <p class="product-category">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Category'); ?>
                    </p>
                    <h1 style="margin-bottom: 1rem"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-rating" style="margin-bottom: 1rem">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <span style="color: var(--text-muted)">(0 reviews)</span>
                    </div>
                    <div class="product-price" style="margin-bottom: 1.5rem">
                        <span class="price-current" style="font-size: 2rem">$<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem">
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>
                    <p style="color: <?php echo $product['stock'] > 0 ? 'var(--text-secondary)' : '#dc2626'; ?>; margin-bottom: 1rem;">
                        Stock: <?php echo htmlspecialchars($product['stock']); ?>
                    </p>
                    <div class="qty-selector">
                        <div class="qty-input">
                            <button onclick="changeQty(-1)">-</button>
                            <input type="number" id="qty-input" value="1" min="1" max="<?php echo $product['stock']; ?>" />
                            <button onclick="changeQty(1)">+</button>
                        </div>
                        <button
                            onclick="addToCart(<?php echo $product['id']; ?>)"
                            class="btn btn-primary btn-lg"
                            style="flex: 1"
                            <?php echo $product['stock'] <= 0 ? 'disabled style="opacity:0.5; cursor:not-allowed; flex:1"' : ''; ?>>
                            <?php echo $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                        </button>
                        <button
                            onclick="toggleWishlist(<?php echo $product['id']; ?>)"
                            class="btn btn-secondary btn-lg"
                            style="padding: 0 1.5rem">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="product-tabs">
                <div class="tab-buttons">
                    <button class="tab-btn active" onclick="switchTab(0)">
                        Description
                    </button>
                    <button class="tab-btn" onclick="switchTab(1)">
                        Specifications
                    </button>
                    <button class="tab-btn" onclick="switchTab(2)">
                        Reviews (0)
                    </button>
                </div>
            </div>
            <div class="tab-content" id="tab-content">
                <h3 style="margin-bottom: 1rem">Product Description</h3>
                <p style="color: var(--text-secondary); line-height: 1.8">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 0;">
                <i class="fas fa-exclamation-circle" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                <h2 style="margin-bottom: 1rem;">Product Not Found</h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">The product you are looking for does not exist.</p>
                <a href="index.php" class="btn btn-primary">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Products -->
<section style="background: var(--bg-primary)">
    <div class="container">
        <div class="section-header">
            <h2>Related Products</h2>
            <p>You might also like</p>
        </div>
        <div class="product-grid" id="related-grid">
            <?php
            // Get related products (same category, exclude current product)
            $relatedProducts = array_filter($allProducts, function ($p) use ($product) {
                return $product && $p['id'] !== $product['id'] && $p['category_id'] === $product['category_id'];
            });
            // If no related products, show random products
            if (empty($relatedProducts)) {
                $relatedProducts = array_filter($allProducts, function ($p) use ($product) {
                    return $product && $p['id'] !== $product['id'];
                });
            }
            // Show up to 4 related products
            $relatedProducts = array_slice($relatedProducts, 0, 4);
            foreach ($relatedProducts as $p):
            ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $p['id']; ?>'">
                    <div class="product-image">
                        <div class="product-actions" onclick="event.stopPropagation()">
                            <button onclick="toggleWishlist(<?php echo $p['id']; ?>)"><i class="fas fa-heart"></i></button>
                        </div>
                        <?php if ($p['image']): ?>
                            <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" style="width:100%;height:200px;object-fit:cover;" />
                        <?php else: ?>
                            <i class="fas fa-box"></i>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($p['category_name'] ?? 'Category'); ?></div>
                        <h4 class="product-title"><?php echo htmlspecialchars($p['name']); ?></h4>
                        <div class="product-price">
                            <span class="price-current">$<?php echo number_format($p['price'], 2); ?></span>
                        </div>
                        <button onclick="event.stopPropagation(); addToCart(<?php echo $p['id']; ?>)" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 1rem;">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
    // Pass products from PHP to JS for cart/wishlist functions
    const dbProducts = <?php echo json_encode($allProducts); ?>;
    // Map DB products to appState.products format
    appState.products = dbProducts.map(p => ({
        id: p.id,
        name: p.name,
        category: p.category_name,
        price: p.price,
        oldPrice: null,
        rating: 5,
        ratingCount: 10,
        badge: null,
        icon: 'box'
    }));

    let currentQty = 1;
    const productId = <?php echo $productId; ?>;

    function changeQty(delta) {
        const maxQty = <?php echo $product ? $product['stock'] : 1; ?>;
        currentQty = Math.max(1, Math.min(maxQty, currentQty + delta));
        document.getElementById("qty-input").value = currentQty;
    }

    function switchTab(tabIndex) {
        const btns = document.querySelectorAll(".tab-btn");
        btns.forEach((btn, i) => {
            btn.classList.toggle("active", i === tabIndex);
        });
        const content = document.getElementById("tab-content");
        if (tabIndex === 0) {
            content.innerHTML = `
            <h3 style="margin-bottom: 1rem;">Product Description</h3>
            <p style="color: var(--text-secondary); line-height: 1.8;"><?php echo $product ? addslashes($product['description']) : ''; ?></p>
          `;
        } else if (tabIndex === 1) {
            content.innerHTML = `
            <h3 style="margin-bottom: 1rem;">Specifications</h3>
            <p style="color: var(--text-secondary);">Specifications coming soon!</p>
          `;
        } else {
            content.innerHTML = `
            <h3 style="margin-bottom: 1rem;">Reviews</h3>
            <p style="color: var(--text-secondary);">Customer reviews coming soon!</p>
          `;
        }
    }
</script>