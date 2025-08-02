<?php
/**
 * Setup Script for PHP Form Validator
 * Run this script once to set up the application
 */

require_once 'config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Form Validator - Setup</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
        }
        
        .section h2 {
            color: #555;
            margin-top: 0;
        }
        
        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border-left: 4px solid #667eea;
        }
        
        .button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
        }
        
        .button:hover {
            opacity: 0.9;
        }
        
        .file-list {
            list-style: none;
            padding: 0;
        }
        
        .file-list li {
            padding: 8px;
            margin: 5px 0;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #28a745;
        }
        
        .file-list li.optional {
            border-left-color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP Form Validator - Setup</h1>
        
        <div class="section">
            <h2>1. System Requirements Check</h2>
            
            <?php
            $requirements = [
                'PHP Version' => [
                    'required' => '7.4',
                    'current' => PHP_VERSION,
                    'check' => version_compare(PHP_VERSION, '7.4.0', '>=')
                ],
                'PDO Extension' => [
                    'required' => 'Enabled',
                    'current' => extension_loaded('pdo') ? 'Enabled' : 'Disabled',
                    'check' => extension_loaded('pdo')
                ],
                'PDO MySQL Extension' => [
                    'required' => 'Enabled',
                    'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
                    'check' => extension_loaded('pdo_mysql')
                ],
                'JSON Extension' => [
                    'required' => 'Enabled',
                    'current' => extension_loaded('json') ? 'Enabled' : 'Disabled',
                    'check' => extension_loaded('json')
                ]
            ];
            
            $allRequirementsMet = true;
            
            foreach ($requirements as $name => $req) {
                $status = $req['check'] ? 'success' : 'error';
                if (!$req['check']) $allRequirementsMet = false;
                
                echo "<div class='status $status'>";
                echo "<strong>$name:</strong> Required: {$req['required']}, Current: {$req['current']}";
                echo "</div>";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>2. Directory Permissions Check</h2>
            
            <?php
            $directories = ['submissions', 'logs'];
            
            foreach ($directories as $dir) {
                if (!file_exists($dir)) {
                    $created = @mkdir($dir, 0755, true);
                    if ($created) {
                        echo "<div class='status success'>Directory '$dir' created successfully</div>";
                    } else {
                        echo "<div class='status error'>Failed to create directory '$dir'</div>";
                        $allRequirementsMet = false;
                    }
                } else {
                    if (is_writable($dir)) {
                        echo "<div class='status success'>Directory '$dir' is writable</div>";
                    } else {
                        echo "<div class='status error'>Directory '$dir' is not writable</div>";
                        $allRequirementsMet = false;
                    }
                }
            }
            ?>
        </div>
        
        <div class="section">
            <h2>3. Database Setup (Optional)</h2>
            
            <?php if (isset($_POST['setup_database'])): ?>
                <?php
                try {
                    require_once 'Database.php';
                    $db = Database::getInstance();
                    
                    // Create tables
                    $tablesCreated = $db->createUsersTable();
                    
                    if ($tablesCreated) {
                        echo "<div class='status success'>Database tables created successfully!</div>";
                    } else {
                        echo "<div class='status error'>Failed to create database tables</div>";
                    }
                    
                } catch (Exception $e) {
                    echo "<div class='status error'>Database setup failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                    echo "<div class='status warning'>You can still use the form validator with file-based storage</div>";
                }
                ?>
            <?php else: ?>
                <p>The form validator can work with or without a database:</p>
                <ul>
                    <li><strong>With Database:</strong> Data is stored in MySQL database with better performance and features</li>
                    <li><strong>Without Database:</strong> Data is stored in JSON files (good for testing)</li>
                </ul>
                
                <p>To set up the database, first update the database configuration in <code>config.php</code>:</p>
                <pre>define('DB_HOST', 'your_host');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');</pre>
                
                <form method="post">
                    <button type="submit" name="setup_database" class="button">Setup Database Tables</button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>4. File Structure</h2>
            
            <p>Your form validator should have the following files:</p>
            
            <ul class="file-list">
                <li><strong>config.php</strong> - Configuration file</li>
                <li><strong>Validator.php</strong> - Validation class</li>
                <li><strong>process_form.php</strong> - Form processing script</li>
                <li><strong>index.php</strong> - Main form page</li>
                <li><strong>ajax_validate.php</strong> - AJAX validation handler</li>
                <li class="optional"><strong>Database.php</strong> - Database handler (optional)</li>
                <li class="optional"><strong>setup.php</strong> - This setup script (optional)</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>5. Security Recommendations</h2>
            
            <div class="status warning">
                <strong>Important Security Notes:</strong>
                <ul>
                    <li>Change default database credentials in <code>config.php</code></li>
                    <li>Set proper file permissions (644 for files, 755 for directories)</li>
                    <li>Use HTTPS in production</li>
                    <li>Regularly update PHP and dependencies</li>
                    <li>Consider implementing rate limiting for form submissions</li>
                    <li>Remove or restrict access to <code>setup.php</code> after setup</li>
                </ul>
            </div>
        </div>
        
        <div class="section">
            <h2>6. Testing</h2>
            
            <?php if ($allRequirementsMet): ?>
                <div class="status success">
                    <strong>Setup Complete!</strong> All requirements are met.
                </div>
                
                <p>You can now test your form validator:</p>
                <a href="index.php" class="button">Test Form Validator</a>
                
            <?php else: ?>
                <div class="status error">
                    <strong>Setup Incomplete!</strong> Please fix the issues above before proceeding.
                </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>7. Usage Examples</h2>
            
            <h3>Basic Usage:</h3>
            <pre>&lt;?php
require_once 'Validator.php';

$data = $_POST; // or any array of form data
$validator = new Validator($data);

if ($validator->validateAll()) {
    // All validations passed
    $cleanData = $validator->getSanitizedData();
    // Process the data...
} else {
    // Show errors
    $errors = $validator->getErrors();
}
?&gt;</pre>
            
            <h3>Individual Field Validation:</h3>
            <pre>&lt;?php
$validator = new Validator(['email' => $email]);
if ($validator->validateEmail()) {
    echo "Email is valid";
} else {
    echo $validator->getError('email');
}
?&gt;</pre>
        </div>
    </div>
</body>
</html>