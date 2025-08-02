<?php
require_once 'config.php';

/**
 * Form Validator Class
 * Handles all validation logic for form inputs
 */
class Validator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Validate all form fields
     * @return bool True if all validations pass
     */
    public function validateAll() {
        $this->validateName();
        $this->validateEmail();
        $this->validatePhone();
        $this->validatePassword();
        $this->validateAge();
        $this->validateCountry();
        $this->validateMessage();
        
        return empty($this->errors);
    }
    
    /**
     * Validate name field
     */
    public function validateName() {
        $name = trim($this->data['name'] ?? '');
        
        if (empty($name)) {
            $this->addError('name', ERROR_MESSAGES['name_required']);
            return false;
        }
        
        if (strlen($name) < MIN_NAME_LENGTH) {
            $this->addError('name', ERROR_MESSAGES['name_too_short']);
            return false;
        }
        
        if (strlen($name) > MAX_NAME_LENGTH) {
            $this->addError('name', ERROR_MESSAGES['name_too_long']);
            return false;
        }
        
        // Check for valid characters (letters, spaces, hyphens, apostrophes)
        if (!preg_match("/^[a-zA-Z\s\-']+$/", $name)) {
            $this->addError('name', 'Name can only contain letters, spaces, hyphens, and apostrophes');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate email field
     */
    public function validateEmail() {
        $email = trim($this->data['email'] ?? '');
        
        if (empty($email)) {
            $this->addError('email', ERROR_MESSAGES['email_required']);
            return false;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('email', ERROR_MESSAGES['email_invalid']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate phone field (optional)
     */
    public function validatePhone() {
        $phone = trim($this->data['phone'] ?? '');
        
        // Phone is optional, so if empty, it's valid
        if (empty($phone)) {
            return true;
        }
        
        // Remove all non-numeric characters for validation
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid format (10 digits or with formatting)
        if (!preg_match('/^(\d{3}-\d{3}-\d{4}|\d{10})$/', $phone) && strlen($cleanPhone) !== 10) {
            $this->addError('phone', ERROR_MESSAGES['phone_invalid']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate password fields
     */
    public function validatePassword() {
        $password = $this->data['password'] ?? '';
        $confirmPassword = $this->data['confirmPassword'] ?? '';
        
        if (empty($password)) {
            $this->addError('password', ERROR_MESSAGES['password_required']);
            return false;
        }
        
        // Check password strength
        if (!$this->isStrongPassword($password)) {
            $this->addError('password', ERROR_MESSAGES['password_weak']);
            return false;
        }
        
        // Check if passwords match
        if ($password !== $confirmPassword) {
            $this->addError('confirmPassword', ERROR_MESSAGES['password_mismatch']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if password meets strength requirements
     */
    private function isStrongPassword($password) {
        return strlen($password) >= MIN_PASSWORD_LENGTH &&
               preg_match('/[a-z]/', $password) &&      // lowercase letter
               preg_match('/[A-Z]/', $password) &&      // uppercase letter
               preg_match('/[0-9]/', $password) &&      // number
               preg_match('/[@$!%*?&]/', $password);    // special character
    }
    
    /**
     * Validate age field
     */
    public function validateAge() {
        $age = $this->data['age'] ?? '';
        
        if (empty($age)) {
            $this->addError('age', ERROR_MESSAGES['age_required']);
            return false;
        }
        
        if (!is_numeric($age) || $age < MIN_AGE || $age > MAX_AGE) {
            $this->addError('age', ERROR_MESSAGES['age_invalid']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate country field
     */
    public function validateCountry() {
        $country = $this->data['country'] ?? '';
        
        if (empty($country)) {
            $this->addError('country', ERROR_MESSAGES['country_required']);
            return false;
        }
        
        if (!array_key_exists($country, ALLOWED_COUNTRIES)) {
            $this->addError('country', ERROR_MESSAGES['country_invalid']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate message field (optional)
     */
    public function validateMessage() {
        $message = trim($this->data['message'] ?? '');
        
        // Message is optional, so if empty, it's valid
        if (empty($message)) {
            return true;
        }
        
        if (strlen($message) > MAX_MESSAGE_LENGTH) {
            $this->addError('message', ERROR_MESSAGES['message_too_long']);
            return false;
        }
        
        return true;
    }
    
    /**
     * Add error to errors array
     */
    private function addError($field, $message) {
        $this->errors[$field] = $message;
    }
    
    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get error for specific field
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }
    
    /**
     * Check if field has error
     */
    public function hasError($field) {
        return isset($this->errors[$field]);
    }
    
    /**
     * Get sanitized data
     */
    public function getSanitizedData() {
        $sanitized = [];
        
        foreach ($this->data as $key => $value) {
            if ($key === 'password' || $key === 'confirmPassword') {
                // Don't include passwords in sanitized data for security
                continue;
            }
            
            $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $sanitized;
    }
    
    /**
     * Static method to sanitize individual input
     */
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
?>