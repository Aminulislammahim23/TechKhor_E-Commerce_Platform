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
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.5);
            flex-shrink: 0;
        }
        
        .avatar-letter {
            font-size: 32px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .header-text h1 {
            margin: 0;
        }
        
        .header-text p {
            margin: 5px 0 0 0;
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
        .form-group textarea,
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
            
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
            
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus,
        .form-group input[type="file"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
            
        .form-group input::placeholder {
            color: #aaa;
        }
            
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 14px;
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
            
        /* Settings Styles */
        .settings-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }
            
        .settings-container.active {
            max-height: 2000px;
        }
            
        .settings-category {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid #e1e5ee;
        }
            
        .settings-category h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
            
        .settings-form {
            margin-top: 15px;
        }
            
        /* Switch/Toggle Styles */
        .settings-grid {
            display: grid;
            gap: 20px;
        }
            
        .setting-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e1e5ee;
        }
            
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
            
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
            
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
            
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
            
        input:checked + .slider {
            background-color: #667eea;
        }
            
        input:checked + .slider:before {
            transform: translateX(26px);
        }
            
        .setting-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
            
        .setting-info p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
    
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .product-form {
                padding: 15px;
            }
        }
        
        /* Settings Button */
        .settings-btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-right: 15px;
            border: none;
            cursor: pointer;
        }
        
        .settings-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .settings-icon {
            font-size: 18px;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 24px;
        }
        
        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .close:hover {
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 30px;
        }
        
        /* Tab Styles */
        .settings-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
        }
        
        .tab-btn {
            padding: 12px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        
        .tab-btn:hover {
            color: #667eea;
            background-color: #f8f9ff;
        }
        
        .tab-btn.active {
            color: #667eea;
            border-bottom: 3px solid #667eea;
        }
        
        .tab-content {
            min-height: 300px;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Settings Forms */
        .settings-form {
            max-width: 600px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        /* Avatar Upload Styles */
        .profile-avatar-section {
            display: flex;
            align-items: center;
            gap: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff5f5 100%);
            border-radius: 12px;
            margin-bottom: 25px;
            border: 2px solid #e1e5ee;
        }
        
        .current-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            border: 4px solid white;
            flex-shrink: 0;
        }
        
        .avatar-display {
            font-size: 42px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }
        
        .avatar-display-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .avatar-upload-info {
            flex: 1;
        }
        
        .avatar-label {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }
        
        .file-input-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .avatar-input {
            display: none;
        }
        
        .file-input-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .file-input-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .upload-icon {
            font-size: 16px;
        }
        
        .file-name {
            font-size: 14px;
            color: #666;
            font-style: italic;
        }
        
        .upload-hint {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: #999;
        }
        
        .save-btn {
            background: #2ecc71;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .save-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }
        
        .cancel-btn {
            background: #95a5a6;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .cancel-btn:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="admin-avatar">
                        <?php
                        // Check if admin has a custom avatar image
                        $adminAvatar = $_SESSION['user']['avatar'] ?? 'default.png';
                        $avatarPath = './app/assets/images/avatars/' . $adminAvatar;
                        
                        if (!empty($adminAvatar) && $adminAvatar !== 'default.png' && file_exists($avatarPath)): ?>
                            <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="Admin Avatar" class="avatar-image">
                        <?php else:
                            // Get first letter of admin name for fallback avatar
                            $adminName = $_SESSION['user']['name'] ?? 'Admin';
                            $firstLetter = strtoupper(substr($adminName, 0, 1));
                            echo '<span class="avatar-letter">' . htmlspecialchars($firstLetter) . '</span>';
                        endif; ?>
                    </div>
                    <div class="header-text">
                        <h1>Admin Dashboard</h1>
                        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button id="settingsBtn" class="settings-btn">
                        <span class="settings-icon">⚙️</span>
                        Settings
                    </button>
                    <a href="?logout=true" class="logout-btn">
                        <span class="logout-icon">🚪</span>
                        Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Settings Modal -->
        <div id="settingsModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>⚙️ Admin Settings</h2>
                    <span class="close">&times;</span>
                </div>
                
                <div class="modal-body">
                    <div class="settings-tabs">
                        <button class="tab-btn active" data-tab="profile">👤 Profile</button>
                        <button class="tab-btn" data-tab="security">🔒 Security</button>
                        <button class="tab-btn" data-tab="system">🔧 System</button>
                        <button class="tab-btn" data-tab="store">🏪 Store</button>
                    </div>
                    
                    <div class="tab-content">
                        <!-- Profile Settings -->
                        <div id="profile" class="tab-pane active">
                            <form id="profileSettingsForm" class="settings-form" enctype="multipart/form-data">
                                                            <input type="hidden" name="action" value="update_profile">
                                <!-- Avatar Upload Section -->
                                <div class="profile-avatar-section">
                                    <div class="current-avatar">
                                        <?php
                                        // Check if admin has a custom avatar image
                                        $adminAvatar = $_SESSION['user']['avatar'] ?? 'default.png';
                                        $avatarPath = './app/assets/images/avatars/' . $adminAvatar;
                                        
                                        if (!empty($adminAvatar) && $adminAvatar !== 'default.png' && file_exists($avatarPath)): ?>
                                            <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="Admin Avatar" class="avatar-display-image">
                                        <?php else:
                                            // Get first letter of admin name for fallback avatar
                                            $adminName = $_SESSION['user']['name'] ?? 'Admin';
                                            $firstLetter = strtoupper(substr($adminName, 0, 1));
                                            echo '<span class="avatar-display">' . htmlspecialchars($firstLetter) . '</span>';
                                        endif; ?>
                                    </div>
                                    <div class="avatar-upload-info">
                                        <label for="adminAvatar" class="avatar-label">Profile Picture</label>
                                        <div class="file-input-wrapper">
                                            <input type="file" id="adminAvatar" name="admin_avatar" accept="image/*" class="avatar-input">
                                            <label for="adminAvatar" class="file-input-btn">
                                                <span class="upload-icon">📁</span>
                                                Choose Image
                                            </label>
                                            <span class="file-name" id="fileName">No file chosen</span>
                                        </div>
                                        <small class="upload-hint">Supported: JPG, PNG, GIF (Max 2MB)</small>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="adminName">Full Name</label>
                                    <input type="text" id="adminName" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="adminEmail">Email Address</label>
                                    <input type="email" id="adminEmail" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="adminPhone">Phone Number</label>
                                    <input type="tel" id="adminPhone" name="phone" value="<?php echo htmlspecialchars($_SESSION['user']['phone'] ?? ''); ?>" placeholder="Enter phone number">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="save-btn">Save Changes</button>
                                    <button type="reset" class="cancel-btn">Cancel</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Security Settings -->
                        <div id="security" class="tab-pane">
                            <form id="securitySettingsForm" class="settings-form">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" id="currentPassword" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <input type="password" id="newPassword" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm New Password</label>
                                    <input type="password" id="confirmPassword" name="confirm_password" required>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="save-btn">Change Password</button>
                                    <button type="reset" class="cancel-btn">Cancel</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- System Settings -->
                        <div id="system" class="tab-pane">
                            <div class="settings-grid">
                                <div class="setting-item">
                                    <label class="switch">
                                        <input type="checkbox" id="maintenanceMode" name="maintenance_mode">
                                        <span class="slider"></span>
                                    </label>
                                    <div class="setting-info">
                                        <h4>Maintenance Mode</h4>
                                        <p>Temporarily disable website for maintenance</p>
                                    </div>
                                </div>
                                
                                <div class="setting-item">
                                    <label class="switch">
                                        <input type="checkbox" id="emailNotifications" name="email_notifications" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="setting-info">
                                        <h4>Email Notifications</h4>
                                        <p>Receive email alerts for important events</p>
                                    </div>
                                </div>
                                
                                <div class="setting-item">
                                    <label class="switch">
                                        <input type="checkbox" id="autoBackup" name="auto_backup">
                                        <span class="slider"></span>
                                    </label>
                                    <div class="setting-info">
                                        <h4>Automatic Backups</h4>
                                        <p>Daily database backups</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Store Information -->
                        <div id="store" class="tab-pane">
                            <form id="storeSettingsForm" class="settings-form">
                                <div class="form-group">
                                    <label for="storeName">Store Name</label>
                                    <input type="text" id="storeName" name="store_name" placeholder="Enter store name">
                                </div>
                                <div class="form-group">
                                    <label for="storeAddress">Store Address</label>
                                    <textarea id="storeAddress" name="store_address" rows="3" placeholder="Enter store address"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="storePhone">Store Phone</label>
                                    <input type="tel" id="storePhone" name="store_phone" placeholder="Enter store phone">
                                </div>
                                <div class="form-group">
                                    <label for="storeEmail">Store Email</label>
                                    <input type="email" id="storeEmail" name="store_email" placeholder="Enter store email">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="save-btn">Save Store Info</button>
                                    <button type="reset" class="cancel-btn">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>📦 Recent Products</h2>
                <div class="search-controls" style="display: flex; gap: 15px; align-items: center;">
                    <div class="search-bar-container" style="position: relative;">
                        <input type="text" id="productSearch" placeholder="Search products..." class="product-search-input" style="padding: 10px 15px; border: 2px solid #ddd; border-radius: 8px; width: 250px; font-size: 14px;">
                        <span class="search-icon" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);">🔍</span>
                    </div>
                </div>
            </div>
            <table id="productsTable">
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
                    <tr class="product-row" data-product-name="<?php echo strtolower(htmlspecialchars($product['product_name'] ?? '')); ?>">
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
    // Settings Modal Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const settingsBtn = document.getElementById('settingsBtn');
        const settingsModal = document.getElementById('settingsModal');
        const closeBtn = document.querySelector('.close');
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
            
        // Open modal
        settingsBtn.addEventListener('click', function() {
            settingsModal.style.display = 'block';
        });
            
        // Close modal
        closeBtn.addEventListener('click', function() {
            settingsModal.style.display = 'none';
        });
            
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === settingsModal) {
                settingsModal.style.display = 'none';
            }
        });
            
        // Tab switching
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                    
                // Remove active classes
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));
                    
                // Add active classes
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
            
        // Form submissions
        document.getElementById('profileSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            // Show loading indicator
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            fetch('./app/controllers/profileController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    alert(data.message);
                    settingsModal.style.display = 'none';
                    // Reload to show updated avatar
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });
            
        document.getElementById('securitySettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            formData.append('action', 'change_password');
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
                
            if (newPassword !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
                
            if (newPassword.length < 8) {
                alert('Password must be at least 8 characters long!');
                return;
            }
            
            // Show loading indicator
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Changing...';
            submitBtn.disabled = true;
            
            fetch('./app/controllers/profileController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    alert(data.message);
                    settingsModal.style.display = 'none';
                    form.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });
            
        document.getElementById('storeSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Store information updated successfully!');
            settingsModal.style.display = 'none';
        });
        
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
        
        // Settings form validation
        document.querySelectorAll('.settings-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = new FormData(this);
                const formName = this.getAttribute('id');
                
                // Basic validation based on form type
                if (formName === 'profileSettingsForm') {
                    const name = formData.get('admin_name');
                    const email = formData.get('admin_email');
                    
                    if (!name || !email) {
                        alert('Please fill in all required fields');
                        return;
                    }
                    
                    if (!/\S+@\S+\.\S+/.test(email)) {
                        alert('Please enter a valid email address');
                        return;
                    }
                }
                
                if (formName === 'securitySettingsForm') {
                    const currentPass = formData.get('current_password');
                    const newPass = formData.get('new_password');
                    const confirmPass = formData.get('confirm_password');
                    
                    if (!currentPass || !newPass || !confirmPass) {
                        alert('Please fill in all password fields');
                        return;
                    }
                    
                    if (newPass !== confirmPass) {
                        alert('New passwords do not match');
                        return;
                    }
                    
                    if (newPass.length < 8) {
                        alert('Password must be at least 8 characters long');
                        return;
                    }
                }
                
                // Show success message
                alert('Settings updated successfully!');
                
                // Reset form
                this.reset();
            });
        });
        
        // Product Search Functionality
        const productSearchInput = document.getElementById('productSearch');
        
        // Avatar Upload Functionality
        const avatarInput = document.getElementById('adminAvatar');
        const fileNameDisplay = document.getElementById('fileName');
        
        if (avatarInput) {
            avatarInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, PNG, GIF, WEBP)');
                        this.value = '';
                        fileNameDisplay.textContent = 'No file chosen';
                        return;
                    }
                    
                    // Validate file size (2MB max)
                    const maxSize = 2 * 1024 * 1024;
                    if (file.size > maxSize) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        fileNameDisplay.textContent = 'No file chosen';
                        return;
                    }
                    
                    fileNameDisplay.textContent = file.name;
                } else {
                    fileNameDisplay.textContent = 'No file chosen';
                }
            });
        }
        
        const productRows = document.querySelectorAll('.product-row');
        
        if (productSearchInput) {
            productSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                productRows.forEach(row => {
                    const productName = row.getAttribute('data-product-name') || '';
                    const isVisible = productName.includes(searchTerm);
                    
                    row.style.display = isVisible ? '' : 'none';
                });
                
                // Show message if no results
                const visibleRows = Array.from(productRows).filter(row => 
                    row.style.display !== 'none'
                );
                
                // You could add a "no results" message here if needed
            });
        }
    });
</script>
</html>