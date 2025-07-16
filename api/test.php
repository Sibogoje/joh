<?php
// filepath: g:\My Drive\Projects\joh\api\test.php
// Simple test file to check PHP functionality

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo json_encode([
    'status' => 'success',
    'message' => 'PHP is working',
    'php_version' => phpversion(),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>