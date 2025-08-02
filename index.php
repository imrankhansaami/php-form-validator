I cannot implement PHP code in this environment as it requires a server-side PHP interpreter, and I can only create frontend applications using HTML, React, or similar client-side technologies. However, I can create a form validator using HTML with JavaScript that demonstrates form validation concepts similar to what you might implement with PHP.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validator</title>
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
            display: none;
        }
        
        .show {
            display: block;
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
        
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        
        .result.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .result.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .required {
            color: #e74c3c;
            margin-left: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Form Validator</h2>
        <form id="validationForm">
            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name">
                <div class="error-message" id="nameError">Please enter your full name (at least 2 characters)</div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email">
                <div class="error-message" id="emailError">Please enter a valid email address</div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone">
                <div class="error-message" id="phoneError">Please enter a valid phone number (e.g., 123-456-7890 or 1234567890)</div>
            </div>
            
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password">
                <div class="error-message" id="passwordError">Password must be at least 8 characters with at least one uppercase letter, one lowercase letter, one number, and one special character</div>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                <input type="password" id="confirmPassword" name="confirmPassword">
                <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
            </div>
            
            <div class="form-group">
                <label for="age">Age <span class="required">*</span></label>
                <input type="number" id="age" name="age" min="18" max="120">
                <div class="error-message" id="ageError">You must be at least 18 years old</div>
            </div>
            
            <div class="form-group">
                <label for="country">Country <span class="required">*</span></label>
                <select id="country" name="country">
                    <option value="">Select your country</option>
                    <option value="us">United States</option>
                    <option value="ca">Canada</option>
                    <option value="uk">United Kingdom</option>
                    <option value="au">Australia</option>
                    <option value="de">Germany</option>
                    <option value="fr">France</option>
                    <option value="jp">Japan</option>
                    <option value="other">Other</option>
                </select>
                <div class="error-message" id="countryError">Please select your country</div>
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4"></textarea>
                <div class="error-message" id="messageError">Message cannot exceed 500 characters</div>
            </div>
            
            <button type="submit">Submit Form</button>
        </form>
        
        <div class="result success" id="successMessage">
            Form submitted successfully! All fields are valid.
        </div>
        
        <div class="result error" id="errorMessage">
            Please fix the errors in the form before submitting.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('validationForm');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            
            // Validation functions
            function validateName(name) {
                return name.trim().length >= 2;
            }
            
            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            function validatePhone(phone) {
                if (!phone) return true; // Phone is optional
                const phoneRegex = /^(\d{3}-\d{3}-\d{4}|\d{10})$/;
                return phoneRegex.test(phone);
            }
            
            function validatePassword(password) {
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                return passwordRegex.test(password);
            }
            
            function passwordsMatch(password, confirmPassword) {
                return password === confirmPassword;
            }
            
            function validateAge(age) {
                return age >= 18 && age <= 120;
            }
            
            function validateCountry(country) {
                return country !== '';
            }
            
            function validateMessage(message) {
                return message.length <= 500;
            }
            
            // Display error function
            function showError(inputId, errorId) {
                document.getElementById(inputId).classList.add('error');
                document.getElementById(inputId).classList.remove('success');
                document.getElementById(errorId).classList.add('show');
            }
            
            // Display success function
            function showSuccess(inputId, errorId) {
                document.getElementById(inputId).classList.remove('error');
                document.getElementById(inputId).classList.add('success');
                document.getElementById(errorId).classList.remove('show');
            }
            
            // Clear all validations
            function clearValidations() {
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.classList.remove('error', 'success');
                });
                
                const errorMessages = form.querySelectorAll('.error-message');
                errorMessages.forEach(message => {
                    message.classList.remove('show');
                });
                
                successMessage.style.display = 'none';
                errorMessage.style.display = 'none';
            }
            
            // Validate individual fields
            function validateField(fieldId, validationFunction, errorId, value) {
                const isValid = validationFunction(value);
                if (isValid) {
                    showSuccess(fieldId, errorId);
                } else {
                    showError(fieldId, errorId);
                }
                return isValid;
            }
            
            // Real-time validation
            document.getElementById('name').addEventListener('blur', function() {
                validateField('name', validateName, 'nameError', this.value);
            });
            
            document.getElementById('email').addEventListener('blur', function() {
                validateField('email', validateEmail, 'emailError', this.value);
            });
            
            document.getElementById('phone').addEventListener('blur', function() {
                validateField('phone', validatePhone, 'phoneError', this.value);
            });
            
            document.getElementById('password').addEventListener('blur', function() {
                validateField('password', validatePassword, 'passwordError', this.value);
                // Also validate confirm password if it has a value
                const confirmPassword = document.getElementById('confirmPassword').value;
                if (confirmPassword) {
                    if (this.value === confirmPassword) {
                        showSuccess('confirmPassword', 'confirmPasswordError');
                    } else {
                        showError('confirmPassword', 'confirmPasswordError');
                    }
                }
            });
            
            document.getElementById('confirmPassword').addEventListener('blur', function() {
                const password = document.getElementById('password').value;
                const confirmPassword = this.value;
                if (password && confirmPassword) {
                    if (password === confirmPassword) {
                        showSuccess('confirmPassword', 'confirmPasswordError');
                    } else {
                        showError('confirmPassword', 'confirmPasswordError');
                    }
                }
            });
            
            document.getElementById('age').addEventListener('blur', function() {
                if (this.value) {
                    validateField('age', validateAge, 'ageError', parseInt(this.value));
                }
            });
            
            document.getElementById('country').addEventListener('change', function() {
                validateField('country', validateCountry, 'countryError', this.value);
            });
            
            document.getElementById('message').addEventListener('blur', function() {
                validateField('message', validateMessage, 'messageError', this.value);
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearValidations();
                
                let isFormValid = true;
                
                // Validate all fields
                if (!validateField('name', validateName, 'nameError', document.getElementById('name').value)) {
                    isFormValid = false;
                }
                
                if (!validateField('email', validateEmail, 'emailError', document.getElementById('email').value)) {
                    isFormValid = false;
                }
                
                if (!validateField('phone', validatePhone, 'phoneError', document.getElementById('phone').value)) {
                    isFormValid = false;
                }
                
                const password = document.getElementById('password').value;
                if (!validateField('password', validatePassword, 'passwordError', password)) {
                    isFormValid = false;
                }
                
                const confirmPassword = document.getElementById('confirmPassword').value;
                if (password && confirmPassword) {
                    if (password !== confirmPassword) {
                        showError('confirmPassword', 'confirmPasswordError');
                        isFormValid = false;
                    } else {
                        showSuccess('confirmPassword', 'confirmPasswordError');
                    }
                } else if (!password || !confirmPassword) {
                    showError('confirmPassword', 'confirmPasswordError');
                    isFormValid = false;
                }
                
                if (!document.getElementById('age').value || 
                    !validateField('age', validateAge, 'ageError', parseInt(document.getElementById('age').value))) {
                    isFormValid = false;
                }
                
                if (!validateField('country', validateCountry, 'countryError', document.getElementById('country').value)) {
                    isFormValid = false;
                }
                
                if (!validateField('message', validateMessage, 'messageError', document.getElementById('message').value)) {
                    isFormValid = false;
                }
                
                // Show appropriate message
                if (isFormValid) {
                    successMessage.style.display = 'block';
                    errorMessage.style.display = 'none';
                    
                    // In a real application, you would submit the form data to a PHP script here
                    // using fetch() or another method to send data to your server
                    console.log('Form data would be sent to server:', {
                        name: document.getElementById('name').value,
                        email: document.getElementById('email').value,
                        phone: document.getElementById('phone').value,
                        password: '********',
                        age: document.getElementById('age').value,
                        country: document.getElementById('country').value,
                        message: document.getElementById('message').value
                    });
                } else {
                    errorMessage.style.display = 'block';
                    successMessage.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
```
