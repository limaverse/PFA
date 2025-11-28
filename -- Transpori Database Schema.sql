-- Transpori Database Schema
-- Safe Transportation Reporting System for Tunisia

CREATE DATABASE IF NOT EXISTS transpori;
USE transpori;

-- Emergency Contacts Table
CREATE TABLE emergency_contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    description TEXT,
    category ENUM('emergency', 'green_line', 'transport', 'support') DEFAULT 'emergency',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users/Members Table
CREATE TABLE members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    is_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    profile_image VARCHAR(255),
    preferences JSON
);

-- Categories for Reports and Content
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('report', 'experience', 'article') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reports/Incidents Table
CREATE TABLE reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255),
    incident_date DATE,
    incident_time TIME,
    transport_type ENUM('bus', 'train', 'metro', 'taxi', 'light_rail', 'other') DEFAULT 'other',
    is_anonymous BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'under_review', 'resolved', 'rejected') DEFAULT 'pending',
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    evidence_files JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Shared Experiences Table
CREATE TABLE experiences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    location VARCHAR(255),
    transport_type ENUM('bus', 'train', 'metro', 'taxi', 'light_rail', 'other') DEFAULT 'other',
    experience_type ENUM('positive', 'negative', 'suggestion') DEFAULT 'positive',
    is_public BOOLEAN DEFAULT TRUE,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Articles/Blog Posts Table
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    featured_image VARCHAR(255),
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    views_count INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    tags JSON,
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES members(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Events Table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(255),
    organizer VARCHAR(255),
    contact_info VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    max_participants INT,
    registered_participants INT DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Statistics Table
CREATE TABLE statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    statistic_type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    unit VARCHAR(50),
    period ENUM('daily', 'weekly', 'monthly', 'yearly') DEFAULT 'monthly',
    reference_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Comments Table
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    content TEXT NOT NULL,
    parent_type ENUM('experience', 'article') NOT NULL,
    parent_id INT NOT NULL,
    is_approved BOOLEAN DEFAULT TRUE,
    likes_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Likes Table
CREATE TABLE likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    target_type ENUM('experience', 'article', 'comment') NOT NULL,
    target_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (member_id, target_type, target_id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Admins Table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    permissions JSON,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Newsletter Subscriptions Table
CREATE TABLE newsletter_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Initial Data

-- Emergency Contacts
INSERT INTO emergency_contacts (name, phone_number, description, category) VALUES
('Police Secours', '197', 'Police d''urgence', 'emergency'),
('Garde Nationale', '198', 'Garde nationale pour la sécurité', 'emergency'),
('Protection Civile', '198', 'Pompiers et secours d''urgence', 'emergency'),
('Ligne Verte Anti-Corruption', '1899', 'Signalement de corruption', 'green_line'),
('Sécurité des Transports', '1899', 'Problèmes de sécurité dans les transports', 'green_line'),
('SNCFT Info', '30 100', 'Informations ferroviaires', 'transport'),
('TGM Information', '71 447 000', 'Informations ligne TGM', 'transport'),
('Métro Léger', '71 234 000', 'Informations métro léger de Tunis', 'transport');

-- Initial Categories
INSERT INTO categories (name, description, type) VALUES
('Harcèlement', 'Harcèlement dans les transports', 'report'),
('Vol', 'Vol et agression', 'report'),
('Propreté', 'Manque de propreté', 'report'),
('Comportement', 'Mauvais comportement', 'report'),
('Sécurité', 'Problème de sécurité', 'report'),
('Infrastructure', 'Problème d''infrastructure', 'report'),
('Expérience Positive', 'Expériences positives partagées', 'experience'),
('Suggestion', 'Suggestions d''amélioration', 'experience'),
('Alerte', 'Alertes importantes', 'experience'),
('Sécurité', 'Articles sur la sécurité', 'article'),
('Événements', 'Actualités et événements', 'article'),
('Conseils', 'Conseils pratiques', 'article'),
('Législation', 'Réglementation des transports', 'article');

-- Create Default Admin
INSERT INTO admins (username, password_hash, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@transpori.tn', 'Administrateur Principal', 'super_admin');

-- Create Indexes for Performance
CREATE INDEX idx_reports_status ON reports(status);
CREATE INDEX idx_reports_member_id ON reports(member_id);
CREATE INDEX idx_reports_created_at ON reports(created_at);
CREATE INDEX idx_experiences_member_id ON experiences(member_id);
CREATE INDEX idx_experiences_created_at ON experiences(created_at);
CREATE INDEX idx_articles_published ON articles(is_published, published_at);
CREATE INDEX idx_members_email ON members(email);
CREATE INDEX idx_comments_parent ON comments(parent_type, parent_id);

-- Create Views

-- View for Public Experiences
CREATE VIEW public_experiences_view AS
SELECT 
    e.id,
    e.title,
    e.content,
    e.location,
    e.transport_type,
    e.experience_type,
    e.likes_count,
    e.comments_count,
    e.created_at,
    CASE 
        WHEN e.is_public = FALSE THEN 'Anonymous'
        ELSE CONCAT(m.first_name, ' ', m.last_name)
    END as author_name,
    c.name as category_name
FROM experiences e
LEFT JOIN members m ON e.member_id = m.id
LEFT JOIN categories c ON e.category_id = c.id
WHERE e.is_public = TRUE;

-- View for Report Statistics
CREATE VIEW report_statistics_view AS
SELECT 
    c.name as category,
    COUNT(*) as total_reports,
    AVG(CASE WHEN r.status = 'resolved' THEN 1 ELSE 0 END) as resolution_rate,
    AVG(CASE WHEN r.severity = 'critical' THEN 1 ELSE 0 END) as critical_rate
FROM reports r
JOIN categories c ON r.category_id = c.id
GROUP BY c.name;