document.addEventListener('DOMContentLoaded', function() {
    initSlider();
    initDealTimer();
    initWishlistButtons();
    initNewsletterForm();
});

function initSlider() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.slider-nav.prev');
    const nextBtn = document.querySelector('.slider-nav.next');
    
    let currentSlide = 0;
    const slideCount = slides.length;
    
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        
        currentSlide = index;
    }
    
    
    function nextSlide() {
        const nextIndex = (currentSlide + 1) % slideCount;
        showSlide(nextIndex);
    }
    
    
    function prevSlide() {
        const prevIndex = (currentSlide - 1 + slideCount) % slideCount;
        showSlide(prevIndex);
    }
    
    
    let sliderInterval = setInterval(nextSlide, 5000);
    
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            clearInterval(sliderInterval);
            nextSlide();
            sliderInterval = setInterval(nextSlide, 5000);
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            clearInterval(sliderInterval);
            prevSlide();
            sliderInterval = setInterval(nextSlide, 5000);
        });
    }
    
   
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            clearInterval(sliderInterval);
            showSlide(index);
            sliderInterval = setInterval(nextSlide, 5000);
        });
    });
    
    
    const heroBanner = document.querySelector('.hero-banner');
    if (heroBanner) {
        heroBanner.addEventListener('mouseenter', () => {
            clearInterval(sliderInterval);
        });
        
        heroBanner.addEventListener('mouseleave', () => {
            sliderInterval = setInterval(nextSlide, 5000);
        });
    }
}

function initDealTimer() {
    const hoursElement = document.getElementById('hours');
    const minutesElement = document.getElementById('minutes');
    const secondsElement = document.getElementById('seconds');
    
    if (!hoursElement || !minutesElement || !secondsElement) return;
    
    function updateTimer() {
        
        let hours = 12;
        let minutes = 45;
        let seconds = 30;
        
       
        hoursElement.textContent = hours.toString().padStart(2, '0');
        minutesElement.textContent = minutes.toString().padStart(2, '0');
        secondsElement.textContent = seconds.toString().padStart(2, '0');
        
        
        seconds--;
        if (seconds < 0) {
            seconds = 59;
            minutes--;
            if (minutes < 0) {
                minutes = 59;
                hours--;
                if (hours < 0) {
                    hours = 23;
                }
            }
        }
    }
    
    
    setInterval(updateTimer, 1000);
    updateTimer(); // Initial call
}


function initWishlistButtons() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            this.classList.toggle('active');
            
            if (this.classList.contains('active')) {
                this.textContent = '♥';
                this.title = 'Remove from wishlist';
                
                showNotification('Added to wishlist!', 'success');
            } else {
                this.textContent = '♡';
                this.title = 'Add to wishlist';
                
                showNotification('Removed from wishlist', 'info');
            }
        });
    });
}


function initCartButtons() {
    const cartButtons = document.querySelectorAll('.cart-btn');
    
    cartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            
            this.style.transform = 'scale(0.9)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            
            showNotification('Product added to cart!', 'success');
            
            
            updateCartBadge();
        });
    });
}


function initNewsletterForm() {
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (email) {
                
                showNotification('Thank you for subscribing!', 'success');
                emailInput.value = '';
            } else {
                showNotification('Please enter a valid email address', 'error');
            }
        });
    }
}


function showNotification(message, type = 'info') {
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '15px 25px',
        borderRadius: '8px',
        color: 'white',
        fontWeight: '600',
        zIndex: '9999',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease'
    });
    
   
    switch(type) {
        case 'success':
            notification.style.backgroundColor = '#28a745';
            break;
        case 'error':
            notification.style.backgroundColor = '#dc3545';
            break;
        case 'info':
        default:
            notification.style.backgroundColor = '#17a2b8';
            break;
    }
    
   
    document.body.appendChild(notification);
    
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function updateCartBadge() {
    const cartBadge = document.querySelector('.cart-btn .notification-badge');
    if (cartBadge) {
        let currentCount = parseInt(cartBadge.textContent) || 0;
        cartBadge.textContent = currentCount + 1;
    }
}

document.addEventListener('DOMContentLoaded', initCartButtons);

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            observer.unobserve(img);
        }
    });
});

document.querySelectorAll('img[data-src]').forEach(img => {
    imageObserver.observe(img);
});

// Account Dropdown Menu
function initAccountDropdown() {
    const accountDropdown = document.getElementById('accountDropdown');
    
    if (accountDropdown) {
        accountDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Simple redirect approach for now
            window.location.href = '?page=login';
        });
    }
}

// Initialize account dropdown
document.addEventListener('DOMContentLoaded', initAccountDropdown);