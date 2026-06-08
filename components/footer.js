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
                        <li><a href="/products.html">Shop</a></li>
                        <li><a href="/about.html">About Us</a></li>
                        <li><a href="/contact.html">Contact</a></li>
                        <li><a href="/faq.html">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Customer Service</h4>
                    <ul class="footer-links">
                        <li><a href="/privacy.html">Privacy Policy</a></li>
                        <li><a href="/terms.html">Terms & Conditions</a></li>
                        <li><a href="/cart.html">Cart</a></li>
                        <li><a href="/wishlist.html">Wishlist</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>My Account</h4>
                    <ul class="footer-links">
                        <li><a href="/login.html">Login</a></li>
                        <li><a href="/register.html">Register</a></li>
                        <li><a href="/user/dashboard.html">Dashboard</a></li>
                        <li><a href="/user/orders.html">Orders</a></li>
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
