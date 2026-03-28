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
    
    console.log('Form submitted. URL:', url);

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response received. Status:', response.status, 'Content-Type:', response.headers.get('content-type'));
        if (!response.ok) {
            console.error('HTTP error, status:', response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        console.log('Status:', data.status);
        console.log('Message:', data.message);
        console.log('Data object:', data.data);
        
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            if (data.data && data.data.redirect) {
                console.log('Redirect URL found:', data.data.redirect);
                // Redirect after message is shown
                setTimeout(() => {
                    console.log('Executing redirect now to:', data.data.redirect);
                    window.location.href = data.data.redirect;
                }, 1000);
            } else {
                console.warn('No redirect data in response');
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        console.error('Error stack:', error.stack);
        showMessage('An error occurred. Please try again.', 'error');
    });
}

function showMessage(message, type) {
    console.log('Showing message:', type, '-', message);
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `${type}-message`;
    messageDiv.textContent = message;
    messageDiv.style.marginBottom = '20px';
    messageDiv.style.zIndex = '9999';
    
    // Try to insert into auth-form first, then auth-container, then body
    const authForm = document.querySelector('.auth-form');
    const authContainer = document.querySelector('.auth-container');
    const container = authForm || authContainer || document.body;
    
    console.log('Message container:', container.className);
    
    if (container.firstChild) {
        container.insertBefore(messageDiv, container.firstChild);
    } else {
        container.appendChild(messageDiv);
    }
    
    console.log('Message inserted into DOM');
    
    // Auto-remove message after 3 seconds
    setTimeout(() => {
        console.log('Removing message after 3 seconds');
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 3000);
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
