<?php
require_once 'config.php';
require_once 'Validator.php';

/**
 * AJAX Validation Handler
 * Provides real-time field validation for better UX
 */

// Set JSON response header
header('Content-Type: application/json');

// Initialize response
$response = [
    'valid' => false,
    'message' => '',
    'field' => ''
];

// Check if request is POST and AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    
    $response['message'] = 'Invalid request';
    echo json_encode($response);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['field']) || !isset($input['value'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit;
}

$field = $input['field'];
$value = $input['value'];
$allData = $input['allData'] ?? [];

$response['field'] = $field;

// Create validator instance
$validator = new Validator(array_merge($allData, [$field => $value]));

// Validate specific field
try {
    switch ($field) {
        case 'name':
            $isValid = $validator->validateName();
            break;
            
        case 'email':
            $isValid = $validator->validateEmail();
            // Additional check for existing email if database is available
            if ($isValid && class_exists('Database')) {
                try {
                    $db = Database::getInstance();
                    if ($db->emailExists($value)) {
                        $validator->addError('email', 'Email address already exists');
                        $isValid = false;
                    }
                } catch (Exception $e) {
                    // If database check fails, continue with basic validation
                }
            }
            break;
            
        case 'phone':
            $isValid = $validator->validatePhone();
            break;
            
        case 'password':
            $isValid = $validator->validatePassword();
            break;
            
        case 'confirmPassword':
            // For confirm password, we need the original password
            $password = $allData['password'] ?? '';
            $confirmPassword = $value;
            
            if (empty($confirmPassword)) {
                $validator->addError('confirmPassword', 'Please confirm your password');
                $isValid = false;
            } elseif ($password !== $confirmPassword) {
                $validator->addError('confirmPassword', ERROR_MESSAGES['password_mismatch']);
                $isValid = false;
            } else {
                $isValid = true;
            }
            break;
            
        case 'age':
            $isValid = $validator->validateAge();
            break;
            
        case 'country':
            $isValid = $validator->validateCountry();
            break;
            
        case 'message':
            $isValid = $validator->validateMessage();
            break;
            
        default:
            $response['message'] = 'Unknown field';
            echo json_encode($response);
            exit;
    }
    
    $response['valid'] = $isValid;
    
    if (!$isValid) {
        $errors = $validator->getErrors();
        $response['message'] = $errors[$field] ?? 'Validation failed';
    } else {
        $response['message'] = 'Valid';
    }
    
} catch (Exception $e) {
    $response['valid'] = false;
    $response['message'] = 'Validation error occurred';
    error_log("AJAX validation error for field $field: " . $e->getMessage());
}

echo json_encode($response);
?>