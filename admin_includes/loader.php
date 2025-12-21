<!-- Page Loader Overlay -->
<div id="pageLoader" class="page-loader">
    <div class="loader-content">
        <div class="loader-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="loader-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
        </div>
        <p class="loader-text">Loading...</p>
    </div>
</div>

<!-- Inline Loader Template (for sections/cards) -->
<template id="inlineLoaderTemplate">
    <div class="inline-loader">
        <div class="inline-spinner"></div>
        <span>Loading...</span>
    </div>
</template>

<!-- Button Loader Template -->
<template id="btnLoaderTemplate">
    <span class="btn-loader">
        <span class="btn-spinner"></span>
    </span>
</template>

<script>
// Loader Utility Functions - Global Object
const AdminLoader = {
    // Show full page loader
    show: function(text) {
        const loader = document.getElementById('pageLoader');
        if (!loader) return;
        const loaderText = loader.querySelector('.loader-text');
        if (loaderText && text) loaderText.textContent = text;
        loader.classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    // Hide full page loader
    hide: function() {
        const loader = document.getElementById('pageLoader');
        if (!loader) return;
        loader.classList.remove('active');
        document.body.style.overflow = '';
    },

    // Show loader on specific element (card, section)
    showInline: function(element, text) {
        if (!element) return;
        const template = document.getElementById('inlineLoaderTemplate');
        if (!template) return;
        const loader = template.content.cloneNode(true);
        if (text) loader.querySelector('span').textContent = text;
        element.style.position = 'relative';
        element.appendChild(loader);
    },

    // Hide inline loader from element
    hideInline: function(element) {
        if (!element) return;
        const loader = element.querySelector('.inline-loader');
        if (loader) loader.remove();
    },

    // Add loading state to button
    buttonLoading: function(button, text) {
        if (!button) return;
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        const template = document.getElementById('btnLoaderTemplate');
        if (!template) return;
        const loader = template.content.cloneNode(true);
        button.innerHTML = '';
        button.appendChild(loader);
        if (text) {
            const span = document.createElement('span');
            span.textContent = ' ' + text;
            button.appendChild(span);
        }
    },

    // Remove loading state from button
    buttonReset: function(button) {
        if (!button) return;
        button.disabled = false;
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            delete button.dataset.originalText;
        }
    }
};

// Initialize loader behaviors
(function() {
    // Show loader on ALL internal link clicks
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href) return;

        // Skip these types of links
        if (href.startsWith('#') ||
            href.startsWith('javascript:') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:') ||
            link.getAttribute('target') === '_blank' ||
            link.getAttribute('onclick') ||
            link.classList.contains('no-loader') ||
            href.includes('logout.php')) {
            return;
        }

        // Show loader for internal navigation
        if (href.includes('.php') || href.startsWith('../') || href.startsWith('./') || href.startsWith('/')) {
            AdminLoader.show('Loading...');
        }
    });

    // Show loader on form submissions
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList.contains('no-loader') || form.hasAttribute('data-ajax')) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            AdminLoader.buttonLoading(submitBtn, 'Processing...');
        }

        // Also show page loader for form submissions
        AdminLoader.show('Processing...');
    });

    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        AdminLoader.hide();
        // Reset any stuck buttons
        document.querySelectorAll('button[disabled][data-original-text]').forEach(function(btn) {
            AdminLoader.buttonReset(btn);
        });
    });

    // Handle browser back/forward
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            AdminLoader.hide();
            document.querySelectorAll('button[disabled]').forEach(function(btn) {
                if (btn.dataset.originalText) {
                    AdminLoader.buttonReset(btn);
                }
            });
        }
    });

    // Hide loader on page unload (prevents stuck loader)
    window.addEventListener('beforeunload', function() {
        // Loader will show, then page changes
    });

    // Fallback: Hide loader after timeout (in case of errors)
    setTimeout(function() {
        AdminLoader.hide();
    }, 10000);
})();

// ============ NOTIFICATION SYSTEM ============
// Initialize Notyf with custom configuration
const AdminNotify = new Notyf({
    duration: 4000,
    position: { x: 'right', y: 'top' },
    dismissible: true,
    ripple: true,
    types: [
        {
            type: 'success',
            background: '#10b981',
            icon: {
                className: 'bi bi-check-circle-fill',
                tagName: 'i'
            }
        },
        {
            type: 'error',
            background: '#ef4444',
            icon: {
                className: 'bi bi-x-circle-fill',
                tagName: 'i'
            }
        },
        {
            type: 'info',
            background: '#3b82f6',
            icon: {
                className: 'bi bi-info-circle-fill',
                tagName: 'i'
            }
        },
        {
            type: 'warning',
            background: '#f59e0b',
            icon: {
                className: 'bi bi-exclamation-triangle-fill',
                tagName: 'i'
            }
        },
        {
            type: 'deleted',
            background: '#ef4444',
            icon: {
                className: 'bi bi-trash-fill',
                tagName: 'i'
            }
        },
        {
            type: 'updated',
            background: '#6366f1',
            icon: {
                className: 'bi bi-pencil-fill',
                tagName: 'i'
            }
        },
        {
            type: 'added',
            background: '#10b981',
            icon: {
                className: 'bi bi-plus-circle-fill',
                tagName: 'i'
            }
        }
    ]
});

// Check URL parameters and show notifications
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    const status = urlParams.get('status');

    if (action && status) {
        // Get the page context from URL
        const path = window.location.pathname;
        let itemType = 'Item';

        if (path.includes('announcement')) {
            itemType = 'Announcement';
        } else if (path.includes('assignment')) {
            itemType = 'Assignment';
        } else if (path.includes('timetable')) {
            itemType = 'Timetable Entry';
        }

        // Show notification based on action and status
        if (status === 'success') {
            switch(action) {
                case 'added':
                    AdminNotify.open({
                        type: 'added',
                        message: `${itemType} created successfully!`
                    });
                    break;
                case 'updated':
                    AdminNotify.open({
                        type: 'updated',
                        message: `${itemType} updated successfully!`
                    });
                    break;
                case 'deleted':
                    AdminNotify.open({
                        type: 'deleted',
                        message: `${itemType} deleted successfully!`
                    });
                    break;
                default:
                    AdminNotify.success('Operation completed successfully!');
            }
        } else if (status === 'error') {
            switch(action) {
                case 'added':
                    AdminNotify.error(`Failed to create ${itemType.toLowerCase()}. Please try again.`);
                    break;
                case 'updated':
                    AdminNotify.error(`Failed to update ${itemType.toLowerCase()}. Please try again.`);
                    break;
                case 'deleted':
                    AdminNotify.error(`Failed to delete ${itemType.toLowerCase()}. Please try again.`);
                    break;
                default:
                    AdminNotify.error('Operation failed. Please try again.');
            }
        }

        // Clean up URL (remove query params) without refreshing page
        if (window.history.replaceState) {
            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        }
    }
})();
</script>
