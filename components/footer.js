const footerHTML = `
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>LuxuryStore</h4>
                    <p style="color: #9ca3af; margin-bottom: 1.5rem;">Premium products for modern living. Quality guaranteed.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/eccommerce/products.php">Shop</a></li>
                        <li><a href="/eccommerce/about.html">About Us</a></li>
                        <li><a href="/eccommerce/contact.html">Contact</a></li>
                        <li><a href="/eccommerce/faq.html">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Customer Service</h4>
                    <ul>
                        <li><a href="/eccommerce/privacy.html">Privacy Policy</a></li>
                        <li><a href="/eccommerce/terms.html">Terms & Conditions</a></li>
                        <li><a href="/eccommerce/cart.html">Cart</a></li>
                        <li><a href="/eccommerce/wishlist.html">Wishlist</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>My Account</h4>
                    <ul>
                        <li><a href="/eccommerce/login.php">Login</a></li>
                        <li><a href="/eccommerce/register.php">Register</a></li>
                        <li><a href="/eccommerce/user/dashboard.php">Dashboard</a></li>
                        <li><a href="/eccommerce/user/orders.php">Orders</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 LuxuryStore. All rights reserved.</p>
            </div>
        </div>
    </footer>
`;

function renderFooter() {
    document.getElementById('footer-container').innerHTML = footerHTML;
}

renderFooter();
