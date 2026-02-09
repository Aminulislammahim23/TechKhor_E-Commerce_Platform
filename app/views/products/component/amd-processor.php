<?php 
require_once __DIR__ . '/../../layouts/header.php'; 
?>

<main class="product-listing-container">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-container">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="?page=home">🏠 Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="?category=component">Component</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="?category=component&subcategory=processor">Processor</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">AMD</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="product-listing-layout">
            <!-- Sidebar Filters -->
            <aside class="filters-sidebar">
                <div class="filter-section">
                    <h3 class="filter-title">Price Range</h3>
                    <div class="price-slider-container">
                        <div class="price-slider">
                            <div class="slider-track"></div>
                            <div class="slider-range" style="left: 0%; right: 0%;"></div>
                            <div class="slider-handle" style="left: 0%;"></div>
                            <div class="slider-handle" style="left: 100%;"></div>
                        </div>
                        <div class="price-inputs">
                            <input type="number" class="price-input" value="0" min="0" max="86500">
                            <span>-</span>
                            <input type="number" class="price-input" value="86500" min="0" max="86500">
                        </div>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title expandable">Availability</h3>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" name="availability" value="in-stock">
                            <span class="checkmark"></span>
                            In Stock
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="availability" value="pre-order">
                            <span class="checkmark"></span>
                            Pre Order
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="availability" value="upcoming">
                            <span class="checkmark"></span>
                            Up Coming
                        </label>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title expandable">Series</h3>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="1000">
                            <span class="checkmark"></span>
                            1000 Series
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="2000">
                            <span class="checkmark"></span>
                            2000 Series
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="3000">
                            <span class="checkmark"></span>
                            3000 Series
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="4000">
                            <span class="checkmark"></span>
                            4000 Series
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="5000">
                            <span class="checkmark"></span>
                            5000 Series
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="7000">
                            <span class="checkmark"></span>
                            7000 Series
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="8000">
                            <span class="checkmark"></span>
                            8000 Series
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="series" value="9000">
                            <span class="checkmark"></span>
                            9000 Series
                        </label>
                    </div>
                </div>
            </aside>

            <!-- Main Product Content -->
            <div class="products-main">
                <div class="products-header">
                    <h1 class="products-title">AMD</h1>
                    <div class="products-controls">
                        <div class="control-group">
                            <label>Show:</label>
                            <select class="control-select">
                                <option>20</option>
                                <option>40</option>
                                <option>60</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <label>Sort By:</label>
                            <select class="control-select">
                                <option>Default</option>
                                <option>Price: Low to High</option>
                                <option>Price: High to Low</option>
                                <option>Name: A to Z</option>
                                <option>Name: Z to A</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="products-grid">
                    <!-- Product 1: AMD Athlon PRO 300GE -->
                    <div class="product-card">
                        <div class="product-badge savings">Save: 950৳</div>
                        <div class="product-image">
                            <img src="https://placehold.co/300x300/333/white?text=ATHLON" alt="AMD Athlon PRO 300GE">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">AMD Athlon PRO 300GE AM4 Socket Desktop Processor with Radeon Vega 3 Graphics (Rebox)</h3>
                            <ul class="product-specs">
                                <li>Base Clock Speed: 3.4GHz</li>
                                <li>Package: AM4</li>
                                <li>PCI Express PCIe 3.0</li>
                            </ul>
                            <div class="product-price">
                                <span class="current-price">4,250৳</span>
                                <span class="original-price">5,200৳</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn-buy">🛒 Buy Now</button>
                                <button class="btn-compare">⇄ Add to Compare</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2: AMD Ryzen 3 2200G -->
                    <div class="product-card">
                        <div class="product-badge savings">Save: 510৳</div>
                        <div class="product-image">
                            <img src="https://placehold.co/300x300/ED1C24/white?text=RYZEN+3" alt="AMD Ryzen 3 2200G">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">AMD Ryzen 3 2200G Quad-Core Processor With Radeon Vega 8 Graphics</h3>
                            <ul class="product-specs">
                                <li>Speed: 3.5GHz Up to 3.7GHz</li>
                                <li>Cache: L1 384KB, L2 2MB L3 4MB</li>
                                <li>Cores-4 & Threads-4</li>
                                <li>Powerful Radeon Vega graphics</li>
                            </ul>
                            <div class="product-price">
                                <span class="current-price">4,990৳</span>
                                <span class="original-price">5,500৳</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn-buy">🛒 Buy Now</button>
                                <button class="btn-compare">⇄ Add to Compare</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3: AMD Ryzen 5 2400G -->
                    <div class="product-card">
                        <div class="product-badge savings">Save: 600৳</div>
                        <div class="product-image">
                            <img src="https://placehold.co/300x300/ED1C24/white?text=RYZEN+5" alt="AMD Ryzen 5 2400G">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">AMD Ryzen 5 2400G Desktop Processor with Radeon RX Vega 11 Graphics</h3>
                            <ul class="product-specs">
                                <li>Speed: 3.6GHz Up To 3.9GHz</li>
                                <li>Cache: L1 384KB L2 2MB L3 4MB</li>
                                <li>Cores-4 & Threads-8</li>
                                <li>Powerful Radeon Vega graphics</li>
                            </ul>
                            <div class="product-price">
                                <span class="current-price">6,600৳</span>
                                <span class="original-price">7,200৳</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn-buy">🛒 Buy Now</button>
                                <button class="btn-compare">⇄ Add to Compare</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 4: AMD Ryzen 3 3200G -->
                    <div class="product-card">
                        <div class="product-badge savings">Save: 1,400৳</div>
                        <div class="product-image">
                            <img src="https://placehold.co/300x300/ED1C24/white?text=RYZEN+3" alt="AMD Ryzen 3 3200G">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">AMD Ryzen 3 3200G Processor with Radeon RX Vega 8 Graphics</h3>
                            <ul class="product-specs">
                                <li>Speed: 3.6 GHz up to 4.0 GHz</li>
                                <li>Cores-4 & Threads-4</li>
                                <li>Memory Speed: Up to 2933MHz</li>
                                <li>Radeon Vega 8 Graphics</li>
                            </ul>
                            <div class="product-price">
                                <span class="current-price">6,400৳</span>
                                <span class="original-price">7,800৳</span>
                            </div>
                            <div class="product-actions">
                                <button class="btn-buy">🛒 Buy Now</button>
                                <button class="btn-compare">⇄ Add to Compare</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Floating Action Buttons -->
<div class="floating-actions">
    <div class="action-button compare-btn">
        <div class="button-icon">⇄</div>
        <div class="button-label">COMPARE</div>
        <div class="notification-badge">0</div>
    </div>
    <div class="action-button cart-btn">
        <div class="button-icon">🛒</div>
        <div class="button-label">CART</div>
        <div class="notification-badge">0</div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>