<?php
require_once 'config.php';

// Get response data from session (if redirected from process_form.php)
$response = $_SESSION['form_response'] ?? null;
$formData = $_SESSION['form_data'] ?? [];

// Clear session data after using it
unset($_SESSION['form_response'], $_SESSION['form_data']);

/**
 * Helper function to get form field value
 */
function getFieldValue($field, $formData, $default = '') {
    return htmlspecialchars($formData[$field] ?? $default, ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function to check if field has error
 */
function hasError($field, $response) {
    return isset($response['errors'][$field]);
}

/**
 * Helper function to get error message for field
 */
function getError($field, $response) {
    return $response['errors'][$field] ?? '';
}

/**
 * Helper function to generate error class
 */
function getErrorClass($field, $response) {
    return hasError($field, $response) ? 'error' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Form Validator</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .error {
            border-color: #e74c3c !important;
        }
        
        .success {
            border-color: #27ae60 !important;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .alert {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .required {
            color: #e74c3c;
            margin-left: 3px;
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        .loading .spinner {
            display: inline-block;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>PHP Form Validator</h2>
        
        <?php if ($response): ?>
            <div class="alert <?php echo $response['success'] ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($response['message']); ?>
            </div>
        <?php endif; ?>
        
        <form id="validationForm" method="POST" action="process_form.php" novalidate>
            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo getFieldValue('name', $formData); ?>"
                    class="<?php echo getErrorClass('name', $response); ?>"
                    required
                >
                <?php if (hasError('name', $response)): ?>
                    <div class="error-message"><?php echo getError('name', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo getFieldValue('email', $formData); ?>"
                    class="<?php echo getErrorClass('email', $response); ?>"
                    required
                >
                <?php if (hasError('email', $response)): ?>
                    <div class="error-message"><?php echo getError('email', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="<?php echo getFieldValue('phone', $formData); ?>"
                    class="<?php echo getErrorClass('phone', $response); ?>"
                    placeholder="123-456-7890 or 1234567890"
                >
                <?php if (hasError('phone', $response)): ?>
                    <div class="error-message"><?php echo getError('phone', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input 
                    type="password" 
                    id="password" 
                    name="password"
                    class="<?php echo getErrorClass('password', $response); ?>"
                    required
                >
                <?php if (hasError('password', $response)): ?>
                    <div class="error-message"><?php echo getError('password', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                <input 
                    type="password" 
                    id="confirmPassword" 
                    name="confirmPassword"
                    class="<?php echo getErrorClass('confirmPassword', $response); ?>"
                    required
                >
                <?php if (hasError('confirmPassword', $response)): ?>
                    <div class="error-message"><?php echo getError('confirmPassword', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="age">Age <span class="required">*</span></label>
                <input 
                    type="number" 
                    id="age" 
                    name="age" 
                    min="<?php echo MIN_AGE; ?>" 
                    max="<?php echo MAX_AGE; ?>"
                    value="<?php echo getFieldValue('age', $formData); ?>"
                    class="<?php echo getErrorClass('age', $response); ?>"
                    required
                >
                <?php if (hasError('age', $response)): ?>
                    <div class="error-message"><?php echo getError('age', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="country">Country <span class="required">*</span></label>
                <select 
                    id="country" 
                    name="country"
                    class="<?php echo getErrorClass('country', $response); ?>"
                    required
                >
                    <option value="">Select your country</option>
                    <?php foreach (ALLOWED_COUNTRIES as $code => $name): ?>
                        <option 
                            value="<?php echo $code; ?>"
                            <?php echo (getFieldValue('country', $formData) === $code) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (hasError('country', $response)): ?>
                    <div class="error-message"><?php echo getError('country', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <textarea 
                    id="message" 
                    name="message" 
                    rows="4"
                    maxlength="<?php echo MAX_MESSAGE_LENGTH; ?>"
                    class="<?php echo getErrorClass('message', $response); ?>"
                    placeholder="Optional message (max <?php echo MAX_MESSAGE_LENGTH; ?> characters)"
                ><?php echo getFieldValue('message', $formData); ?></textarea>
                <?php if (hasError('message', $response)): ?>
                    <div class="error-message"><?php echo getError('message', $response); ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" id="submitBtn">
                <span class="spinner"></span>
                Submit Form
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('validationForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Add loading state on form submission
            form.addEventListener('submit', function() {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });
            
            // Real-time validation feedback (optional)
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.classList.contains('error') && this.value.trim()) {
                        // Remove error class if user has entered something
                        this.classList.remove('error');
                    }
                });
            });
            
            // Password confirmation validation
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            
            function checkPasswordMatch() {
                if (password.value && confirmPassword.value) {
                    if (password.value === confirmPassword.value) {
                        confirmPassword.classList.remove('error');
                        confirmPassword.classList.add('success');
                    } else {
                        confirmPassword.classList.remove('success');
                        confirmPassword.classList.add('error');
                    }
                }
            }
            
            password.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
            
            // Character counter for message field
            const messageField = document.getElementById('message');
            if (messageField) {
                const maxLength = <?php echo MAX_MESSAGE_LENGTH; ?>;
                const counterDiv = document.createElement('div');
                counterDiv.style.cssText = 'font-size: 12px; color: #666; text-align: right; margin-top: 5px;';
                messageField.parentNode.appendChild(counterDiv);
                
                function updateCounter() {
                    const remaining = maxLength - messageField.value.length;
                    counterDiv.textContent = `${messageField.value.length}/${maxLength} characters`;
                    counterDiv.style.color = remaining < 50 ? '#e74c3c' : '#666';
                }
                
                messageField.addEventListener('input', updateCounter);
                updateCounter(); // Initialize counter
            }
        });
    </script>
</body>
</html>