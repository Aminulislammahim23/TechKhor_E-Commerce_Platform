<?php
class ProductController {
    private $connection;
    private $uploadDir;
    
    public function __construct($connection) {
        $this->connection = $connection;
        $this->uploadDir = dirname(__DIR__) . '/assets/images/products/';
    }
    
    /**
     * Handle product creation with image upload
     * @return array Success status and message
     */
    public function createProduct() {
        $errors = [];
        $success = false;
        $message = '';
        
        // Validate required fields
        $productName = trim($_POST['product_name'] ?? '');
        $price = $_POST['price'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $categoryId = $_POST['category_id'] ?? null;
        $brandId = $_POST['brand_id'] ?? null;
        $status = $_POST['status'] ?? 1;
        
        // Required field validation
        if (empty($productName)) {
            $errors[] = 'Product name is required';
        }
        
        if (empty($price) || !is_numeric($price) || $price <= 0) {
            $errors[] = 'Valid price is required';
        }
        
        if (empty($stock) || !is_numeric($stock) || $stock < 0) {
            $errors[] = 'Valid stock quantity is required';
        }
        
        // Handle image upload if provided
        $imagePath = null;
        if (isset($_FILES['p_image']) && $_FILES['p_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['p_image']);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            } else {
                $errors = array_merge($errors, $uploadResult['errors']);
            }
        }
        
        // If no errors, insert into database
        if (empty($errors)) {
            $stmt = $this->connection->prepare("INSERT INTO products (category_id, brand_id, product_name, price, stock, status, p_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissdis", $categoryId, $brandId, $productName, $price, $stock, $status, $imagePath);
            
            if ($stmt->execute()) {
                $success = true;
                $message = 'Product added successfully!';
                // Log the action
                error_log("Product created: {$productName} (ID: {$stmt->insert_id})");
            } else {
                $errors[] = 'Database error: ' . $stmt->error;
            }
            $stmt->close();
        }
        
        return [
            'success' => $success,
            'errors' => $errors,
            'message' => $message
        ];
    }
    
    /**
     * Handle image file upload
     * @param array $file $_FILES array for the image
     * @return array Upload result with path or errors
     */
    private function handleImageUpload($file) {
        $errors = [];
        $uploadPath = null;
        
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Invalid file type. Please upload JPG, PNG, GIF, or WEBP images only.';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds 5MB limit.';
            return ['success' => false, 'errors' => $errors];
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueFilename = uniqid('product_') . '_' . time() . '.' . $fileExtension;
        $destination = $this->uploadDir . $uniqueFilename;
        
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                $errors[] = 'Failed to create upload directory.';
                return ['success' => false, 'errors' => $errors];
            }
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Store relative path in database
            $uploadPath = 'app/assets/images/products/' . $uniqueFilename;
            return ['success' => true, 'path' => $uploadPath];
        } else {
            $errors[] = 'Failed to move uploaded file.';
            return ['success' => false, 'errors' => $errors];
        }
    }
    
    /**
     * Get all products with image paths
     * @param int $limit Number of products to fetch
     * @return array Products data
     */
    public function getRecentProducts($limit = 5) {
        $products = [];
        $stmt = $this->connection->prepare("
            SELECT p.product_id, p.product_name, p.price, p.stock, p.p_image, b.brand_name 
            FROM products p 
            LEFT JOIN brands b ON p.brand_id = b.brand_id 
            ORDER BY p.product_id DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        $stmt->close();
        return $products;
    }
    
    /**
     * Get base URL for image paths
     * @return string Base URL
     */
    public function getImageBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
        return $protocol . '://' . $host . $basePath . '/';
    }
}
?>