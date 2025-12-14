// Order Tracking JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-refresh order status (optional)
    const AUTO_REFRESH_INTERVAL = 30000; // 30 seconds
    let refreshTimer;

    // Initialize
    init();

    function init() {
        setupWhatsAppButton();
        setupAutoRefresh();
        animateProgressOnLoad();
    }

    // Auto Refresh Status
    function setupAutoRefresh() {
        // Check if auto-refresh is enabled
        const isTrackingActive = document.querySelector('.tracking-note');
        
        if (isTrackingActive) {
            refreshTimer = setInterval(() => {
                refreshOrderStatus();
            }, AUTO_REFRESH_INTERVAL);
        }
    }

    // Refresh order status via AJAX
    function refreshOrderStatus() {
        const invoiceNumber = document.querySelector('.invoice-number')?.textContent;
        
        if (!invoiceNumber) return;

        // Uncomment this when you have the backend API ready
        /*
        fetch(`/api/orders/${invoiceNumber}/status`)
            .then(response => response.json())
            .then(data => {
                updateOrderStatus(data);
            })
            .catch(error => {
                console.error('Error refreshing order status:', error);
            });
        */
    }

    // Update order status in the UI
    function updateProgressSteps(currentStatus) {
        // Mapping status database ke progress bar:
        const statusMap = {
            'paid': 0, 
            'processing': 1,
            'ready': 2,
            'serving': 3,
            'completed': 4
        };
        
        // Urutan status yang ditampilkan di progress bar (5 steps)
        const steps = ['paid', 'processing', 'ready', 'serving', 'completed']; 
        
        const currentIndex = statusMap[currentStatus.toLowerCase()] !== undefined 
                            ? statusMap[currentStatus.toLowerCase()] 
                            : -1; 

        document.querySelectorAll('.progress-step').forEach((step, index) => {
            step.classList.remove('active', 'current', 'completed');
            
            // Tandai step yang sudah selesai
            if (index < currentIndex) {
                step.classList.add('active', 'completed');
            } 
            // Tandai step yang sedang berjalan (current)
            else if (index === currentIndex) {
                step.classList.add('active', 'current');
            }
            
            // Pengecualian: 'paid' selalu selesai jika status > paid
            if (currentStatus.toLowerCase() !== 'pending' && index === 0) {
                step.classList.add('active', 'completed');
            }
        });
    }

    // Update progress timeline
    function updateProgressSteps(currentStep) {
        const steps = ['received', 'preparing', 'queueing', 'ready', 'completed'];
        const currentIndex = steps.indexOf(currentStep.toLowerCase());

        document.querySelectorAll('.progress-step').forEach((step, index) => {
            step.classList.remove('active', 'current', 'completed');
            
            if (index < currentIndex) {
                step.classList.add('active', 'completed');
            } else if (index === currentIndex) {
                step.classList.add('active', 'current');
            }
        });
    }

    // Animate progress on page load
    function animateProgressOnLoad() {
        const steps = document.querySelectorAll('.progress-step');
        steps.forEach((step, index) => {
            setTimeout(() => {
                step.style.opacity = '0';
                step.style.transform = 'translateX(-20px)';
                step.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    step.style.opacity = '1';
                    step.style.transform = 'translateX(0)';
                }, 50);
            }, index * 100);
        });
    }

    // Countdown timer for estimated completion
    function startCountdown() {
        const timeMinutesEl = document.querySelector('.time-minutes');
        if (!timeMinutesEl) return;

        let minutes = parseInt(timeMinutesEl.textContent);
        
        const countdownInterval = setInterval(() => {
            minutes--;
            
            if (minutes <= 0) {
                clearInterval(countdownInterval);
                timeMinutesEl.textContent = '0';
                // Show notification or alert
                showNotification('Your order is ready!');
            } else {
                timeMinutesEl.textContent = minutes;
            }
        }, 60000); // Every minute
    }

    // Show notification
    function showNotification(message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Orbit Cafe Order Update', {
                body: message,
                icon: '/images/logo.png'
            });
        }
    }

    // Request notification permission
    function requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    // Item image error handling
    document.querySelectorAll('.item-image').forEach(img => {
        img.addEventListener('error', function() {
            this.src = '/images/drinks/default.jpg';
        });
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
        }
    });

    // Request notification permission on load (optional)
    // requestNotificationPermission();
});

// Export functions for external use
export {
    updateOrderStatus,
    refreshOrderStatus
};