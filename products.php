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

$pageTitle = "All Products - LuxuryStore";
include 'includes/header.php';
?>

<!-- Shop Content -->
<section>
  <div class="container">
    <div
      class="shop-header"
      style="
            background: var(--bg-primary);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
          ">
      <div>
        <h2>All Products</h2>
        <p style="color: var(--text-secondary)">
          Showing <span id="productCount"><?php echo count($products); ?></span> products
        </p>
      </div>
      <select
        class="sort-select"
        style="
              padding: 0.5rem 1rem;
              border: 1px solid var(--border-color);
              border-radius: var(--radius-md);
            ">
        <option>Sort by: Featured</option>
        <option>Price: Low to High</option>
        <option>Price: High to Low</option>
        <option>Newest First</option>
        <option>Best Rated</option>
      </select>
    </div>

    <div
      class="shop-layout"
      style="display: grid; grid-template-columns: 280px 1fr; gap: 2rem">
      <!-- Filters Sidebar -->
      <aside
        class="filters"
        style="
              background: var(--bg-primary);
              border-radius: var(--radius-lg);
              padding: 1.5rem;
              box-shadow: var(--shadow-sm);
              height: fit-content;
            ">
        <div class="filter-group">
          <h4>Categories</h4>
          <ul class="filter-options" style="list-style: none">
            <li
              style="
                    margin-bottom: 0.75rem;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                  ">
              <input type="checkbox" id="cat1" /><label for="cat1">Electronics (<?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Electronics')); ?>)</label>
            </li>
            <li
              style="
                    margin-bottom: 0.75rem;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                  ">
              <input type="checkbox" id="cat2" /><label for="cat2">Fashion (<?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Fashion')); ?>)</label>
            </li>
            <li
              style="
                    margin-bottom: 0.75rem;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                  ">
              <input type="checkbox" id="cat3" /><label for="cat3">Home & Garden (<?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Home & Garden')); ?>)</label>
            </li>
            <li
              style="
                    margin-bottom: 0.75rem;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                  ">
              <input type="checkbox" id="cat4" /><label for="cat4">Sports (<?php echo count(array_filter($products, fn($p) => $p['category_name'] === 'Sports')); ?>)</label>
            </li>
          </ul>
        </div>
        <div class="filter-group">
          <h4>Price Range</h4>
          <ul class="filter-options">
            <li
              style="
                    margin-bottom: 0.75rem;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                  ">
              <input type="checkbox" id="p1" /><label for="p1">Under $50</label>
            </li>
            <li
              style="
                    margin-bottom: 0.75rem;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                  ">
              <input type="checkbox" id="p2" /><label for="p2">$50 - $100</label>
            </li>
            <li
              style="
                    margin-bottom: 0.75rem;
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                  ">
              <input type="checkbox" id="p3" /><label for="p3">$100 - $200</label>
            </li>
          </ul>
        </div>
        <button class="btn btn-primary" style="width: 100%">
          <i class="fas fa-filter"></i> Apply Filters
        </button>
      </aside>

      <!-- Products Grid -->
      <div>
        <div class="product-grid" id="products-grid">
          <?php foreach ($products as $product): ?>
            <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
              <div class="product-image">
                <div class="product-actions" onclick="event.stopPropagation()">
                  <button onclick="toggleWishlist(<?php echo $product['id']; ?>)"><i class="fas fa-heart"></i></button>
                </div>
                <?php if ($product['image']): ?>
                  <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%;height:200px;object-fit:cover;">
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
    icon: 'box'
  }));

  // Check for saved search query
  document.addEventListener('DOMContentLoaded', () => {
    const savedQuery = localStorage.getItem('searchQuery');
    if (savedQuery) {
      const searchInput = document.getElementById('searchInput');
      if (searchInput) searchInput.value = savedQuery;
      filterProducts(savedQuery);
      localStorage.removeItem('searchQuery');
    }
  });
</script>

<?php include 'includes/footer.php'; ?>