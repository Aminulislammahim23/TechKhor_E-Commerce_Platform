<?php
  require_once './app/models/db.php';
  
  // Check database connection
  $connection = getConnection();
  
  if (!$connection) {
    // Connection failed, show 500 error
    require_once './app/views/error/500.php';
    exit();
  }
?>
<?php require_once './app/views/layouts/header.php'; ?>

<nav class="category-nav">
        <div class="container">
            <ul>
                <li><a href="product.php?category=desktop">Desktop</a></li>
                <li><a href="product.php?category=laptop">Laptop</a></li>
                <li><a href="product.php?category=component">Component</a></li>
                <li><a href="product.php?category=monitor">Monitor</a></li>
                <li><a href="product.php?category=power">Power</a></li>
                <li><a href="product.php?category=phone">Phone</a></li>
                <li><a href="product.php?category=tablet">Tablet</a></li>
                <li><a href="product.php?category=office">Office Equipment</a></li>
                <li><a href="product.php?category=camera">Camera</a></li>
                <li><a href="product.php?category=security">Security</a></li>
                <li><a href="product.php?category=networking">Networking</a></li>
                <li><a href="product.php?category=software">Software</a></li>
                <li><a href="product.php?category=storage">Server & Storage</a></li>
                <li><a href="product.php?category=accessories">Accessories</a></li>
                <li><a href="product.php?category=gadget">Gadget</a></li>
                <li><a href="product.php?category=gaming">Gaming</a></li>
                <li><a href="product.php?category=tv">TV</a></li>
                <li><a href="product.php?category=appliance">Appliance</a></li>
            </ul>
        </div>
    </nav>


    <main>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
            <strong>✓ Successfully Connected!</strong> Database connection established successfully.
        </div>
        <p>This is the main content of the page.</p>
    </main>

    <?php
        require_once './app/views/layouts/footer.php';
    ?>
    
</body>
</html>