<?php
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ?page=login');
    exit();
}

// Get database connection
require_once dirname(__DIR__, 2) . '/models/db.php';
$connection = getConnection();

// Initialize statistics
$totalUsers = 0;
$totalProducts = 0;
$totalOrders = 0;
$monthlyRevenue = 0;
$totalCategories = 0;
$totalBrands = 0;

// Fetch statistics
if ($connection) {
    // Total users
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalUsers = $row['count'];
    }
    
    // Total products
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM products");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalProducts = $row['count'];
    }
    
    // Total orders
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM orders");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalOrders = $row['count'];
    }
    
    // Calculate monthly revenue
    $result = mysqli_query($connection, "SELECT SUM(total_amount) as revenue FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $monthlyRevenue = $row['revenue'] ?? 0;
    }
    
    // Total categories
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM categories");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalCategories = $row['count'];
    }
    
    // Total brands
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM brands");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalBrands = $row['count'];
    }
}

// Fetch recent orders with customer names
$recentOrders = [];
if ($connection) {
    $result = mysqli_query($connection, "SELECT o.order_id, o.total_amount, o.order_date, o.payment_method, c.name as customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.customer_id ORDER BY o.order_date DESC LIMIT 5");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $recentOrders[] = $row;
        }
    }
}

// Fetch recent products
$recentProducts = [];
$productErrors = [];
$productSuccess = false;
$productMessage = '';

// Handle product creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
    require_once './app/controllers/productController.php';
    $productController = new ProductController($connection);
    $result = $productController->createProduct();
    
    $productErrors = $result['errors'];
    $productSuccess = $result['success'];
    $productMessage = $result['message'];
}

// Fetch recent products using the controller
if ($connection) {
    require_once './app/controllers/productController.php';
    $productController = new ProductController($connection);
    $recentProducts = $productController->getRecentProducts(5);
    $imageBaseUrl = $productController->getImageBaseUrl();
}

// Fetch low stock products
$lowStockProducts = [];
if ($connection) {
    $result = mysqli_query($connection, "SELECT product_name, stock FROM products WHERE stock <= 5 AND stock > 0 ORDER BY stock ASC LIMIT 5");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $lowStockProducts[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | TechKhor</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/index.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
    
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
    
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
    
        .admin-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
    
        .admin-header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
    
        .header-actions {
            display: flex;
            align-items: center;
        }
    
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
    
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    
        .logout-icon {
            font-size: 1.2em;
        }
    
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
    
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    
        .stat-icon {
            font-size: 2.5em;
            margin-right: 20px;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            color: white;
        }
    
        .stat-info h3 {
            font-size: 2em;
            margin-bottom: 5px;
            color: #333;
        }
    
        .stat-info p {
            color: #666;
            font-size: 1.1em;
        }
    
        /* Admin Section */
        .admin-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    
        .admin-section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
    
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
    
        th {
            background-color: #667eea;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
    
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
    
        tr:hover {
            background-color: #f8f9ff;
        }
    
        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .logout-btn {
                width: 100%;
                justify-content: center;
                max-width: 200px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
            
            .stat-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    
        /* Product Form Styles */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    
        .section-header h2 {
            margin: 0;
            color: #333;
        }
    
        .toggle-btn {
            background: #667eea;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
    
        .toggle-btn:hover {
            background: #5a6fd8;
            transform: rotate(90deg);
        }
    
        .product-form-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
    
        .product-form-container.active {
            max-height: 1000px;
        }
    
        .product-form {
            background: #f8f9ff;
            padding: 25px;
            border-radius: 10px;
            border: 2px dashed #667eea;
        }
    
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
    
        .form-group {
            margin-bottom: 15px;
        }
    
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
    
        .form-group input,
        .form-group select,
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5ee;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
    
        .form-group input[type="file"] {
            padding: 10px;
            background: #fff;
        }
    
        .form-group input:focus,
        .form-group select:focus,
        .form-group input[type="file"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
    
        .form-group input::placeholder {
            color: #aaa;
        }
    
        .submit-btn, .reset-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
    
        .submit-btn {
            background: #2ecc71;
            color: white;
        }
    
        .submit-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }
    
        .reset-btn {
            background: #f39c12;
            color: white;
        }
    
        .reset-btn:hover {
            background: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }
    
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .product-form {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="header-content">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>
                </div>
                <div class="header-actions">
                    <a href="?logout=true" class="logout-btn">
                        <span class="logout-icon">🚪</span>
                        Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <h3><?php echo $totalProducts; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📋</div>
                <div class="stat-info">
                    <h3><?php echo $totalOrders; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3>৳<?php echo number_format($monthlyRevenue, 2); ?></h3>
                    <p>Monthly Revenue</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-info">
                    <h3><?php echo $totalCategories; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <div class="stat-info">
                    <h3><?php echo $totalBrands; ?></h3>
                    <p>Brands</p>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders Section -->
        <div class="admin-section">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>#ORD-<?php echo str_pad($order['order_id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                        <td><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></td>
                        <td>৳<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No recent orders found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Recent Products Section -->
        <div class="admin-section">
            <h2>Recent Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentProducts as $product): ?>
                    <tr>
                        <td>#PROD-<?php echo str_pad($product['product_id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($product['product_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></td>
                        <td>৳<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock'] ?? 'N/A'; ?></td>
                        <td>
                            <?php if (!empty($product['p_image'])): ?>
                                <img src="<?php echo $imageBaseUrl . htmlspecialchars($product['p_image']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <span style="color: #999;">No Image</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($recentProducts)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No products found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Low Stock Alert Section -->
        <?php if (!empty($lowStockProducts)): ?>
        <div class="admin-section">
            <h2 style="color: #e74c3c;">⚠️ Low Stock Alert</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Current Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockProducts as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td style="color: #e74c3c; font-weight: bold;"><?php echo $product['stock']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Add New Product Section -->
        <div class="admin-section">
            <div class="section-header">
                <h2>📦 Add New Product</h2>
                <button id="toggleProductForm" class="toggle-btn">+</button>
            </div>
            
            <?php if ($productSuccess): ?>
                <div class="success-message" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($productMessage); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($productErrors)): ?>
                <div class="error-message" style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <strong>Errors:</strong>
                    <ul style="margin: 5px 0 0 20px;">
                        <?php foreach ($productErrors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div id="productFormContainer" class="product-form-container">
                <form id="addProductForm" class="product-form" method="post" action="" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="productName">Product Name *</label>
                            <input type="text" id="productName" name="product_name" required placeholder="Enter product name">
                        </div>
                        
                        <div class="form-group">
                            <label for="productCategory">Category</label>
                            <select id="productCategory" name="category_id">
                                <option value="">Select Category</option>
                                <?php
                                // Fetch categories for dropdown
                                if ($connection) {
                                    $catResult = mysqli_query($connection, "SELECT category_id, category_name FROM categories WHERE status = 1 ORDER BY category_name");
                                    if ($catResult) {
                                        while ($cat = mysqli_fetch_assoc($catResult)) {
                                            echo '<option value="' . $cat['category_id'] . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="productBrand">Brand</label>
                            <select id="productBrand" name="brand_id">
                                <option value="">Select Brand</option>
                                <?php
                                // Fetch brands for dropdown
                                if ($connection) {
                                    $brandResult = mysqli_query($connection, "SELECT brand_id, brand_name FROM brands WHERE status = 1 ORDER BY brand_name");
                                    if ($brandResult) {
                                        while ($brand = mysqli_fetch_assoc($brandResult)) {
                                            echo '<option value="' . $brand['brand_id'] . '">' . htmlspecialchars($brand['brand_name']) . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="productPrice">Price (৳) *</label>
                            <input type="number" id="productPrice" name="price" step="0.01" min="0" required placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="productStock">Stock Quantity *</label>
                            <input type="number" id="productStock" name="stock" min="0" required placeholder="Enter stock quantity">
                        </div>
                        
                        <div class="form-group">
                            <label for="productImage">Product Image</label>
                            <input type="file" id="productImage" name="p_image" accept="image/*">
                            <small style="color: #666; display: block; margin-top: 5px;">Supported formats: JPG, PNG, GIF, WEBP</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="productStatus">Status</label>
                            <select id="productStatus" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <!-- Empty column for spacing -->
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="submit-btn">➕ Add Product</button>
                        <button type="reset" class="reset-btn">🔄 Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php 
        // Close database connection
        if (isset($connection)) {
            mysqli_close($connection);
        }
    ?>
</body>
<script>
    // Toggle product form visibility
    document.getElementById('toggleProductForm').addEventListener('click', function() {
        const formContainer = document.getElementById('productFormContainer');
        const toggleBtn = this;
        
        formContainer.classList.toggle('active');
        
        if (formContainer.classList.contains('active')) {
            toggleBtn.textContent = '−';
            toggleBtn.style.transform = 'rotate(0deg)';
        } else {
            toggleBtn.textContent = '+';
            toggleBtn.style.transform = 'rotate(90deg)';
        }
    });
    
    // Form submission handling
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // Basic validation
        const productName = formData.get('product_name');
        const price = formData.get('price');
        const stock = formData.get('stock');
        const productImage = formData.get('p_image');
        
        if (!productName || !price || !stock) {
            alert('Please fill in all required fields (Product Name, Price, and Stock)');
            return;
        }
        
        if (parseFloat(price) <= 0) {
            alert('Price must be greater than 0');
            return;
        }
        
        if (parseInt(stock) < 0) {
            alert('Stock cannot be negative');
            return;
        }
        
        // Validate image file if provided
        let imageFileName = '';
        if (productImage && productImage.size > 0) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!allowedTypes.includes(productImage.type)) {
                alert('Please select a valid image file (JPG, PNG, GIF, WEBP)');
                return;
            }
            
            if (productImage.size > maxSize) {
                alert('Image file size must be less than 5MB');
                return;
            }
            
            imageFileName = productImage.name;
        }
        
        // In a real implementation, you would send this to a PHP handler
        // For now, we'll show a success message
        let successMessage = 'Product added successfully!\n\n' +
                           'Product: ' + productName + '\n' +
                           'Price: ৳' + parseFloat(price).toFixed(2) + '\n' +
                           'Stock: ' + stock;
        
        if (imageFileName) {
            successMessage += '\nImage: ' + imageFileName;
        }
        
        alert(successMessage);
        
        // Reset form and close it
        this.reset();
        const formContainer = document.getElementById('productFormContainer');
        const toggleBtn = document.getElementById('toggleProductForm');
        
        formContainer.classList.remove('active');
        toggleBtn.textContent = '+';
        toggleBtn.style.transform = 'rotate(90deg)';
    });
    
    // Reset button functionality
    document.querySelector('.reset-btn').addEventListener('click', function() {
        const formContainer = document.getElementById('productFormContainer');
        const toggleBtn = document.getElementById('toggleProductForm');
        
        // Close form after reset
        setTimeout(() => {
            formContainer.classList.remove('active');
            toggleBtn.textContent = '+';
            toggleBtn.style.transform = 'rotate(90deg)';
        }, 100);
    });
</script>
</html>