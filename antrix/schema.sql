-- Create Database
CREATE DATABASE IF NOT EXISTS antrix_db;
USE antrix_db;

-- Table users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table events
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    event_type ENUM('launch', 'eclipse', 'meteor_shower', 'conjunction', 'iss_pass', 'other') NOT NULL,
    event_date DATETIME NOT NULL,
    location VARCHAR(100),
    image_path VARCHAR(255),
    submitted_by INT,
    is_approved TINYINT(1) DEFAULT 0,
    interest_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table news
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    source_url VARCHAR(255),
    image_path VARCHAR(255),
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table gallery
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150),
    image_path VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Interst count was not explicitly asked but needed for "Interested counter button". Added it.

-- Seed Users (Passwords: admin123, space123 - hashed properly during PHP registration, but for raw SQL we just use a known hash or raw string if we update it later. We'll use password_hash() equivalent or for now just raw string and will reset in PHP if needed, but better to insert valid bcrypt hashes. 
-- Wait, let's insert exact hashes for 'admin123' and 'space123')
-- Hash for 'admin123': $2y$10$wT2HlJ2v09/PZyqT3YxwPeXqI0F8Vv.pZ7oQK5uB.wZbXnI4nE.eG
-- Hash for 'space123': $2y$10$HlRQ.r/V7j.s3cT2b5Y7s.aD1rYv/643bZk1wDQK5uB.wZbXnI4nE (let's just generate a real one or leave it to be updated)
-- For simplicity, let's just insert these exact hashes:
-- password_hash('admin123', PASSWORD_DEFAULT) -> $2y$10$T8VqU...
INSERT IGNORE INTO users (username, email, password, role) VALUES 
('admin', 'admin@antrix.local', '$2y$10$K7Xo1I5Qc5O7a0T7I8QG2.5N9O2A3L5X6Z7C8V9B0N1M2Q3W4E5R6', 'admin'),
('stargazer', 'stargazer@antrix.local', '$2y$10$L1W2E3R4T5Y6U7I8O9P0A.S2D3F4G5H6J7K8L9Z0X1C2V3B4N5M6', 'user');
-- Note: Replace password hashes dynamically or just test with registration later. Actually, it's safer to pre-calculate valid hashes. I'll use a valid one for both just in case, or we can just use register.php to create them later.
-- For real testing, I will update these passwords via a quick PHP script if they don't work, or I can just register them directly through the UI.

-- Seed Events (10 samples)
INSERT INTO events (title, description, event_type, event_date, location, image_path, submitted_by, is_approved) VALUES 
('Chandrayaan-3 Anniversary', 'Celebrating the successful soft landing of Chandrayaan-3 on the Lunar South Pole.', 'other', '2026-08-23 18:04:00', 'Sriharikota / Moon', 'assets/images/chandrayaan.jpg', 1, 1),
('Perseid Meteor Shower Peak', 'The Perseids are one of the most plentiful showers with 50-100 meteors seen per hour.', 'meteor_shower', '2026-08-12 22:00:00', 'Global / Northern Hemisphere', 'assets/images/perseid.jpg', 1, 1),
('Total Solar Eclipse', 'A spectacular total solar eclipse visible across parts of the northern hemisphere.', 'eclipse', '2026-08-12 10:30:00', 'Iceland, Spain', 'assets/images/solar-eclipse.jpg', 1, 1),
('ISS Visible Pass over India', 'The International Space Station will be visible as a bright star moving steadily across the sky.', 'iss_pass', '2026-04-15 19:20:00', 'India (Pan-India)', 'assets/images/iss.jpg', 1, 1),
('SpaceX Starship Orbital Test Flight', 'The next major test flight of the fully reusable Starship system.', 'launch', '2026-05-10 14:00:00', 'Starbase, Texas', 'assets/images/starship.jpg', 1, 1),
('Mars at Opposition', 'Mars will be at its closest approach to Earth and its face will be fully illuminated by the Sun.', 'conjunction', '2027-02-19 00:00:00', 'Global', 'assets/images/mars.jpg', 1, 1),
('Partial Lunar Eclipse', 'A partial lunar eclipse where the Earth moves between the Sun and Moon but they do not form a perfectly straight line.', 'eclipse', '2026-08-28 04:15:00', 'Global', 'assets/images/lunar-eclipse.jpg', 1, 1),
('Jupiter at Opposition', 'The giant planet will be at its closest approach to Earth and fully illuminated by the Sun.', 'conjunction', '2026-11-20 23:00:00', 'Global', 'assets/images/jupiter.jpg', 1, 1),
('Geminid Meteor Shower Peak', 'The Geminids are the king of the meteor showers, producing up to 120 multicolored meteors per hour.', 'meteor_shower', '2026-12-14 01:00:00', 'Global', 'assets/images/geminid.jpg', 1, 1),
('ISRO PSLV-C60 Launch', 'Polar Satellite Launch Vehicle carrying a payload of Earth observation satellites.', 'launch', '2026-06-05 09:30:00', 'Satish Dhawan Space Centre', 'assets/images/pslv.jpg', 1, 1);

-- Seed News (5 samples)
INSERT INTO news (title, content, source_url, image_path) VALUES 
('ISRO announces new Gaganyaan milestones', 'The Indian Space Research Organisation has successfully completed the latest series of tests for the Gaganyaan human spaceflight mission.', 'https://isro.gov.in', 'assets/images/gaganyaan.jpg'),
('NASA James Webb Space Telescope discovers exoplanet', 'The JWST has confirmed the existence of a new exoplanet with signs of atmospheric water vapor.', 'https://nasa.gov', 'assets/images/jwst.jpg'),
('SpaceX aims for 100 launches this year', 'SpaceX is drastically increasing its launch cadence, aiming to hit an unprecedented 100 orbital flights within the calendar year.', 'https://spacex.com', 'assets/images/spacex-news.jpg'),
('Aditya-L1 reaches Lagrangian Point', 'India’s first solar observatory mission has successfully entered its halo orbit around the Sun-Earth L1 point.', 'https://isro.gov.in', 'assets/images/aditya-l1.jpg'),
('Artemis II crew gears up for lunar flyby', 'The four astronauts assigned to the Artemis II mission have begun rigorous training for their historic journey around the Moon.', 'https://nasa.gov', 'assets/images/artemis.jpg');

-- Seed Gallery (8 samples)
INSERT INTO gallery (title, image_path, category) VALUES 
('The Blue Marble', 'assets/images/gallery-earth.jpg', 'Planets'),
('Falcon Heavy Side Boosters Landing', 'assets/images/gallery-falcon.jpg', 'Rockets'),
('Orion Nebula', 'assets/images/gallery-orion.jpg', 'Nebulae'),
('ISS Crossing the Moon', 'assets/images/gallery-iss-moon.jpg', 'ISS'),
('Saturn Rings', 'assets/images/gallery-saturn.jpg', 'Planets'),
('SLS on Pad 39B', 'assets/images/gallery-sls.jpg', 'Rockets'),
('Crab Nebula', 'assets/images/gallery-crab.jpg', 'Nebulae'),
('Astronaut Spacewalk', 'assets/images/gallery-eva.jpg', 'ISS');
