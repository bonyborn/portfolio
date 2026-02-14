-- =============================================
-- Database: personal_profile_db
-- Description: Database for personal profile website with mentoring program
-- =============================================

-- Create the database
CREATE DATABASE IF NOT EXISTS personal_profile_db;
USE personal_profile_db;

-- =============================================
-- Table: users (for admin/portfolio owner)
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    title VARCHAR(100),
    bio TEXT,
    profile_image_url VARCHAR(255),
    phone VARCHAR(20),
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- Table: portfolio_projects
-- =============================================
CREATE TABLE IF NOT EXISTS portfolio_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    short_description VARCHAR(500),
    image_url VARCHAR(255) NOT NULL,
    project_url VARCHAR(255) NOT NULL,
    technologies VARCHAR(500), -- Comma-separated or JSON
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- Table: skills
-- =============================================
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL,
    proficiency_level INT NOT NULL CHECK (proficiency_level >= 0 AND proficiency_level <= 100),
    skill_type ENUM('technical', 'soft', 'tool') DEFAULT 'technical',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Table: mentoring_programs
-- =============================================
CREATE TABLE IF NOT EXISTS mentoring_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_name VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    duration_months INT NOT NULL,
    price_monthly DECIMAL(10, 2) NOT NULL,
    price_full DECIMAL(10, 2) NOT NULL,
    highlights JSON, -- Store program highlights as JSON array
    status ENUM('active', 'inactive', 'full') DEFAULT 'active',
    max_participants INT,
    current_participants INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- Table: program_benefits
-- =============================================
CREATE TABLE IF NOT EXISTS program_benefits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    benefit_text TEXT NOT NULL,
    display_order INT DEFAULT 0,
    FOREIGN KEY (program_id) REFERENCES mentoring_programs(id) ON DELETE CASCADE
);

-- =============================================
-- Table: mentoring_registrations
-- =============================================
CREATE TABLE IF NOT EXISTS mentoring_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    experience_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    focus_area ENUM('frontend', 'backend', 'fullstack', 'mobile', 'other') NOT NULL,
    learning_goals TEXT,
    preferred_schedule ENUM('weekday_mornings', 'weekday_evenings', 'weekends', 'flexible') NOT NULL,
    referral_source VARCHAR(200),
    agreed_to_terms BOOLEAN DEFAULT FALSE,
    subscribe_newsletter BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected', 'completed') DEFAULT 'pending',
    notes TEXT,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (program_id) REFERENCES mentoring_programs(id) ON DELETE CASCADE
);

-- =============================================
-- Table: contact_messages
-- =============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    responded BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL
);

-- =============================================
-- Table: testimonials
-- =============================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    person_name VARCHAR(100) NOT NULL,
    person_title VARCHAR(100),
    person_company VARCHAR(100),
    content TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    approved BOOLEAN DEFAULT FALSE,
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Table: social_links
-- =============================================
CREATE TABLE IF NOT EXISTS social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform_name VARCHAR(50) NOT NULL,
    platform_icon VARCHAR(50), -- FontAwesome class
    profile_url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);

-- =============================================
-- Table: site_settings
-- =============================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description VARCHAR(255)
);

-- =============================================
-- Table: newsletter_subscribers
-- =============================================
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    unsubscribed_at TIMESTAMP NULL
);

-- =============================================
-- Table: blog_posts (for future expansion)
-- =============================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    author_id INT,
    featured_image VARCHAR(255),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- Table: blog_categories
-- =============================================
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
);

-- =============================================
-- Table: blog_post_categories (many-to-many)
-- =============================================
CREATE TABLE IF NOT EXISTS blog_post_categories (
    post_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE CASCADE
);

-- =============================================
-- Insert Initial Data
-- =============================================

-- Insert admin user (password: Admin123! - hashed with bcrypt)
INSERT INTO users (username, email, password_hash, full_name, title, bio, profile_image_url, phone, location) 
VALUES (
    'Boniface Nzau', 
    'nzauboniface76@gmail.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- bcrypt hash for "Admin123!"
    'Alex Morgan', 
    'Full-Stack Developer & Mentor',
    'Passionate about creating elegant web solutions and empowering the next generation of developers through personalized mentoring and tutoring programs.',
    'c:\users\USER\Desktop\PROJECT\static\bonie.png',
    '+254 (759-919-826)',
    'Mwingi, Kitui'
);

-- Insert skills
INSERT INTO skills (skill_name, proficiency_level, skill_type, display_order) VALUES
('HTML/CSS', 95, 'technical', 1),
('JavaScript', 90, 'technical', 2),
('React', 85, 'technical', 3),
('Node.js', 80, 'technical', 4),
('UI/UX Design', 75, 'technical', 5),
('Python', 70, 'technical', 6),
('Database Design', 85, 'technical', 7),
('Git & Version Control', 90, 'tool', 8),
('Communication', 95, 'soft', 9),
('Mentoring', 90, 'soft', 10);

-- Insert portfolio projects
INSERT INTO portfolio_projects (title, description, short_description, image_url, project_url, technologies, featured, display_order) VALUES
(
    'EcoStore E-commerce', 
    'A fully responsive e-commerce website for sustainable products with shopping cart, payment gateway, and admin dashboard. Built with React, Node.js, and MongoDB.', 
    'Sustainable e-commerce platform with full shopping functionality',
    'https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1074&q=80',
    'https://www.example-ecostore.com',
    'React, Node.js, MongoDB, Stripe API',
    TRUE,
    1
),
(
    'HealthTrack Fitness App', 
    'A comprehensive fitness tracking application with workout plans, nutrition logging, and progress analytics. Features include workout scheduling, calorie tracking, and progress visualization.',
    'Fitness tracking app with workout and nutrition features',
    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80',
    'https://www.example-healthtrack.com',
    'React Native, Firebase, Redux',
    TRUE,
    2
),
(
    'FinancePro Dashboard', 
    'An interactive financial dashboard for personal finance management with visualization tools and budget planning. Includes expense tracking, investment monitoring, and financial goal setting.',
    'Financial dashboard for personal finance management',
    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1115&q=80',
    'https://www.example-financepro.com',
    'Vue.js, Django, Chart.js, PostgreSQL',
    TRUE,
    3
),
(
    'LearnCode Platform', 
    'An online learning platform for coding with interactive lessons, coding challenges, and progress tracking. Supports multiple programming languages with real-time code execution.',
    'Interactive coding learning platform',
    'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80',
    'https://www.example-learncode.com',
    'Next.js, TypeScript, Docker, AWS',
    TRUE,
    4
);

-- Insert mentoring program
INSERT INTO mentoring_programs (program_name, description, duration_months, price_monthly, price_full, highlights, status, max_participants, current_participants) VALUES
(
    'Developer Accelerator Program',
    'A comprehensive 3-month program designed to transform beginners into job-ready developers. Includes one-on-one mentoring, project building, and career guidance.',
    3,
    299.00,
    799.00,
    '["One-on-one weekly mentoring sessions", "Personalized learning path", "Code reviews and portfolio building", "Interview preparation", "Access to exclusive resources", "Monthly group workshops"]',
    'active',
    20,
    8
);

-- Insert program benefits
INSERT INTO program_benefits (program_id, benefit_text, display_order) VALUES
(1, 'One-on-one weekly mentoring sessions', 1),
(1, 'Personalized learning path based on your goals', 2),
(1, 'Code reviews and portfolio building', 3),
(1, 'Interview preparation and career guidance', 4),
(1, 'Access to exclusive project resources', 5),
(1, 'Monthly group workshops and Q&A sessions', 6);

-- Insert social links
INSERT INTO social_links (platform_name, platform_icon, profile_url, display_order, is_active) VALUES
('GitHub', 'fab fa-github', 'https://github.com/nzauboniface-svg76/Nuu', 1, TRUE),
('LinkedIn', 'fab fa-linkedin-in', 'https://www.linkedin.com/jobs/', 2, TRUE),
('Twitter', 'fab fa-twitter', 'https://x.com/home', 3, TRUE),
('YouTube', 'fab fa-youtube', 'https://www.youtube.com/@BONIFACENZAU-j5m', 4, TRUE),
('Codepen', 'fab fa-codepen', 'https://codepen.io/pen?welcome=true', 5, TRUE);

-- Insert testimonials
INSERT INTO testimonials (person_name, person_title, person_company, content, rating, approved, featured, display_order) VALUES
(
    'John Paul',
    'Frontend Developer',
    'TechCorp Inc.',
    'Nzau's 's mentoring program transformed my career. Within 3 months, I went from knowing basics to landing my first developer job. The personalized approach made all the difference!',
    5,
    TRUE,
    TRUE,
    1
),
(
    'Michael Muthui',
    'Full-Stack Developer',
    'StartupXYZ',
    'The portfolio review session was incredibly valuable. Boniface pointed out improvements I never would have thought of. My GitHub profile looks professional now!',
    5,
    TRUE,
    TRUE,
    2
),
(
    'Willis',
    'Career Changer',
    '',
    'As someone switching careers into tech, I was overwhelmed. Boniface broke everything down into manageable steps and provided constant support. Highly recommended!',
    5,
    TRUE,
    TRUE,
    3
);

-- Insert site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('site_title', 'Boniface Nzau | Developer & Mentor', 'string', 'Website title'),
('site_tagline', 'Empowering developers through mentorship', 'string', 'Website tagline'),
('contact_email', 'nzauboniface76@gmail.com', 'string', 'Primary contact email'),
('mentoring_active', 'true', 'boolean', 'Whether mentoring program is accepting applications'),
('max_registrations_per_month', '25', 'number', 'Maximum registrations to accept per month'),
('newsletter_signup_enabled', 'true', 'boolean', 'Whether newsletter signup is enabled'),
('maintenance_mode', 'false', 'boolean', 'Whether site is in maintenance mode'),
('google_analytics_id', '', 'string', 'Google Analytics tracking ID');

-- =============================================
-- Create Views for Common Queries
-- =============================================

-- View for active mentoring program details
CREATE OR REPLACE VIEW active_mentoring_programs AS
SELECT 
    mp.*,
    COUNT(DISTINCT mr.id) as total_applications,
    COUNT(DISTINCT CASE WHEN mr.status = 'accepted' THEN mr.id END) as accepted_applications
FROM mentoring_programs mp
LEFT JOIN mentoring_registrations mr ON mp.id = mr.program_id
WHERE mp.status = 'active'
GROUP BY mp.id;

-- View for dashboard statistics
CREATE OR REPLACE VIEW dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM mentoring_registrations) as total_registrations,
    (SELECT COUNT(*) FROM mentoring_registrations WHERE status = 'pending') as pending_registrations,
    (SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE) as unread_messages,
    (SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active = TRUE) as newsletter_subscribers,
    (SELECT COUNT(*) FROM portfolio_projects) as total_projects,
    (SELECT COUNT(*) FROM testimonials WHERE approved = TRUE) as approved_testimonials;

-- =============================================
-- Create Stored Procedures
-- =============================================

DELIMITER //

-- Procedure to get mentor profile with statistics
CREATE PROCEDURE GetMentorProfile(IN mentor_id INT)
BEGIN
    SELECT 
        u.*,
        (SELECT COUNT(*) FROM mentoring_registrations mr 
         JOIN mentoring_programs mp ON mr.program_id = mp.id 
         WHERE mp.status = 'active') as total_students,
        (SELECT AVG(rating) FROM testimonials WHERE approved = TRUE) as avg_rating,
        (SELECT COUNT(*) FROM portfolio_projects WHERE featured = TRUE) as featured_projects
    FROM users u
    WHERE u.id = mentor_id;
END //

-- Procedure to register for mentoring program
CREATE PROCEDURE RegisterForMentoring(
    IN p_program_id INT,
    IN p_full_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_experience_level ENUM('beginner', 'intermediate', 'advanced'),
    IN p_focus_area ENUM('frontend', 'backend', 'fullstack', 'mobile', 'other'),
    IN p_learning_goals TEXT,
    IN p_preferred_schedule ENUM('weekday_mornings', 'weekday_evenings', 'weekends', 'flexible'),
    IN p_agreed_to_terms BOOLEAN,
    IN p_subscribe_newsletter BOOLEAN
)
BEGIN
    DECLARE program_active BOOLEAN;
    DECLARE current_count INT;
    DECLARE max_count INT;
    
    -- Check if program is active
    SELECT status = 'active', current_participants, max_participants 
    INTO program_active, current_count, max_count
    FROM mentoring_programs 
    WHERE id = p_program_id;
    
    IF NOT program_active THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Program is not active';
    ELSEIF max_count IS NOT NULL AND current_count >= max_count THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Program is full';
    ELSEIF NOT p_agreed_to_terms THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Terms must be agreed to';
    ELSE
        -- Insert registration
        INSERT INTO mentoring_registrations (
            program_id, full_name, email, phone, experience_level, 
            focus_area, learning_goals, preferred_schedule, 
            agreed_to_terms, subscribe_newsletter
        ) VALUES (
            p_program_id, p_full_name, p_email, p_phone, p_experience_level,
            p_focus_area, p_learning_goals, p_preferred_schedule,
            p_agreed_to_terms, p_subscribe_newsletter
        );
        
        -- Update participant count if accepted immediately
        UPDATE mentoring_programs 
        SET current_participants = current_participants + 1 
        WHERE id = p_program_id;
        
        -- If subscribed to newsletter, add to newsletter list
        IF p_subscribe_newsletter THEN
            INSERT IGNORE INTO newsletter_subscribers (email, name)
            VALUES (p_email, p_full_name);
        END IF;
    END IF;
END //

-- Procedure to get monthly registration statistics
CREATE PROCEDURE GetMonthlyRegistrationStats(IN year_param INT)
BEGIN
    SELECT 
        MONTH(application_date) as month,
        COUNT(*) as total_registrations,
        COUNT(CASE WHEN experience_level = 'beginner' THEN 1 END) as beginners,
        COUNT(CASE WHEN experience_level = 'intermediate' THEN 1 END) as intermediate,
        COUNT(CASE WHEN experience_level = 'advanced' THEN 1 END) as advanced,
        COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending
    FROM mentoring_registrations
    WHERE YEAR(application_date) = year_param
    GROUP BY MONTH(application_date)
    ORDER BY month;
END //

DELIMITER ;

-- =============================================
-- Create Triggers
-- =============================================

DELIMITER //

-- Trigger to update program participant count when registration status changes
CREATE TRIGGER UpdateParticipantCount
AFTER UPDATE ON mentoring_registrations
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status AND NEW.status = 'accepted' THEN
        UPDATE mentoring_programs 
        SET current_participants = current_participants + 1 
        WHERE id = NEW.program_id;
    END IF;
    
    IF OLD.status = 'accepted' AND NEW.status != 'accepted' THEN
        UPDATE mentoring_programs 
        SET current_participants = current_participants - 1 
        WHERE id = NEW.program_id;
    END IF;
END //

-- Trigger to add timestamp when contact message is responded to
CREATE TRIGGER UpdateContactMessageResponse
BEFORE UPDATE ON contact_messages
FOR EACH ROW
BEGIN
    IF NEW.responded = TRUE AND OLD.responded = FALSE THEN
        SET NEW.responded_at = CURRENT_TIMESTAMP;
    END IF;
END //

DELIMITER ;

-- =============================================
-- Create Indexes for Performance
-- =============================================

-- Indexes for mentoring_registrations
CREATE INDEX idx_registrations_email ON mentoring_registrations(email);
CREATE INDEX idx_registrations_status ON mentoring_registrations(status);
CREATE INDEX idx_registrations_date ON mentoring_registrations(application_date);
CREATE INDEX idx_registrations_program ON mentoring_registrations(program_id);

-- Indexes for contact_messages
CREATE INDEX idx_contact_email ON contact_messages(email);
CREATE INDEX idx_contact_read ON contact_messages(is_read);
CREATE INDEX idx_contact_date ON contact_messages(created_at);

-- Indexes for portfolio_projects
CREATE INDEX idx_portfolio_featured ON portfolio_projects(featured);
CREATE INDEX idx_portfolio_order ON portfolio_projects(display_order);

-- Indexes for blog posts
CREATE INDEX idx_blog_status ON blog_posts(status);
CREATE INDEX idx_blog_published ON blog_posts(published_at);
CREATE INDEX idx_blog_slug ON blog_posts(slug);

-- =============================================
-- Sample Data Inserts for Testing
-- =============================================

-- Insert sample mentoring registrations
INSERT INTO mentoring_registrations (program_id, full_name, email, phone, experience_level, focus_area, learning_goals, preferred_schedule, agreed_to_terms, subscribe_newsletter, status) VALUES
(1, 'John paul', 'john@example.com', '+1234567890', 'beginner', 'frontend', 'Learn React and build a portfolio', 'weekday_evenings', TRUE, TRUE, 'accepted'),
(1, 'Jane kyalo', 'jane@example.com', '+0987654321', 'intermediate', 'fullstack', 'Master backend development with Node.js', 'weekends', TRUE, FALSE, 'pending'),
(1, 'Robert kamau', 'robert@example.com', '+1122334455', 'beginner', 'mobile', 'Learn React Native for mobile apps', 'flexible', TRUE, TRUE, 'accepted');

-- Insert sample contact messages
INSERT INTO contact_messages (name, email, subject, message, is_read, responded) VALUES
('Alice ochieng', 'alice@example.com', 'Consultation Inquiry', 'I would like to schedule a consultation about your mentoring program.', TRUE, TRUE),
('Bob Wekesa', 'bob@example.com', 'Project Collaboration', 'I have a project idea and would like to discuss potential collaboration.', FALSE, FALSE);

-- Insert newsletter subscribers
INSERT INTO newsletter_subscribers (email, name) VALUES
('subscriber1@example.com', 'Subscriber One'),
('subscriber2@example.com', 'Subscriber Two');

-- =============================================
-- Database User Creation (for application access)
-- =============================================

-- Create a dedicated database user for the application
CREATE USER IF NOT EXISTS 'profile_app'@'localhost' IDENTIFIED BY 'SecurePassword123!';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON personal_profile_db.* TO 'profile_app'@'localhost';
FLUSH PRIVILEGES;

-- =============================================
-- Export Commands
-- =============================================

-- To export the database structure and data:
-- mysqldump -u root -p personal_profile_db > personal_profile_db_backup.sql

-- To import the database:
-- mysql -u root -p personal_profile_db < personal_profile_db_backup.sql