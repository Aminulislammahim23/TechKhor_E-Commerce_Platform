<?php
  session_start();
  
  
  if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy();
    header('Location: ?page=home');
    exit();
  }
  
  
  require_once './app/controllers/pageController.php';
  require_once './app/models/db.php';
  
  
  $connection = getConnection();
  $controller = new PageController($connection);
  
  
  $controller->handleRequest();
  
  
?>
<?php require_once './app/views/layouts/header.php'; ?>

<main>
   
    <section class="hero-banner">
        <div class="banner-slider">
            <div class="slide active">
                <div class="slide-content">
                    <div class="slide-text">
                        <h1>PREMIUM GAMING GEARS</h1>
                        <p>Up to 50% OFF on Latest Gaming Accessories</p>
                        <a href="/products/gaming" class="btn-primary">SHOP NOW</a>
                    </div>
                    <div class="slide-image">
                        <img src="https://placehold.co/600x400/FF6B35/white?text=GAMING+GEAR" alt="Gaming Gear">
                    </div>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-text">
                        <h1>LATEST SMARTPHONES</h1>
                        <p>Flagship Phones at Unbeatable Prices</p>
                        <a href="/products/phone" class="btn-primary">EXPLORE PHONES</a>
                    </div>
                    <div class="slide-image">
                        <img src="https://placehold.co/600x400/4A90E2/white?text=SMARTPHONES" alt="Smartphones">
                    </div>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-text">
                        <h1>BUILD YOUR DREAM PC</h1>
                        <p>Customize Your Perfect Gaming Setup</p>
                        <a href="/builder.php" class="btn-primary">PC BUILDER</a>
                    </div>
                    <div class="slide-image">
                        <img src="https://placehold.co/600x400/2ECC71/white?text=PC+BUILDER" alt="PC Builder">
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-dots">
            <span class="dot active" data-slide="0"></span>
            <span class="dot" data-slide="1"></span>
            <span class="dot" data-slide="2"></span>
        </div>
        <button class="slider-nav prev">‹</button>
        <button class="slider-nav next">›</button>
    </section>

   
    <section class="featured-categories">
        <div class="container">
            <h2 class="section-title">SHOP BY CATEGORY</h2>
            <div class="categories-grid">
                <a href="/products/desktop" class="category-card">
                    <div class="category-icon">🖥️</div>
                    <h3>Desktop PCs</h3>
                    <p>Premium Gaming & Workstation PCs</p>
                </a>
                <a href="/products/laptop" class="category-card">
                    <div class="category-icon">💻</div>
                    <h3>Laptops</h3>
                    <p>Ultrabooks, Gaming & Business Laptops</p>
                </a>
                <a href="/products/component" class="category-card">
                    <div class="category-icon">⚙️</div>
                    <h3>Components</h3>
                    <p>RAM, SSD, GPU & More</p>
                </a>
                <a href="/products/phone" class="category-card">
                    <div class="category-icon">📱</div>
                    <h3>Smartphones</h3>
                    <p>Latest Flagship Phones</p>
                </a>
                <a href="/products/gaming" class="category-card">
                    <div class="category-icon">🎮</div>
                    <h3>Gaming</h3>
                    <p>Gaming Accessories & Consoles</p>
                </a>
                <a href="/products/accessories" class="category-card">
                    <div class="category-icon">🎧</div>
                    <h3>Accessories</h3>
                    <p>Headphones, Keyboards & More</p>
                </a>
            </div>
        </div>
    </section>

    
    <section class="hot-deals">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">🔥 HOT DEALS</h2>
                <a href="/deals" class="view-all">View All Deals →</a>
            </div>
            <div class="deals-timer">
                <div class="timer-item">
                    <span class="time-number" id="hours">12</span>
                    <span class="time-label">HOURS</span>
                </div>
                <div class="timer-separator">:</div>
                <div class="timer-item">
                    <span class="time-number" id="minutes">45</span>
                    <span class="time-label">MINUTES</span>
                </div>
                <div class="timer-separator">:</div>
                <div class="timer-item">
                    <span class="time-number" id="seconds">30</span>
                    <span class="time-label">SECONDS</span>
                </div>
            </div>
            <div class="products-grid">
                <div class="product-card">
                    <div class="product-badge">SALE</div>
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/FF6B35/white?text=PRODUCT" alt="Product">
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn">♡</button>
                            <button class="action-btn cart-btn">🛒</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Gaming Keyboard RGB</h3>
                        <div class="product-price">
                            <span class="current-price">৳ 2,499</span>
                            <span class="original-price">৳ 3,500</span>
                        </div>
                        <div class="product-rating">
                            <div class="stars">★★★★★</div>
                            <span class="rating-count">(128)</span>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-badge hot">HOT</div>
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/4A90E2/white?text=PRODUCT" alt="Product">
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn">♡</button>
                            <button class="action-btn cart-btn">🛒</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Wireless Mouse Pro</h3>
                        <div class="product-price">
                            <span class="current-price">৳ 1,299</span>
                            <span class="original-price">৳ 1,800</span>
                        </div>
                        <div class="product-rating">
                            <div class="stars">★★★★☆</div>
                            <span class="rating-count">(89)</span>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-badge new">NEW</div>
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/2ECC71/white?text=PRODUCT" alt="Product">
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn">♡</button>
                            <button class="action-btn cart-btn">🛒</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Mechanical Keyboard</h3>
                        <div class="product-price">
                            <span class="current-price">৳ 3,999</span>
                            <span class="original-price">৳ 5,200</span>
                        </div>
                        <div class="product-rating">
                            <div class="stars">★★★★★</div>
                            <span class="rating-count">(256)</span>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-badge">SALE</div>
                    <div class="product-image">
                        <img src="https://placehold.co/300x300/F39C12/white?text=PRODUCT" alt="Product">
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn">♡</button>
                            <button class="action-btn cart-btn">🛒</button>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Gaming Headset 7.1</h3>
                        <div class="product-price">
                            <span class="current-price">৳ 2,199</span>
                            <span class="original-price">৳ 2,999</span>
                        </div>
                        <div class="product-rating">
                            <div class="stars">★★★★☆</div>
                            <span class="rating-count">(142)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="services-section">
        <div class="container">
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">🚚</div>
                    <h3>Free Delivery</h3>
                    <p>On orders over ৳ 5,000</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">↩️</div>
                    <h3>Easy Returns</h3>
                    <p>30-day return policy</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🛡️</div>
                    <h3>Secure Payment</h3>
                    <p>Multiple payment options</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">💬</div>
                    <h3>24/7 Support</h3>
                    <p>Dedicated customer service</p>
                </div>
            </div>
        </div>
    </section>

  
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>STAY UPDATED WITH TECHKHOR</h2>
                <p>Subscribe to our newsletter for the latest deals and product updates</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit" class="btn-secondary">SUBSCRIBE</button>
                </form>
            </div>
        </div>
    </section>
</main>

    <?php
        require_once './app/views/layouts/footer.php';
    ?>
    
    <script src="./app/assets/js/index.js"></script>
    <script src="./app/assets/js/auth.js"></script>
</body>
</html>