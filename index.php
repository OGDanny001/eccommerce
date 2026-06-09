<?php
// Fetch products from database
require 'config/db.php';

$products = [];
if ($conn) {
  $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.image, p.stock, c.name as category_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.id ASC");
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
  $stmt->close();
}

$pageTitle = "LuxuryStore - Premium Shopping";
include 'includes/header.php';
?>

<!-- Hero Slider -->
<section class="hero">
  <div id="slider-container"></div>
</section>

<!-- Categories Section -->
<section>
  <div class="container">
    <div class="section-header">
      <h2>Shop by Category</h2>
      <p>Find what you're looking for</p>
    </div>
    <div class="categories-grid">
      <div class="category-card">
        <i class="fas fa-tshirt"></i>
        <h4>Fashion</h4>
        <p><?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Fashion')); ?> Products</p>
      </div>
      <div class="category-card">
        <i class="fas fa-mobile-alt"></i>
        <h4>Electronics</h4>
        <p><?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Electronics')); ?> Products</p>
      </div>
      <div class="category-card">
        <i class="fas fa-gem"></i>
        <h4>Accessories</h4>
        <p><?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Accessories')); ?> Products</p>
      </div>
      <div class="category-card">
        <i class="fas fa-home"></i>
        <h4>Home & Garden</h4>
        <p><?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Home & Garden')); ?> Products</p>
      </div>
      <div class="category-card">
        <i class="fas fa-gamepad"></i>
        <h4>Electronics</h4>
        <p><?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Electronics')); ?> Products</p>
      </div>
      <div class="category-card">
        <i class="fas fa-running"></i>
        <h4>Sports</h4>
        <p><?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Sports')); ?> Products</p>
      </div>
    </div>
  </div>
</section>

<!-- Featured Products -->
<section style="background: var(--bg-primary)">
  <div class="container">
    <div class="section-header">
      <h2>Featured Products</h2>
      <p>Handpicked for you</p>
    </div>
    <div class="product-grid" id="featured-grid">
      <?php foreach (array_slice($products, 0, 4) as $product): ?>
        <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
          <div class="product-image">
            <div class="product-actions" onclick="event.stopPropagation()">
              <button onclick="toggleWishlist(<?php echo $product['id']; ?>)"><i class="fas fa-heart"></i></button>
            </div>
            <?php if ($product['image']): ?>
              <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%;height:200px;object-fit:cover;">
            <?php else: ?>
              <i class="fas fa-box"></i>
            <?php endif; ?>
          </div>
          <div class="product-info">
            <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Category'); ?></div>
            <h4 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h4>
            <div class="product-price">
              <span class="price-current">$<?php echo number_format($product['price'], 2); ?></span>
            </div>
            <button onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 1rem;">
              <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Trending Products -->
<section>
  <div class="container">
    <div class="section-header">
      <h2>Trending Products</h2>
      <p>What's popular right now</p>
    </div>
    <div class="product-grid" id="trending-grid">
      <?php foreach (array_slice($products, 4, 4) as $product): ?>
        <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
          <div class="product-image">
            <div class="product-actions" onclick="event.stopPropagation()">
              <button onclick="toggleWishlist(<?php echo $product['id']; ?>)"><i class="fas fa-heart"></i></button>
            </div>
            <?php if ($product['image']): ?>
              <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%;height:200px;object-fit:cover;">
            <?php else: ?>
              <i class="fas fa-box"></i>
            <?php endif; ?>
          </div>
          <div class="product-info">
            <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Category'); ?></div>
            <h4 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h4>
            <div class="product-price">
              <span class="price-current">$<?php echo number_format($product['price'], 2); ?></span>
            </div>
            <button onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 1rem;">
              <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Newsletter -->
<section
  style="
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
      ">
  <div class="container" style="text-align: center">
    <h2 style="margin-bottom: 1rem">Stay Updated</h2>
    <p style="margin-bottom: 2rem; opacity: 0.9">
      Subscribe to our newsletter and get 15% off your first order!
    </p>
    <div class="newsletter-form" style="max-width: 500px; margin: 0 auto">
      <input type="email" placeholder="Enter your email address" />
      <button class="btn btn-secondary">
        <i class="fas fa-paper-plane"></i> Subscribe
      </button>
    </div>
  </div>
</section>

<script>
  // Pass products from PHP to JS for cart/wishlist functions
  const dbProducts = <?php echo json_encode($products); ?>;
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
    icon: 'box',
    image: p.image
  }));
</script>

<?php include 'includes/footer.php'; ?>