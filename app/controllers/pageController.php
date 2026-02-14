<?php
class PageController {
    private $allowedPages = ['home', 'login', 'register', 'builder', 'admin'];
    private $dbConnection;
    
    public function __construct($dbConnection = null) {
        $this->dbConnection = $dbConnection;
    }
    
    
    public function handleRequest() {
        $page = $_GET['page'] ?? 'home';
        
        // Handle logout
        if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
            $this->handleLogout();
            return;
        }
        
        if (!$this->isValidPage($page)) {
            $this->show404();
            return;
        }
        
        switch ($page) {
            case 'login':
                $this->handleLoginPage();
                break;
            case 'register':
                $this->handleRegisterPage();
                break;
            case 'builder':
                $this->handleBuilderPage();
                break;
            case 'admin':
                $this->handleAdminPage();
                break;
            case 'home':
            default:
                $this->handleHomePage();
                break;
        }
    }
    
    
    private function isValidPage($page) {
        return in_array($page, $this->allowedPages);
    }
    
    
    private function handleHomePage() {
        
        if ($this->dbConnection && !$this->dbConnection) {
            require_once './app/views/error/500.php';
            exit();
        }
        
        
    }
    
    
    private function handleLoginPage() {
        $errors = [];
        $formData = ['email' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Use the LoginController for handling login
            require_once './app/controllers/authController/loginController.php';
            
            $loginController = new LoginController($this->dbConnection);
            $result = $loginController->handleLogin();
            
            if ($result['success']) {
                $loginController->redirectByRole();
            } else {
                // Login failed, pass errors and form data to view
                $errors = $result['errors'];
                $formData = $result['formData'];
            }
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->redirectToHome();
        }
        
        // Include the login view with any errors or form data
        require_once './app/views/account/login.php';
        exit();
    }
    
    
    private function handleRegisterPage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Use the RegisterController for handling registration
            require_once './app/controllers/authController/registerController.php';
            
            $registerController = new RegisterController($this->dbConnection);
            $result = $registerController->handleRegistration();
            
            if ($result['success']) {
                $registerController->redirectAfterSuccess();
            } else {
                // Registration failed, pass errors and form data to view
                $errors = $result['errors'];
                $formData = $result['formData'];
            }
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->redirectToHome();
        }
        
        // Include the registration view with any errors or form data
        require_once './app/views/account/register.php';
        exit();
    }
    
    
    private function handleBuilderPage() {
        require_once './app/views/builder/builder.php';
        exit();
    }
    
    private function handleAdminPage() {
        // Check if user is logged in and has admin role
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: ?page=login');
            exit();
        }
        
        require_once './app/views/admin/dashboard.php';
        exit();
    }
    
    private function handleLogout() {
        // Use the LoginController for handling logout
        require_once './app/controllers/authController/loginController.php';
        
        $loginController = new LoginController($this->dbConnection);
        $loginController->handleLogout();
    }
    
    private function redirectToHome() {
        header('Location: ?page=home');
        exit();
    }
    
    
    private function show404() {
        http_response_code(404);
        require_once './app/views/error/404.php';
        exit();
    }
    
    
    public function getAllowedPages() {
        return $this->allowedPages;
    }
    
   
    public function isActivePage($page) {
        $currentPage = $_GET['page'] ?? 'home';
        return $currentPage === $page;
    }
    
    public function generateNavigation() {
        $navItems = [
            'home' => ['label' => 'Home', 'url' => '?page=home'],
            'builder' => ['label' => 'PC Builder', 'url' => '?page=builder'],
            'login' => ['label' => 'Login', 'url' => '?page=login'],
            'register' => ['label' => 'Register', 'url' => '?page=register']
        ];
        
        return $navItems;
    }
}

function renderPage($page) {
    $controller = new PageController();
    $_GET['page'] = $page;
    $controller->handleRequest();
}

function isCurrentPage($page) {
    $controller = new PageController();
    return $controller->isActivePage($page);
}
?>