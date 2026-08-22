CREATE DATABASE IF NOT EXISTS dog_walk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dog_walk;

DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS ratings;
DROP TABLE IF EXISTS walkers;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(50),
    address VARCHAR(255),
    role ENUM('user','walker','admin') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 0,
    is_blocked TINYINT(1) NOT NULL DEFAULT 0,
    activation_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_expires_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE walkers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    photo VARCHAR(255),
    description TEXT,
    favorite_breed VARCHAR(100),
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    contact_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    walker_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_walker_rating (user_id, walker_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (walker_id) REFERENCES walkers(id) ON DELETE CASCADE
);

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    walker_id INT NOT NULL,
    dog_breed VARCHAR(100) NOT NULL,
    dog_name VARCHAR(100) NOT NULL,
    dog_gender ENUM('male','female') NOT NULL,
    dog_age INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (walker_id) REFERENCES walkers(id) ON DELETE CASCADE
);

INSERT INTO users (email, password, first_name, last_name, phone, address, role, is_active, is_blocked)
VALUES
('admin@dogwalk.local', '$2y$12$Px3kuMiGT1/7W5drwjylB.CDz8WBnnx3aWjOmBqrnhhTu5Es1/gWC', 'Admin', 'User', '060000000', 'Subotica', 'admin', 1, 0),
('walker1@dogwalk.local', '$2y$12$Px3kuMiGT1/7W5drwjylB.CDz8WBnnx3aWjOmBqrnhhTu5Es1/gWC', 'Marko', 'Petrović', '061111111', 'Subotica', 'walker', 1, 0),
('walker2@dogwalk.local', '$2y$12$Px3kuMiGT1/7W5drwjylB.CDz8WBnnx3aWjOmBqrnhhTu5Es1/gWC', 'Ana', 'Jovanović', '062222222', 'Novi Sad', 'walker', 1, 0),
('user@dogwalk.local', '$2y$12$Px3kuMiGT1/7W5drwjylB.CDz8WBnnx3aWjOmBqrnhhTu5Es1/gWC', 'Test', 'Korisnik', '063333333', 'Beograd', 'user', 1, 0),
('walker3@dogwalk.local', '$2y$12$Px3kuMiGT1/7W5drwjylB.CDz8WBnnx3aWjOmBqrnhhTu5Es1/gWC', 'Jelena', 'Savić', '064444444', 'Beograd', 'walker', 1, 0),
('walker4@dogwalk.local', '$2y$12$Px3kuMiGT1/7W5drwjylB.CDz8WBnnx3aWjOmBqrnhhTu5Es1/gWC', 'Nikola', 'Ilić', '065555555', 'Novi Sad', 'walker', 1, 0),
('walker5@dogwalk.local', '$2y$12$Px3kuMiGT1/7W5drwjylB.CDz8WBnnx3aWjOmBqrnhhTu5Es1/gWC', 'Milica', 'Kovač', '066666666', 'Subotica', 'walker', 1, 0);

INSERT INTO walkers (user_id, photo, description, favorite_breed, is_approved, is_available, contact_count)
VALUES
(2, NULL, 'Volim pse i imam iskustvo sa manjim i srednjim rasama.', 'Labrador', 1, 1, 8),
(3, NULL, 'Pouzdana šetačica pasa, dostupna vikendom i radnim danima.', 'Bigl', 1, 1, 12),
(5, NULL, 'Šetam energične pse i volim duge šetnje u parku.', 'Border koli', 1, 1, 6),
(6, NULL, 'Mirno i pažljivo radim sa starijim psima i štencima.', 'Zlatni retriver', 1, 1, 5),
(7, NULL, 'Dostupna sam za jutarnje i večernje šetnje.', 'Pudla', 1, 1, 9);

INSERT INTO ratings (user_id, walker_id, rating, comment)
VALUES
(4, 1, 5, 'Odličan šetač, sve preporuke.'),
(4, 2, 4, 'Vrlo dobra komunikacija.'),
(4, 3, 5, 'Pas je bio zadovoljan.'),
(4, 4, 3, 'Korektno.'),
(4, 5, 5, 'Sve pohvale.');
