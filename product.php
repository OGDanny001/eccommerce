<?php
// Get product ID from URL parameter
$productId = isset($_GET['id']) ? $_GET['id'] : 1;

// Set page title
$pageTitle = "Product Details";
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
        <div class="product-detail-layout">
            <div class="product-gallery">
                <i class="fas fa-box"></i>
            </div>
            <div class="product-info-section">
                <p class="product-category">Category</p>
                <h1 style="margin-bottom: 1rem">Product Name</h1>
                <div class="product-rating" style="margin-bottom: 1rem">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <span style="color: var(--text-muted)">(0 reviews)</span>
                </div>
                <div class="product-price" style="margin-bottom: 1.5rem">
                    <span class="price-current" style="font-size: 2rem">$0</span>
                </div>
                <p style="color: var(--text-secondary); margin-bottom: 2rem">
                    Product description will be loaded here.
                </p>
                <div class="qty-selector">
                    <div class="qty-input">
                        <button onclick="changeQty(-1)">-</button>
                        <input type="number" id="qty-input" value="1" min="1" />
                        <button onclick="changeQty(1)">+</button>
                    </div>
                    <button
                        onclick="addToCart(<?php echo $productId; ?>)"
                        class="btn btn-primary btn-lg"
                        style="flex: 1">
                        Add to Cart
                    </button>
                    <button
                        onclick="toggleWishlist(<?php echo $productId; ?>)"
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
                Placeholder for product description.
            </p>
        </div>
    </div>
</section>

<!-- Related Products -->
<section style="background: var(--bg-primary)">
    <div class="container">
        <div class="section-header">
            <h2>Related Products</h2>
            <p>You might also like</p>
        </div>
        <div class="product-grid" id="related-grid"></div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
    let currentQty = 1;

    function changeQty(delta) {
        currentQty = Math.max(1, currentQty + delta);
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
            <p style="color: var(--text-secondary); line-height: 1.8;">Placeholder for product description.</p>
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