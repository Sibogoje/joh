<?php
// filepath: g:\My Drive\Projects\joh\api\generate_password_hash.php
<?php
// Helper script to generate password hash
$password = 'journey2024!';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\nSQL Update Command:\n";
echo "UPDATE admin_users SET password_hash = '$hash' WHERE username = 'admin';\n";
?>