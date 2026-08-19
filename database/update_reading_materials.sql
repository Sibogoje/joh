-- Journey of Hope - Reading Materials table
-- Run this on the journey_of_hope database

USE journey_of_hope;

-- Reading materials table
CREATE TABLE IF NOT EXISTS reading_materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    category ENUM('guides', 'reports', 'training', 'stories', 'resources', 'policy', 'other') DEFAULT 'guides',
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    file_name VARCHAR(255),
    file_path VARCHAR(500),
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

-- Insert sample reading materials
INSERT INTO reading_materials (title, slug, content, excerpt, category, status, author_id, published_at) VALUES
('Empowerment Guide for Community Circles', 'empowerment-guide-community-circles',
'This guide provides facilitators with the tools and methods needed to run effective community circles.\n\nInside you will find:\n- Step-by-step circle facilitation techniques\n- Safe space ground rules\n- Discussion prompts for sensitive topics\n- Referral pathways for survivors\n\nThe principles in this guide are grounded in feminist leadership and trauma-informed practice.',
'A practical facilitator guide for running safe, empowering community circles.',
'guides', 'published', 1, '2024-04-01 09:00:00'),

('Annual Impact Report 2023', 'annual-impact-report-2023',
'Our 2023 annual report highlights the achievements and growth of Journey of Hope over the past year.\n\nKey highlights include:\n- Expansion to 42 community circles\n- Reach across all four regions of Eswatini\n- Growth in feminist leadership training\n- New strategic partnerships\n\nDownload the full report to explore our data and stories in detail.',
'A comprehensive overview of our achievements and reach throughout 2023.',
'reports', 'published', 1, '2024-03-01 12:00:00'),

('Trauma-Informed Care Handbook', 'trauma-informed-care-handbook',
'This handbook introduces principles of trauma-informed care for community volunteers.\n\nTopics covered include:\n- Understanding trauma and its effects\n- Creating psychological safety\n- Responding to disclosures\n- Self-care for caregivers\n\nIt is intended as a supportive resource, not a substitute for professional training.',
'An introductory handbook on trauma-informed approaches for volunteers.',
'training', 'draft', 1, NULL);
