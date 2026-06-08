<?php
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
            <h4>Clothing</h4>
            <p>128 Products</p>
          </div>
          <div class="category-card">
            <i class="fas fa-mobile-alt"></i>
            <h4>Electronics</h4>
            <p>96 Products</p>
          </div>
          <div class="category-card">
            <i class="fas fa-gem"></i>
            <h4>Jewelry</h4>
            <p>64 Products</p>
          </div>
          <div class="category-card">
            <i class="fas fa-home"></i>
            <h4>Home & Garden</h4>
            <p>156 Products</p>
          </div>
          <div class="category-card">
            <i class="fas fa-gamepad"></i>
            <h4>Gaming</h4>
            <p>72 Products</p>
          </div>
          <div class="category-card">
            <i class="fas fa-running"></i>
            <h4>Sports</h4>
            <p>88 Products</p>
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
        <div class="product-grid" id="featured-grid"></div>
      </div>
    </section>

    <!-- Trending Products -->
    <section>
      <div class="container">
        <div class="section-header">
          <h2>Trending Products</h2>
          <p>What's popular right now</p>
        </div>
        <div class="product-grid" id="trending-grid"></div>
      </div>
    </section>

    <!-- Newsletter -->
    <section
      style="
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
      "
    >
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

<?php include 'includes/footer.php'; ?>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const featuredGrid = document.getElementById("featured-grid");
        const trendingGrid = document.getElementById("trending-grid");

        if (featuredGrid) {
          featuredGrid.innerHTML = appState.products
            .slice(0, 4)
            .map((p) => renderProductCard(p))
            .join("");
        }
        if (trendingGrid) {
          trendingGrid.innerHTML = appState.products
            .slice(4, 8)
            .map((p) => renderProductCard(p))
            .join("");
        }
      });
    </script>
