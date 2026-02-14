<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechKhor - Total Solution</title>
    <link rel="stylesheet" href="./app/assets/css/header.css">
    <link rel="stylesheet" href="./app/assets/css/index.css">
    <link rel="stylesheet" href="./app/assets/css/auth.css">
    <link rel="stylesheet" href="./app/assets/css/footer.css">
    <script src="./app/assets/js/header.js"></script>
</head>
<body>
<header>
    <div class="top-header">
        <div class="container">
            <div class="logo">
                <a href="?page=home" class="logo-link">
                    <div class="logo-text">
                        <div class="main-logo">TECHKHOR</div>
                        <div class="sub-logo">TOTAL SOLUTION</div>
                    </div>
                </a>
            </div>
            
            <div class="search-bar">
                <form action="/search.php" method="get">
                    <input type="text" name="q" placeholder="Search">
                    <button type="submit">
                        <span>🔍</span>
                    </button>
                </form>
            </div>

            <div class="top-nav">
                <a href="#" class="nav-item">
                    <span>🎁</span>
                    <div>
                        <p class="title">Offers</p>
                        <p class="subtitle">Latest Offers</p>
                    </div>
                </a>
                <a href="#" class="nav-item">
                    <span>⚡</span>
                    <div>
                        <p class="title">Happy Hour</p>
                        <p class="subtitle">Special Deals</p>
                    </div>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="?logout=true" class="nav-item" id="accountDropdown">
                        <span>👤</span>
                        <div>
                            <p class="title"><?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'User'); ?></p>
                            <p class="subtitle">Logout</p>
                        </div>
                    </a>
                <?php else: ?>
                    <a href="?page=login" class="nav-item" id="accountDropdown">
                        <span>👤</span>
                        <div>
                            <p class="title">Account</p>
                            <p class="subtitle">Register or Login</p>
                        </div>
                    </a>
                <?php endif; ?>
                <a href="?page=builder" class="pc-builder-btn">PC Builder</a>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <a href="?page=admin" class="admin-panel-btn">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </nav>
    <nav class="category-nav">
        <div class="container">
            <ul>
                <li><a href="?category=desktop">Desktop</a></li>
                <li><a href="?category=laptop">Laptop</a></li>
                <li class="dropdown">
                    <a href="?category=component" class="dropdown-toggle">Component ▼</a>
                    <div class="dropdown-menu">
                        <div class="submenu-column">
                            <h3>Component Categories</h3>
                            <ul>
                                <li class="has-submenu">
                                    <a href="?category=component&subcategory=processor" class="submenu-item submenu-parent">Processor ▶</a>
                                    <div class="nested-dropdown">
                                        <a href="?category=component&subcategory=processor&brand=amd" class="nested-item">AMD</a>
                                        <a href="?category=component&subcategory=processor&brand=intel" class="nested-item">Intel</a>
                                    </div>
                                </li>
                                <li><a href="?category=component&subcategory=cpu-cooler" class="submenu-item">CPU Cooler</a></li>
                                <li><a href="?category=component&subcategory=water-cooling" class="submenu-item">Water / Liquid Cooling</a></li>
                                <li><a href="?category=component&subcategory=motherboard" class="submenu-item">Motherboard</a></li>
                                <li><a href="?category=component&subcategory=graphics-card" class="submenu-item">Graphics Card</a></li>
                                <li><a href="?category=component&subcategory=ram-desktop" class="submenu-item">RAM (Desktop)</a></li>
                                <li><a href="?category=component&subcategory=ram-laptop" class="submenu-item">RAM (Laptop)</a></li>
                                <li><a href="?category=component&subcategory=power-supply" class="submenu-item">Power Supply</a></li>
                                <li><a href="?category=component&subcategory=hdd" class="submenu-item">Hard Disk Drive</a></li>
                                <li><a href="?category=component&subcategory=portable-hdd" class="submenu-item">Portable Hard Disk Drive</a></li>
                                <li><a href="?category=component&subcategory=ssd" class="submenu-item">SSD</a></li>
                                <li><a href="?category=component&subcategory=portable-ssd" class="submenu-item">Portable SSD</a></li>
                                <li><a href="?category=component&subcategory=casing" class="submenu-item">Casing</a></li>
                                <li><a href="?category=component&subcategory=casing-cooler" class="submenu-item">Casing Cooler</a></li>
                                <li><a href="?category=component&subcategory=optical-drive" class="submenu-item">Optical Disk Drive</a></li>
                                <li><a href="?category=component&subcategory=vertical-gpu-holder" class="submenu-item">Vertical Graphics Card Holder</a></li>
                            </ul>
                            <a href="?category=component" class="show-all">Show All Component</a>
                        </div>
                    </div>
                </li>
                <li><a href="?category=monitor">Monitor</a></li>
                <li><a href="?category=power">Power</a></li>
                <li><a href="?category=phone">Phone</a></li>
                <li><a href="?category=tablet">Tablet</a></li>
                <li><a href="?category=office-equipment">Office Equipment</a></li>
                <li><a href="?category=camera">Camera</a></li>
                <li><a href="?category=security">Security</a></li>
                <li><a href="?category=networking">Networking</a></li>
                <li><a href="?category=software">Software</a></li>
                <li><a href="?category=server-storage">Server & Storage</a></li>
                <li><a href="?category=accessories">Accessories</a></li>
                <li><a href="?category=gadget">Gadget</a></li>
                <li><a href="?category=gaming">Gaming</a></li>
                <li><a href="?category=tv">TV</a></li>
                <li><a href="?category=appliance">Appliance</a></li>
            </ul>
        </div>
    </nav>
</header>