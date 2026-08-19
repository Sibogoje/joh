<?php
// Helper script to generate a bcrypt password hash for a given password.
// Usage: php generate_password_hash.php

$password = 'admin123'; // Default password; replace or modify as needed.

// Generate the hash.
$hash = password_hash($password, PASSWORD_DEFAULT);

if ($hash === false) {
    die("Error: Failed to generate password hash.");
}

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
?>