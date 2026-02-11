// Main Script JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeElements();
});

function initializeElements() {
    // Initialize delete buttons
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });

    // Initialize approve buttons
    const approveButtons = document.querySelectorAll('[data-action="approve"]');
    approveButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            handleApprove(e);
        });
    });

    // Initialize reject buttons
    const rejectButtons = document.querySelectorAll('[data-action="reject"]');
    rejectButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            handleReject(e);
        });
    });

    // Initialize amount validation
    const amountInputs = document.querySelectorAll('[data-type="amount"]');
    amountInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < 0) {
                this.value = '';
            }
        });
    });
}

function handleApprove(event) {
    event.preventDefault();
    if (confirm('Are you sure you want to approve this?')) {
        const form = event.target.closest('form');
        if (form) {
            form.submit();
        }
    }
}

function handleReject(event) {
    event.preventDefault();
    const reason = prompt('Please enter rejection reason:');
    if (reason !== null) {
        const form = event.target.closest('form');
        if (form) {
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            form.appendChild(reasonInput);
            form.submit();
        }
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'RWF'
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease-in-out;
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
