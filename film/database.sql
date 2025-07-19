
CREATE DATABASE film_sitesi;
USE film_sitesi;

-- Kullanıcılar tablosu
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kategoriler tablosu
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL
);

-- Filmler tablosu
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    poster_url VARCHAR(500),
    backdrop_url VARCHAR(500),
    trailer_url VARCHAR(500),
    movie_url VARCHAR(500),
    imdb_id VARCHAR(20),
    tmdb_id VARCHAR(20),
    year INT,
    duration INT,
    rating DECIMAL(3,1) DEFAULT 0,
    vote_count INT DEFAULT 0,
    country VARCHAR(10),
    category_id INT,
    featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    UNIQUE KEY unique_tmdb (tmdb_id)
);

-- Oyuncular tablosu
CREATE TABLE actors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image_url VARCHAR(500)
);

-- Film-oyuncu ilişki tablosu
CREATE TABLE movie_actors (
    movie_id INT,
    actor_id INT,
    PRIMARY KEY (movie_id, actor_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE
);

-- Puanlamalar tablosu
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_movie (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Yorumlar tablosu
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    comment TEXT NOT NULL,
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Varsayılan admin kullanıcısı ve kategoriler
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO categories (name, slug) VALUES 
('Aksiyon', 'aksiyon'),
('Macera', 'macera'),
('Komedi', 'komedi'),
('Dram', 'dram'),
('Korku', 'korku'),
('Gerilim', 'gerilim'),
('Romantik', 'romantik'),
('Bilim Kurgu', 'bilim-kurgu'),
('Fantastik', 'fantastik'),
('Animasyon', 'animasyon'),
('Suç', 'suc'),
('Belgesel', 'belgesel'),
('Biyografi', 'biyografi'),
('Tarih', 'tarih'),
('Savaş', 'savas'),
('Western', 'western'),
('Müzikal', 'muzikal'),
('Spor', 'spor'),
('Aile', 'aile'),
('Gizem', 'gizem');
