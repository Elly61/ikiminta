// Authentication JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm') || document.getElementById('loginForm') || document.getElementById('adminLoginForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(form);
        });
    }
});

function handleFormSubmit(form) {
    const formData = new FormData(form);
    const url = form.action;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            if (data.data && data.data.redirect) {
                // Redirect immediately after showing message briefly
                setTimeout(() => {
                    window.location.replace(data.data.redirect);
                }, 500);
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    });
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `${type}-message`;
    messageDiv.textContent = message;
    
    const container = document.querySelector('.auth-form') || document.body;
    container.insertBefore(messageDiv, container.firstChild);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Password validation
function validatePassword(password) {
    return password.length >= 8;
}

// Email validation
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Legal ID validation (16 characters)
function validateLegalId(id) {
    return id.length === 16;
}
