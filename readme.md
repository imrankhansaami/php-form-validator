# PHP Form Validator

A comprehensive, multi-file PHP form validation system with clean separation of concerns, robust validation rules, and optional database integration.

## Features

- **Comprehensive Validation**: Name, email, phone, password, age, country, and message validation
- **Security**: Password hashing, SQL injection prevention, XSS protection
- **Database Integration**: Optional MySQL database storage with PDO
- **AJAX Support**: Real-time field validation for better UX
- **File-based Fallback**: Works without database using JSON file storage
- **Responsive Design**: Mobile-friendly form with modern UI
- **Error Handling**: Detailed error messages and logging
- **Configurable**: Easy-to-modify validation rules and settings

## File Structure

```
php-form-validator/
├── config.php              # Configuration and constants
├── Validator.php           # Main validation class
├── process_form.php        # Form processing logic
├── index.php              # Main form HTML page
├── Database.php           # Database handler (optional)
├── ajax_validate.php      # AJAX validation endpoint
├── setup.php             # Installation script
├── README.md             # This documentation
├── submissions/          # Directory for JSON file storage
└── logs/                # Directory for activity logs
```

## Requirements

- PHP 7.4 or higher
- PDO extension (for database features)
- PDO MySQL extension (for database features)
- JSON extension
- Write permissions for `submissions/` and `logs/` directories

## Installation

1. **Upload Files**: Upload all PHP files to your web server
2. **Set Permissions**: Ensure directories are writable (755)
3. **Run Setup**: Visit `setup.php` in your browser for guided installation
4. **Configure Database** (optional): Update database credentials in `config.php`

### Quick Setup

```bash
# Set directory permissions
chmod 755 submissions logs
chmod 644 *.php

# If using database, create database and update config.php
mysql -u root -p -e "CREATE DATABASE form_validator"
```

## Configuration

Edit `config.php` to customize:

```php
// Validation rules
define('MIN_NAME_LENGTH', 2);
define('MIN_PASSWORD_LENGTH', 8);
define('MIN_AGE', 18);

// Database settings (if using database)
define('DB_HOST', 'localhost');
define('DB_NAME', 'form_validator');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

## Usage Examples

### Basic Form Processing

```php
<?php
require_once 'Validator.php';

if ($_POST) {
    $validator = new Validator($_POST);
    
    if ($validator->validateAll()) {
        // Success - process the data
        $cleanData = $validator->getSanitizedData();
        echo "Form submitted successfully!";
    } else {
        // Show errors
        $errors = $validator->getErrors();
        foreach ($errors as $field => $message) {
            echo "$field: $message<br>";
        }
    }
}
?>
```

### Individual Field Validation

```php
<?php
$validator = new Validator(['email' => $email]);

if ($validator->validateEmail()) {
    echo "Email is valid";
} else {
    echo "Error: " . $validator->getError('email');
}
?>
```

### Database Integration

```php
<?php
require_once 'Database.php';

try {
    $db = Database::getInstance();
    $userId = $db->insertUser($cleanData, $password);
    echo "User saved with ID: $userId";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

## Validation Rules

### Name
- Required field
- Minimum 2 characters
- Maximum