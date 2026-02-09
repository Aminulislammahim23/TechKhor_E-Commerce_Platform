<?php
class PageController {
    private $allowedPages = ['home', 'login', 'register', 'builder'];
    private $dbConnection;
    
    public function __construct($dbConnection = null) {
        $this->dbConnection = $dbConnection;
    }
    
    
    public function handleRequest() {
        $page = $_GET['page'] ?? 'home';
        
        
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
        
        if (isset($_SESSION['user_id'])) {
            $this->redirectToHome();
        }
        
        require_once './app/views/account/login.php';
        exit();
    }
    
    
    private function handleRegisterPage() {
        
        if (isset($_SESSION['user_id'])) {
            $this->redirectToHome();
        }
        
        require_once './app/views/account/register.php';
        exit();
    }
    
    
    private function handleBuilderPage() {
        require_once './app/views/builder/builder.php';
        exit();
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