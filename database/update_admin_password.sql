-- Update admin password to use correct hash for 'journey2024!'
USE journey_of_hope;

-- Update the admin user with the correct password hash
UPDATE admin_users 
SET password_hash = '$2y$10$YourHashedPasswordHere' 
WHERE username = 'admin';

-- Alternatively, you can run this PHP script to generate the correct hash:
-- <?php echo password_hash('journey2024!', PASSWORD_DEFAULT); ?>
