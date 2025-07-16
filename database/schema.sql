-- Journey of Hope Database Schema

CREATE DATABASE IF NOT EXISTS journey_of_hope;
USE journey_of_hope;

-- Admin users table
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    category ENUM('community', 'training', 'advocacy', 'economic', 'partnership', 'transformation', 'rising') NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    featured_image VARCHAR(255),
    author_id INT,
    published_at TIMESTAMP NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_status_published (status, published_at),
    INDEX idx_category (category),
    FULLTEXT(title, content, excerpt)
);

-- Gallery images table
CREATE TABLE gallery_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    width INT,
    height INT,
    category ENUM('rising', 'training', 'community', 'advocacy') NOT NULL,
    alt_text VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_featured (is_featured)
);

-- Sessions table for admin authentication
CREATE TABLE admin_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
);

-- Post views tracking (optional)
CREATE TABLE post_views (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    INDEX idx_post_views (post_id, viewed_at)
);

-- Contact form submissions (bonus feature)
CREATE TABLE contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject ENUM('join-circle', 'volunteer', 'training', 'support', 'partnership', 'one-billion-rising', 'other') NOT NULL,
    region ENUM('hhohho', 'manzini', 'shiselweni', 'lubombo', 'other'),
    message TEXT NOT NULL,
    newsletter_subscribe BOOLEAN DEFAULT FALSE,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_subject (subject)
);

-- Insert default admin user
INSERT INTO admin_users (username, password_hash, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@journeyofhope.org', 'Administrator', 'super_admin');
-- Password is 'journey2024!' - change this in production!

-- Insert sample posts
INSERT INTO posts (title, slug, content, excerpt, category, status, author_id, published_at) VALUES
('One Billion Rising 2024: A Resounding Success Across Eswatini', 'one-billion-rising-2024-success', 
'This year''s One Billion Rising events across all four regions of Eswatini marked a significant milestone in our fight against gender-based violence. Thousands of women and girls rose together, dancing for justice and transformation.\n\nThe energy was palpable as communities from Hhohho to Lubombo, Manzini to Shiselweni united in solidarity. Our 42 community circles came together, showcasing the power of collective action and the resilience of Swazi women.\n\nThe events featured powerful speeches from local leaders, traditional dances infused with messages of empowerment, and moments of profound unity that will be remembered for years to come.',
'Thousands of women and girls rose together, dancing for justice and transformation across all four regions of Eswatini.',
'rising', 'published', 1, '2024-02-14 10:00:00'),

('New Community Circle Launched in Lubombo Region', 'new-community-circle-lubombo',
'We are thrilled to announce the establishment of our newest community circle in the Lubombo region, bringing our total to 42 circles across all four regions of Eswatini.\n\nThis new circle will serve women and girls in the eastern region, providing a safe space for dialogue, support, and empowerment. The launch ceremony was attended by local community leaders, traditional authorities, and dozens of enthusiastic participants.\n\nOur community circles are the heart of our work - spaces where women can engage freely without discrimination or intimidation, where stories are shared, strength is found, and transformation begins.',
'Bringing our total to 42 circles across Eswatini, providing safe spaces for women and girls.',
'community', 'published', 1, '2024-03-15 14:30:00'),

('Feminist Leadership Training Empowers 50 Women', 'feminist-leadership-training-march-2024',
'Our latest feminist leadership training session successfully empowered 50 women with comprehensive knowledge about their rights, governance principles, and leadership strategies.\n\nThe three-day intensive workshop covered topics including:\n- Understanding fundamental human rights\n- Women''s participation in governance\n- Feminist principles and their application\n- Economic empowerment strategies\n- Advocacy and community organizing\n\nParticipants came from all four regions of Eswatini, representing diverse backgrounds and experiences. The training emphasized practical skills that participants can immediately apply in their communities and personal lives.',
'Three-day intensive workshop covering rights, governance, and empowerment strategies.',
'training', 'draft', 1, NULL);

-- Insert sample gallery images (placeholder data)
INSERT INTO gallery_images (title, description, filename, original_filename, file_path, file_size, mime_type, width, height, category, uploaded_by) VALUES
('Manzini Region Rising Event', 'Women and girls dancing for justice and transformation', 'rising_manzini_001.jpg', 'IMG_001.jpg', '/uploads/gallery/rising_manzini_001.jpg', 1048576, 'image/jpeg', 1920, 1080, 'rising', 1),
('Hhohho Region Celebration', 'Community members united in solidarity', 'rising_hhohho_002.jpg', 'IMG_002.jpg', '/uploads/gallery/rising_hhohho_002.jpg', 892342, 'image/jpeg', 1600, 900, 'rising', 1),
('Rights Education Workshop', 'Women learning about their fundamental rights', 'training_rights_001.jpg', 'Workshop_001.jpg', '/uploads/gallery/training_rights_001.jpg', 756483, 'image/jpeg', 1400, 800, 'training', 1),
('Circle Meeting - Lubombo', 'Safe space for women to share and support', 'community_lubombo_001.jpg', 'Circle_001.jpg', '/uploads/gallery/community_lubombo_001.jpg', 654321, 'image/jpeg', 1200, 800, 'community', 1);
