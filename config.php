<?php
/**
 * Configuration file for form validator
 */

// Database configuration (if needed)
define('DB_HOST', 'localhost');
define('DB_NAME', 'form_validator');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Form validation rules
define('MIN_NAME_LENGTH', 2);
define('MAX_NAME_LENGTH', 50);
define('MIN_PASSWORD_LENGTH', 8);
define('MIN_AGE', 18);
define('MAX_AGE', 120);
define('MAX_MESSAGE_LENGTH', 500);

// Allowed countries
define('ALLOWED_COUNTRIES', [
    'us' => 'United States',
    'ca' => 'Canada',
    'uk' => 'United Kingdom',
    'au' => 'Australia',
    'de' => 'Germany',
    'fr' => 'France',
    'jp' => 'Japan',
    'bd' => 'Bangladesh',
    'in' => 'India',
    'other' => 'Other'
]);

// Error messages
define('ERROR_MESSAGES', [
    'name_required' => 'Full name is required',
    'name_too_short' => 'Name must be at least ' . MIN_NAME_LENGTH . ' characters long',
    'name_too_long' => 'Name cannot exceed ' . MAX_NAME_LENGTH . ' characters',
    'email_required' => 'Email address is required',
    'email_invalid' => 'Please enter a valid email address',
    'phone_invalid' => 'Please enter a valid phone number (e.g., 123-456-7890 or 1234567890)',
    'password_required' => 'Password is required',
    'password_weak' => 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters with at least one uppercase letter, one lowercase letter, one number, and one special character',
    'password_mismatch' => 'Passwords do not match',
    'age_required' => 'Age is required',
    'age_invalid' => 'You must be at least ' . MIN_AGE . ' years old',
    'country_required' => 'Please select your country',
    'country_invalid' => 'Invalid country selection',
    'message_too_long' => 'Message cannot exceed ' . MAX_MESSAGE_LENGTH . ' characters'
]);

// Success messages
define('SUCCESS_MESSAGES', [
    'form_submitted' => 'Form submitted successfully! All fields are valid.',
    'data_saved' => 'Your information has been saved successfully.'
]);

// Set default timezone
date_default_timezone_set('Asia/Dhaka');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>