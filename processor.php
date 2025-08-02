<?php
require_once 'config.php';
require_once 'Validator.php';

/**
 * Form Processing Script
 * Handles form submission and validation
 */

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'data' => []
];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $formData = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirmPassword' => $_POST['confirmPassword'] ?? '',
            'age' => $_POST['age'] ?? '',
            'country' => $_POST['country'] ?? '',
            'message' => $_POST['message'] ?? ''
        ];
        
        // Create validator instance
        $validator = new Validator($formData);
        
        // Validate all fields
        if ($validator->validateAll()) {
            // All validations passed
            $response['success'] = true;
            $response['message'] = SUCCESS_MESSAGES['form_submitted'];
            $response['data'] = $validator->getSanitizedData();
            
            // Here you could save to database, send email, etc.
            $saved = saveFormData($response['data'], $formData['password']);
            
            if ($saved) {
                $response['message'] = SUCCESS_MESSAGES['data_saved'];
                
                // Clear form data from session (if using sessions to persist data)
                unset($_SESSION['form_data']);
                
                // Log successful submission
                logActivity('Form submitted successfully', $response['data']['email']);
            } else {
                $response['success'] = false;
                $response['message'] = 'Data validation passed but failed to save. Please try again.';
            }
            
        } else {
            // Validation failed
            $response['success'] = false;
            $response['message'] = 'Please fix the errors in the form before submitting.';
            $response['errors'] = $validator->getErrors();
            
            // Store form data in session to repopulate form
            $_SESSION['form_data'] = $validator->getSanitizedData();
            
            // Log validation failure
            logActivity('Form validation failed', $formData['email'] ?? 'unknown');
        }
        
    } catch (Exception $e) {
        // Handle any unexpected errors
        $response['success'] = false;
        $response['message'] = 'An unexpected error occurred. Please try again.';
        
        // Log the error (in production, don't expose internal errors to users)
        error_log('Form processing error: ' . $e->getMessage());
        logActivity('Form processing error: ' . $e->getMessage(), $formData['email'] ?? 'unknown');
    }
} else {
    // Not a POST request
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}

/**
 * Save form data to database or file
 * In a real application, you'd save to a database
 */
function saveFormData($data, $password) {
    try {
        // Hash the password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Create data array for saving
        $saveData = array_merge($data, [
            'password_hash' => $hashedPassword,
            'submitted_at' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        
        // For demo purposes, save to a JSON file
        // In production, use a proper database
        $filename = 'submissions/' . date('Y-m-d') . '_submissions.json';
        
        // Create directory if it doesn't exist
        if (!file_exists('submissions')) {
            mkdir('submissions', 0755, true);
        }
        
        // Read existing data
        $existingData = [];
        if (file_exists($filename)) {
            $existingData = json_decode(file_get_contents($filename), true) ?? [];
        }
        
        // Add new submission
        $existingData[] = $saveData;
        
        // Save to file
        $result = file_put_contents($filename, json_encode($existingData, JSON_PRETTY_PRINT));
        
        return $result !== false;
        
    } catch (Exception $e) {
        error_log('Save data error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Log activity for monitoring and debugging
 */
function logActivity($message, $identifier = '') {
    try {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $message,
            'identifier' => $identifier,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        $logFile = 'logs/activity_' . date('Y-m-d') . '.log';
        
        // Create logs directory if it doesn't exist
        if (!file_exists('logs')) {
            mkdir('logs', 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
        
    } catch (Exception $e) {
        error_log('Logging error: ' . $e->getMessage());
    }
}

// If this is an AJAX request, return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// For non-AJAX requests, redirect back to form with response data
$_SESSION['form_response'] = $response;
header('Location: index.php');
exit;
?>