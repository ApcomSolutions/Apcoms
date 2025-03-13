/**
 * CustomErrorHandler.js
 * A secure error handling utility styled like mobile browser side alerts
 */

// Create the error alert container once and reuse it
let alertContainer = null;
let alertTimeout = null;

/**
 * Display a user-friendly error message in mobile browser side alert style
 * @param {string} message - Error message to display
 * @param {string} type - Error type ('error', 'warning', 'info', 'success')
 * @param {number} duration - How long to show the alert (ms), 0 for persistent
 */
function showAlert(message, type = 'info', duration = 5000) {
    // Remove any existing alerts first to prevent stacking
    removeExistingAlerts();

    // Create container - positioned at the top right
    alertContainer = document.createElement('div');
    alertContainer.className = 'fixed top-4 right-4 z-50 transition-all duration-300 opacity-0 max-w-xs w-full';
    alertContainer.style.maxWidth = '380px';
    document.body.appendChild(alertContainer);

    // Determine color scheme and icon based on type
    let bgColor, borderColor, textColor, iconSvg, titleText;

    switch (type) {
        case 'success':
            bgColor = 'bg-green-50';
            borderColor = 'border-green-500';
            textColor = 'text-green-700';
            titleText = 'Success';
            iconSvg = `<svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>`;
            break;
        case 'warning':
            bgColor = 'bg-yellow-50';
            borderColor = 'border-yellow-500';
            textColor = 'text-yellow-700';
            titleText = 'Warning';
            iconSvg = `<svg class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>`;
            break;
        case 'error':
            bgColor = 'bg-red-50';
            borderColor = 'border-red-500';
            textColor = 'text-red-700';
            titleText = 'Error';
            iconSvg = `<svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>`;
            break;
        case 'info':
        default:
            bgColor = 'bg-blue-50';
            borderColor = 'border-blue-500';
            textColor = 'text-blue-700';
            titleText = 'Information';
            iconSvg = `<svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
            break;
    }

    // Set content - side alert style similar to the image
    alertContainer.innerHTML = `
    <div class="${bgColor} rounded-md overflow-hidden shadow-md border-l-4 ${borderColor} relative">
        <!-- Close button (X) at the top right -->
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-500" id="alert-close-btn">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Content -->
        <div class="p-4">
            <!-- Title with icon -->
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-2">
                    ${iconSvg}
                </div>
                <h3 class="text-sm font-medium ${textColor}">${titleText}</h3>
            </div>

            <!-- Message -->
            <div class="mt-2 text-sm ${textColor}">
                <p>${message}</p>
            </div>
        </div>
    </div>
  `;

    // Fade in effect - important to use setTimeout to ensure the transition works
    setTimeout(() => {
        if (alertContainer) alertContainer.classList.add('opacity-100');
    }, 10);

    // Add close button listener
    const closeButton = document.getElementById('alert-close-btn');
    if (closeButton) {
        closeButton.addEventListener('click', hideAlert);
    }

    // Auto-hide after duration (if duration > 0)
    if (duration > 0) {
        alertTimeout = setTimeout(hideAlert, duration);
    }
}

/**
 * Hide the alert with animation
 */
function hideAlert() {
    // Clear any existing timeout
    if (alertTimeout) {
        clearTimeout(alertTimeout);
        alertTimeout = null;
    }

    // Fade out with transition
    if (alertContainer) {
        alertContainer.classList.remove('opacity-100');
        alertContainer.classList.add('opacity-0');
    }

    // After transition is complete, remove elements from DOM completely
    setTimeout(() => {
        removeExistingAlerts();
    }, 300); // Match this to your transition duration
}

/**
 * Remove existing alerts from DOM completely
 */
function removeExistingAlerts() {
    // Remove any existing alert containers
    if (alertContainer && alertContainer.parentNode) {
        alertContainer.parentNode.removeChild(alertContainer);
        alertContainer = null;
    }

    // Also check for any orphaned elements
    const existingAlerts = document.querySelectorAll('.fixed.top-4.right-4.z-50');
    existingAlerts.forEach(el => {
        if (el.querySelector('#alert-close-btn')) {
            el.parentNode.removeChild(el);
        }
    });
}

/**
 * Handle API errors in a secure way
 * @param {Error} error - The error object
 * @param {string} context - Context where the error occurred
 * @returns {string} User-friendly error message
 */
function handleApiError(error, context = '') {
    console.error(`Error in ${context}:`, error);

    let userMessage = 'An unexpected error occurred. Please try again later.';

    // Check if we have a structured error response
    if (error.response && error.response.data) {
        // Handle structured API errors
        const responseData = error.response.data;

        if (responseData.message) {
            // Use the message but filter out sensitive information
            userMessage = sanitizeErrorMessage(responseData.message);
        }

        // Handle validation errors
        if (responseData.errors) {
            const errorMessages = [];

            Object.entries(responseData.errors).forEach(([field, messages]) => {
                if (Array.isArray(messages)) {
                    errorMessages.push(messages[0]); // Take the first message for each field
                } else if (typeof messages === 'string') {
                    errorMessages.push(messages);
                }
            });

            if (errorMessages.length > 0) {
                userMessage = errorMessages.join(' ');
            }
        }
    } else if (error.message) {
        // If it's a simple error with a message
        userMessage = sanitizeErrorMessage(error.message);
    }

    return userMessage;
}

/**
 * Sanitize error messages to prevent leaking sensitive information
 * @param {string} message - Raw error message
 * @returns {string} Sanitized message
 */
function sanitizeErrorMessage(message) {
    // If the message is not a string, provide a default
    if (typeof message !== 'string') {
        return 'An error occurred.';
    }

    // Check for SQL error patterns and replace with generic message
    const sqlErrorPatterns = [
        /SQL syntax/i,
        /SQL statement/i,
        /syntax error/i,
        /SQLSTATE/i,
        /ORA-\d+/i,
        /PostgreSQL/i,
        /MySQL/i,
        /MariaDB/i,
        /database error/i,
        /query failed/i,
        /constraint violation/i,
        /foreign key constraint/i,
        /unique constraint/i,
        /duplicate entry/i,
        /JDBCException/i,
        /SqlException/i,
        /DB2/i,
        /SQLServer/i
    ];

    // Check for stack trace patterns
    const stackTracePatterns = [
        /at .+\(.+:\d+:\d+\)/i,
        /stack trace/i,
        /line \d+/i,
        /file .+\.php/i,
        /\.js:\d+/i,
        /in .+\.php on line \d+/i,
        /Exception in/i,
        /Traceback/i,
        /throw new Error/i
    ];

    // Check for path disclosure
    const pathDisclosurePatterns = [
        /\/var\/www\//i,
        /\/home\//i,
        /C:\\\\Program Files/i,
        /\/usr\/local\/bin/i,
        /\.\.\/\.\.\/\.\.\/\.\.\//i,
        /\/etc\//i,
        /app\/Http\//i,
        /vendor\//i,
        /artisan/i
    ];

    // Check if any pattern matches
    for (const pattern of [...sqlErrorPatterns, ...stackTracePatterns, ...pathDisclosurePatterns]) {
        if (pattern.test(message)) {
            if (pattern === /constraint violation/i || pattern === /duplicate entry/i) {
                return 'This action cannot be completed because it conflicts with existing data.';
            }

            return 'An internal error occurred. Our team has been notified.';
        }
    }

    // Otherwise return the original message
    return message;
}

/**
 * Show a success notification
 * @param {string} message - Success message
 * @param {number} duration - How long to show the success notification
 */
function showSuccess(message, duration = 3000) {
    showAlert(message, 'success', duration);
}

/**
 * Show a warning notification
 * @param {string} message - Warning message
 * @param {number} duration - How long to show the warning notification
 */
function showWarning(message, duration = 4000) {
    showAlert(message, 'warning', duration);
}

/**
 * Show an info notification
 * @param {string} message - Info message
 * @param {number} duration - How long to show the info notification
 */
function showInfo(message, duration = 3000) {
    showAlert(message, 'info', duration);
}

/**
 * Show an error notification
 * @param {string} message - Error message
 * @param {number} duration - How long to show the error notification
 */
function showError(message, duration = 5000) {
    showAlert(message, 'error', duration);
}

/**
 * Replace all calls to window.alert with our custom alerting mechanism
 */
function replaceNativeAlert() {
    window.originalAlert = window.alert;
    window.alert = function(message) {
        showAlert(message, 'info', 3000);
    };
}

/**
 * Restore the original alert function
 */
function restoreNativeAlert() {
    if (window.originalAlert) {
        window.alert = window.originalAlert;
    }
}

// Add necessary styles to the document
function addAlertStyles() {
    if (!document.getElementById('custom-alert-styles')) {
        const styleEl = document.createElement('style');
        styleEl.id = 'custom-alert-styles';
        styleEl.textContent = `
      .fixed {
        position: fixed;
      }
      .top-4 {
        top: 1rem;
      }
      .right-4 {
        right: 1rem;
      }
      .z-50 {
        z-index: 50;
      }
      .max-w-xs {
        max-width: 20rem;
      }
      .w-full {
        width: 100%;
      }
      .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
      }
      .duration-300 {
        transition-duration: 300ms;
      }
      .opacity-0 {
        opacity: 0;
      }
      .opacity-100 {
        opacity: 1;
      }
      @keyframes alertSlideIn {
        from {
          transform: translateX(20px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
      @keyframes alertSlideOut {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(20px);
          opacity: 0;
        }
      }
    `;
        document.head.appendChild(styleEl);
    }
}

// Initialize when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    addAlertStyles();
    replaceNativeAlert();

    // Cleanup on page load just in case
    removeExistingAlerts();
});

// Export the API for usage
window.ErrorHandler = {
    showError,
    showSuccess,
    showWarning,
    showInfo,
    handleApiError
};
