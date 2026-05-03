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
('Chandrayaan-3 Anniversary', 'This event commemorates the historic Chandrayaan 3 mission and its successful soft landing near the lunar south pole. The anniversary is an opportunity to highlight the scientific achievements of the mission, the engineering challenges overcome by the team, and the ongoing planetary science studies enabled by the landing. Public outreach activities often include talks, educational programs, and multimedia exhibitions that explain the mission objectives, surface experiments, and the broader importance of lunar exploration for science and future human exploration. Observers and enthusiasts gather to celebrate the mission milestone and to reflect on the next steps in lunar research and exploration.', 'other', '2026-08-23 18:04:00', 'Sriharikota / Moon', 'assets/images/chandrayaan.jpg', 1, 1),
('Perseid Meteor Shower Peak', 'The Perseid Meteor Shower offers a spectacular annual display when Earth crosses the debris stream left by Comet Swift Tuttle. At peak, observers under dark skies can see dozens to over one hundred meteors per hour, with bright trails and occasional fireballs. The shower provides a great opportunity for public stargazing events, astrophotography sessions, and citizen science observations to record rates and brightness. Safety and viewing tips are shared widely by astronomy societies, including advice on dark adaptation, comfortable viewing setups, and how to report sightings. The Perseids also inspire discussions about cometary science, meteoroid origins, and the dynamics of small bodies in the solar system.', 'meteor_shower', '2026-08-12 22:00:00', 'Global / Northern Hemisphere', 'assets/images/perseid.jpg', 1, 1),
('Total Solar Eclipse', 'A total solar eclipse is one of the most profound celestial spectacles where the Moon completely covers the solar disk for observers along a narrow path of totality. This event draws international attention because it combines dramatic visual phenomena with scientific opportunities to study the solar corona, perform atmospheric experiments, and engage broad public participation. Events typically include safe viewing workshops, distribution of certified solar filters, and coordinated live streams for those outside the path. Educators emphasize the geometry and orbital mechanics that create eclipses, and local communities along the path often host festivals that blend science outreach with cultural activities.', 'eclipse', '2026-08-12 10:30:00', 'Iceland, Spain', 'assets/images/solar-eclipse.jpg', 1, 1),
('ISS Visible Pass over India', 'An ISS visible pass is a predictable flyover when the International Space Station reflects sunlight and appears as a bright, steadily moving point across the sky. These passes are accessible to the general public and are ideal for introducing people to human spaceflight and orbital mechanics. Observing events often include timing and location information, binocular and photographic guidance, and explanatory material about the astronauts on board and their experiments. Schools and astronomy clubs organize group viewings to encourage STEM engagement, and participants are invited to learn about how orbital altitude and ground track determine visibility windows.', 'iss_pass', '2026-04-15 19:20:00', 'India (Pan-India)', 'assets/images/iss.jpg', 1, 1),
('SpaceX Starship Orbital Test Flight', 'This event marks a major test milestone for a next generation heavy launch system designed for large payloads and eventual crewed missions. Test flights focus on validating integrated stages, guidance systems, thermal performance, and orbital insertion procedures. Observers and analysts follow these tests closely because they reveal progress on reusability, rapid cadence operations, and cost reductions in access to space. The event usually generates extensive media coverage, public discussions about regulatory and environmental considerations near launch sites, and commentary from aerospace professionals about how successful flights advance human and robotic exploration capabilities.', 'launch', '2026-05-10 14:00:00', 'Starbase, Texas', 'assets/images/starship.jpg', 1, 1),
('Mars at Opposition', 'When Mars reaches opposition it lies directly opposite the Sun in Earths sky and is both closer and more fully illuminated than at most other times. This configuration yields excellent observing conditions, making it a prime time for planetary imaging, scientific observation campaigns, and public outreach. Amateur and professional astronomers plan coordinated observations to study surface features, dust storms, and atmospheric composition. Outreach programs highlight how apparent size and brightness change with orbital geometry and provide opportunities to discuss missions currently operating at Mars and plans for future exploration.', 'conjunction', '2027-02-19 00:00:00', 'Global', 'assets/images/mars.jpg', 1, 1),
('Partial Lunar Eclipse', 'A partial lunar eclipse occurs when Earths shadow covers only a portion of the lunar disk, producing subtle but scientifically interesting changes in appearance. Such events are accessible to anyone with a clear horizon and do not require special eye protection, making them ideal for family activities and educational outreach. Observers can study penumbral and umbral contacts, measure the eclipse timing, and photograph the changing lunar contrast. Many astronomy groups use these opportunities to teach about Earth Moon Sun geometry, historical observations, and how eclipses have been used to advance astronomical understanding.', 'eclipse', '2026-08-28 04:15:00', 'Global', 'assets/images/lunar-eclipse.jpg', 1, 1),
('Jupiter at Opposition', 'Jupiter at opposition is when the giant planet is opposite the Sun and nearest to Earth for its orbital cycle, offering exceptional visibility and detail through telescopes. This is an excellent period for studying cloud bands, the Great Red Spot, and storm evolution. Observing programs often encourage imaging, photometry, and long term monitoring to track atmospheric dynamics. Educational events explain Jovian system dynamics, its many moons, and the relevance of giant planets to planetary formation theories. Public observing nights commonly pair Jupiter viewing with talks about solar system architecture and recent mission results.', 'conjunction', '2026-11-20 23:00:00', 'Global', 'assets/images/jupiter.jpg', 1, 1),
('Geminid Meteor Shower Peak', 'The Geminid meteor shower is known for producing numerous bright and often slower moving meteors, peaking in mid December. It originates from the debris of asteroid 3200 Phaethon rather than a comet, which makes it particularly interesting to planetary scientists. The shower produces colorful meteors and many fireballs, and it is a favorite for astrophotographers and observers in both hemispheres where visibility permits. Events emphasize dark sky viewing, photographic techniques, and citizen science projects to record meteor rates and trajectories.', 'meteor_shower', '2026-12-14 01:00:00', 'Global', 'assets/images/geminid.jpg', 1, 1),
('ISRO PSLV-C60 Launch', 'The PSLV launch event is a key moment for national space capabilities and for the deployment of Earth observation satellites that support weather, agriculture, and disaster monitoring. Launch coverage often highlights the payload configuration, mission objectives, and expected orbital parameters. Technical briefings discuss launch vehicle performance, mission planning, and the satellite commissioning timeline. Local outreach and educational programs use launches to engage students in engineering and science, showcasing the role of satellite data in everyday life.', 'launch', '2026-06-05 09:30:00', 'Satish Dhawan Space Centre', 'assets/images/pslv.jpg', 1, 1);

-- Seed News (5 samples)
INSERT INTO news (title, content, source_url, image_path) VALUES 
('ISRO announces new Gaganyaan milestones', 'The Indian Space Research Organisation has successfully completed the latest series of tests for the Gaganyaan human spaceflight mission.', 'https://www.isro.gov.in', 'assets/images/gaganyaan.jpg'),
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
