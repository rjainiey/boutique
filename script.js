// script.js - KEKABOO BOUTIQUE

// Cart Management
let cart = JSON.parse(localStorage.getItem('kekabooCart')) || [];

// Initialize cart in session
function initializeCart() {
    if (!localStorage.getItem('kekabooCart')) {
        localStorage.setItem('kekabooCart', JSON.stringify([]));
    }
}

// Add item to cart
function addToCart(productId, productName, price, image) {
    // Check if user is logged in
    if (typeof isLoggedIn === 'function' && !isLoggedIn()) {
        alert('Please login to add items to your cart');
        window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
        return;
    }
    
    const product = {
        id: productId,
        name: productName,
        price: parseFloat(price),
        image: image,
        quantity: 1
    };
    
    // Check if product already in cart
    const existingItemIndex = cart.findIndex(item => item.id === productId);
    
    if (existingItemIndex > -1) {
        cart[existingItemIndex].quantity += 1;
    } else {
        cart.push(product);
    }
    
    updateCart();
    showMessage('Added to bag successfully!', 'success');
}

// Update cart in localStorage
function updateCart() {
    localStorage.setItem('kekabooCart', JSON.stringify(cart));
    
    // Update cart count in navbar
    updateCartCount();
    
    // Send cart to server if on bag page
    if (window.location.pathname.includes('bag.php')) {
        sendCartToServer();
    }
}

// Update cart count display
function updateCartCount() {
    const cartItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    // You can add a cart count badge to navbar if needed
    const cartBadge = document.querySelector('.cart-badge');
    if (cartBadge) {
        cartBadge.textContent = cartItems;
        cartBadge.style.display = cartItems > 0 ? 'inline-block' : 'none';
    }
}

// Send cart to server (for bag.php)
function sendCartToServer() {
    // This would be an AJAX call to update server-side cart
    // For now, we'll reload the page to sync with PHP session
    if (window.location.pathname.includes('bag.php')) {
        window.location.reload();
    }
}

// Show message
function showMessage(message, type = 'success') {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    // Add to page
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(messageDiv, container.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#ef4444';
        } else {
            input.style.borderColor = '';
        }
    });
    
    if (!isValid) {
        showMessage('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

// Password strength checker
function checkPasswordStrength(password) {
    const strength = {
        0: "Very weak",
        1: "Weak",
        2: "Medium",
        3: "Strong",
        4: "Very strong"
    };
    
    let score = 0;
    
    // Length check
    if (password.length >= 8) score++;
    
    // Contains lowercase
    if (/[a-z]/.test(password)) score++;
    
    // Contains uppercase
    if (/[A-Z]/.test(password)) score++;
    
    // Contains numbers
    if (/[0-9]/.test(password)) score++;
    
    // Contains special characters
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    return {
        score: score,
        text: strength[score]
    };
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCart();
    updateCartCount();
    
    // Add event listeners for forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (this.id && !validateForm(this.id)) {
                e.preventDefault();
            }
        });
    });
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            const indicator = document.getElementById('password-strength');
            if (indicator) {
                indicator.textContent = `Strength: ${strength.text}`;
                indicator.style.color = ['#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'][strength.score];
            }
        });
    }
});

// Export functions for use in HTML
window.addToCart = addToCart;
window.showMessage = showMessage;