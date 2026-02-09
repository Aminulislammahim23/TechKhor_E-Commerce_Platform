document.addEventListener('DOMContentLoaded', function() {
    // Handle main dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        
        // Add click event for mobile toggle
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    });
    
    // Handle nested dropdowns
    const hasSubmenus = document.querySelectorAll('.has-submenu');
    
    hasSubmenus.forEach(item => {
        const parentLink = item.querySelector('.submenu-parent');
        const nestedDropdown = item.querySelector('.nested-dropdown');
        
        // Prevent default on parent link for mobile
        parentLink.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                nestedDropdown.style.display = nestedDropdown.style.display === 'block' ? 'none' : 'block';
            }
        });
    });
    
    // Set active state based on current URL
    setActiveNavigation();
});

function setActiveNavigation() {
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.category-nav a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentUrl.includes(href)) {
            link.classList.add('active');
            
            // Also activate parent dropdown if this is a submenu item
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown) {
                parentDropdown.querySelector('.dropdown-toggle').classList.add('active');
            }
        }
    });
}

// Handle mobile menu toggle
function toggleMobileMenu() {
    const categoryNav = document.querySelector('.category-nav');
    categoryNav.classList.toggle('mobile-active');
}