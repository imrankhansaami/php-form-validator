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
- Maximum 50 characters
- Only letters, spaces, hyphens, and apostrophes allowed

### Email
- Required field
- Must be valid email format
- Checks for existing email (if database enabled)

### Phone
- Optional field
- Format: 123-456-7890 or 1234567890
- Must be exactly 10 digits

### Password
- Required field
- Minimum 8 characters
- Must contain: uppercase, lowercase, number, special character
- Special characters: @$!%*?&

### Confirm Password
- Required field
- Must match password exactly

### Age
- Required field
- Must be between 18 and 120
- Must be numeric

### Country
- Required field
- Must be from predefined list
- Configurable in `config.php`

### Message
- Optional field
- Maximum 500 characters

## API Endpoints

### Form Submission
- **URL**: `process_form.php`
- **Method**: POST
- **Content-Type**: `application/x-www-form-urlencoded`
- **Response**: Redirect or JSON (for AJAX)

### AJAX Validation
- **URL**: `ajax_validate.php`
- **Method**: POST
- **Content-Type**: `application/json`
- **Payload**:
```json
{
    "field": "email",
    "value": "user@example.com",
    "allData": {...}
}
```

## Database Schema

If using database storage, the following tables are created:

### users
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    country VARCHAR(10) NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ip_address VARCHAR(45)
);
```

### activity_logs
```sql
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    identifier VARCHAR(255),
    level ENUM('info', 'warning', 'error') DEFAULT 'info',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Security Features

- **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: HTML encoding with `htmlspecialchars()`
- **CSRF Protection**: Can be added using tokens
- **Input Sanitization**: Automatic trimming and cleaning
- **Rate Limiting**: Can be implemented in `process_form.php`

## Error Handling

### Client-Side Errors
- Real-time validation feedback
- Field highlighting
- Inline error messages
- Form submission prevention

### Server-Side Errors
- Detailed validation messages
- Exception handling
- Error logging
- User-friendly error display

## Customization

### Adding New Validation Rules

1. **Add to Validator class**:
```php
public function validateCustomField() {
    $value = $this->data['custom_field'] ?? '';
    
    if (!$this->customValidationLogic($value)) {
        $this->addError('custom_field', 'Custom error message');
        return false;
    }
    
    return true;
}
```

2. **Update `validateAll()` method**:
```php
public function validateAll() {
    $this->validateName();
    $this->validateEmail();
    // ... existing validations
    $this->validateCustomField(); // Add this line
    
    return empty($this->errors);
}
```

3. **Add to form HTML**:
```html
<div class="form-group">
    <label for="custom_field">Custom Field</label>
    <input type="text" id="custom_field" name="custom_field">
    <div class="error-message" id="customFieldError"></div>
</div>
```

### Modifying Error Messages

Edit the `ERROR_MESSAGES` constant in `config.php`:

```php
define('ERROR_MESSAGES', [
    'name_required' => 'Your custom name error message',
    'email_invalid' => 'Your custom email error message',
    // ... other messages
]);
```

## Deployment

### Production Checklist

- [ ] Update database credentials
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Enable HTTPS
- [ ] Configure error reporting (disable in production)
- [ ] Set up regular backups
- [ ] Implement rate limiting
- [ ] Remove or restrict `setup.php`
- [ ] Configure proper logging

### Performance Optimization

- Use database instead of file storage for better performance
- Implement caching for validation rules
- Optimize database queries with indexes
- Use CDN for static assets
- Enable gzip compression

## Troubleshooting

### Common Issues

**Form not submitting**
- Check file permissions
- Verify PHP error logs
- Ensure all required files are present

**Database connection fails**
- Verify database credentials in `config.php`
- Check if MySQL service is running
- Ensure PDO MySQL extension is installed

**AJAX validation not working**
- Check browser console for JavaScript errors
- Verify `ajax_validate.php` is accessible
- Check server error logs

**File storage not working**
- Ensure `submissions/` and `logs/` directories exist
- Check write permissions (755 for directories)
- Verify disk space availability

### Debug Mode

Enable debug mode by adding to `config.php`:

```php
// Debug mode - disable in production
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions:
- Check the troubleshooting section
- Review error logs
- Test with `setup.php`
- Verify all requirements are met

## Changelog

### Version 1.0.0
- Initial release
- Complete form validation system
- Database integration
- AJAX support
- Security features
- Documentation and setup script
