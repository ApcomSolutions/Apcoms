// resources/js/app.js

import './bootstrap';
import 'flowbite';
import initErrorHandler from './errorHandlerIntegration';

// Initialize the error handler
const AppError = initErrorHandler();

// Make error handler globally available
window.AppError = AppError;

// Initialize AOS animations if available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    }
});
