// resources/js/errorHandlerIntegration.js

/**
 * This file provides an easy way to integrate the CustomErrorHandler
 * across all JavaScript files.
 */

// Check if ErrorHandler is available and provide fallbacks if not
export default function initErrorHandler() {
    // Create a wrapper for error handling methods
    window.AppError = {
        /**
         * Show an error notification
         * @param {string} message - Error message
         * @param {number} duration - How long to show notification in ms
         */
        showError: function(message, duration = 5000) {
            if (window.ErrorHandler && window.ErrorHandler.showError) {
                window.ErrorHandler.showError(message, duration);
            } else {
                console.error('ErrorHandler not available, fallback to alert:', message);
                window.alert(message);
            }
        },

        /**
         * Show a success notification
         * @param {string} message - Success message
         * @param {number} duration - How long to show notification in ms
         */
        showSuccess: function(message, duration = 3000) {
            if (window.ErrorHandler && window.ErrorHandler.showSuccess) {
                window.ErrorHandler.showSuccess(message, duration);
            } else {
                console.log('ErrorHandler not available, fallback to alert:', message);
                window.alert(message);
            }
        },

        /**
         * Show a warning notification
         * @param {string} message - Warning message
         * @param {number} duration - How long to show notification in ms
         */
        showWarning: function(message, duration = 4000) {
            if (window.ErrorHandler && window.ErrorHandler.showWarning) {
                window.ErrorHandler.showWarning(message, duration);
            } else {
                console.warn('ErrorHandler not available, fallback to alert:', message);
                window.alert(message);
            }
        },

        /**
         * Show an info notification
         * @param {string} message - Info message
         * @param {number} duration - How long to show notification in ms
         */
        showInfo: function(message, duration = 3000) {
            if (window.ErrorHandler && window.ErrorHandler.showInfo) {
                window.ErrorHandler.showInfo(message, duration);
            } else {
                console.info('ErrorHandler not available, fallback to alert:', message);
                window.alert(message);
            }
        },

        /**
         * Handle API errors
         * @param {Error} error - Error object
         * @param {string} context - Context where error occurred
         * @returns {string} User-friendly error message
         */
        handleApiError: function(error, context = '') {
            if (window.ErrorHandler && window.ErrorHandler.handleApiError) {
                return window.ErrorHandler.handleApiError(error, context);
            } else {
                console.error('Error handler not available, basic error handling for:', context, error);
                return error.message || 'An unexpected error occurred';
            }
        }
    };

    // Legacy support - replace window.alert with our custom alert
    if (window.ErrorHandler) {
        // Save reference to original alert for safety
        if (!window._originalAlert) {
            window._originalAlert = window.alert;
        }

        // Replace with a more intelligent alert that detects type from content
        // Replace with a more intelligent alert that detects type from content
        window.alert = function(message) {
            if (typeof message === 'string') {
                const lowerMessage = message.toLowerCase();

                // Special case for specific phrases that should always be success
                if (lowerMessage.includes('deleted successfully') ||
                    lowerMessage.includes('team member deleted successfully')) {
                    console.log('SPECIAL CASE SUCCESS ALERT:', message);
                    return window.ErrorHandler.showSuccess(message, 3000);
                }

                if (message.includes('The position field is required') ||
                    message.includes('Error creating team member') ||
                    message.includes('Failed to') ||
                    message.includes('gagal') ||
                    message.includes('tidak valid')) {
                    console.log('SPECIAL ERROR ALERT:', message);
                    return window.ErrorHandler.showError(message, 5000);
                }

                // Check for success-related patterns (English and Indonesian)
                if (lowerMessage.includes('success') ||
                    lowerMessage.includes('successfully') ||
                    lowerMessage.includes('saved successfully') ||
                    lowerMessage.includes('updated successfully') ||
                    lowerMessage.includes('created successfully') ||
                    // Indonesian success keywords
                    lowerMessage.includes('berhasil') ||
                    lowerMessage.includes('sukses') ||
                    lowerMessage.includes('dihapus') ||
                    lowerMessage.includes('ditambahkan') ||
                    lowerMessage.includes('dirubah') ||
                    lowerMessage.includes('diubah') ||
                    lowerMessage.includes('disimpan') ||
                    lowerMessage.includes('diperbarui') ||
                    lowerMessage.includes('diedit') ||
                    // Generic "di" + verb success patterns
                    lowerMessage.includes(' di') && (
                        lowerMessage.includes('buat') ||
                        lowerMessage.includes('hapus') ||
                        lowerMessage.includes('ubah') ||
                        lowerMessage.includes('tambah') ||
                        lowerMessage.includes('simpan') ||
                        lowerMessage.includes('update')
                    ) ||
                    // Additional success indicators
                    lowerMessage.includes('telah dibuat') ||
                    lowerMessage.includes('telah diperbarui')) {
                    console.log('SUCCESS ALERT TRIGGERED:', message);
                    return window.ErrorHandler.showSuccess(message, 3000);
                }

                // Error-related keywords (English and Indonesian)
                else if (lowerMessage.includes('error') ||
                    lowerMessage.includes('failed') ||
                    lowerMessage.includes('invalid') ||
                    lowerMessage.includes('not found') ||
                    lowerMessage.startsWith('error:') ||
                    // Indonesian error keywords
                    lowerMessage.includes('gagal') ||
                    lowerMessage.includes('kesalahan') ||
                    lowerMessage.includes('tidak valid') ||
                    lowerMessage.includes('tidak ditemukan') ||
                    lowerMessage.includes('error:')) {
                    console.log('ERROR ALERT TRIGGERED:', message);
                    return window.ErrorHandler.showError(message, 5000);
                }

                // Warning-related keywords (English and Indonesian)
                else if (lowerMessage.includes('warning') ||
                    lowerMessage.includes('caution') ||
                    lowerMessage.includes('notice') ||
                    // Indonesian warning keywords
                    lowerMessage.includes('peringatan') ||
                    lowerMessage.includes('perhatian') ||
                    lowerMessage.includes('waspada')) {
                    console.log('WARNING ALERT TRIGGERED:', message);
                    return window.ErrorHandler.showWarning(message, 4000);
                }

                // Default to info
                else {
                    console.log('INFO ALERT TRIGGERED:', message);
                    return window.ErrorHandler.showInfo(message, 3000);
                }
            } else {
                // If not a string, just use info type
                return window.ErrorHandler.showInfo(String(message), 3000);
            }
        };
    }

    console.log('Error handler integration initialized');
    return window.AppError;
}
