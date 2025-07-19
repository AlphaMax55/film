
<?php
class Database {
    private $host = '127.0.0.1';
    private $database_name = 'film_sitesi';
    private $username = 'root';
    private $password = 'mysql';
    private $port = 3306;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Önce MySQL olmadan bağlan ve veritabanını oluştur
            $dsn_without_db = "mysql:host=" . $this->host . ";port=" . $this->port . ";charset=utf8";
            $temp_conn = new PDO($dsn_without_db, $this->username, $this->password);
            $temp_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Veritabanını oluştur
            $temp_conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->database_name);
            $temp_conn = null;
            
            // Şimdi veritabanı ile bağlan
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->database_name . ";charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Tabloları oluştur
            $this->createTables();
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

    private function createTables() {
        try {
            // Tabloları oluştur
            $sql = "
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS movies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                poster_url VARCHAR(500),
                backdrop_url VARCHAR(500),
                trailer_url VARCHAR(500),
                movie_url VARCHAR(500),
                tmdb_id VARCHAR(50),
                imdb_id VARCHAR(50),
                year INT,
                duration INT DEFAULT 120,
                rating DECIMAL(3,1) DEFAULT 0.0,
                vote_count INT DEFAULT 0,
                category_id INT,
                featured BOOLEAN DEFAULT 0,
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories (id)
            );

            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS ratings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                movie_id INT NOT NULL,
                rating INT NOT NULL CHECK (rating >= 1 AND rating <= 10),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id),
                FOREIGN KEY (movie_id) REFERENCES movies (id),
                UNIQUE KEY unique_user_movie (user_id, movie_id)
            );

            CREATE TABLE IF NOT EXISTS comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                movie_id INT NOT NULL,
                comment TEXT NOT NULL,
                status VARCHAR(20) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id),
                FOREIGN KEY (movie_id) REFERENCES movies (id)
            );
            ";

            $this->conn->exec($sql);

            // Örnek kategoriler ekle
            $categories = [
                ['Aksiyon', 'aksiyon'],
                ['Macera', 'macera'],
                ['Komedi', 'komedi'],
                ['Dram', 'dram'],
                ['Romantik', 'romantik'],
                ['Korku', 'korku'],
                ['Gerilim', 'gerilim'],
                ['Bilim Kurgu', 'bilim-kurgu'],
                ['Fantastik', 'fantastik'],
                ['Animasyon', 'animasyon'],
                ['Belgesel', 'belgesel'],
                ['Aile', 'aile'],
                ['Gizem', 'gizem'],
                ['Suç', 'suc'],
                ['Savaş', 'savas'],
                ['Western', 'western'],
                ['Müzikal', 'muzikal'],
                ['Spor', 'spor'],
                ['Biyografi', 'biyografi'],
                ['Tarih', 'tarih']
            ];

            $stmt = $this->conn->prepare("INSERT IGNORE INTO categories (name, slug) VALUES (?, ?)");
            foreach ($categories as $category) {
                $stmt->execute($category);
            }

            // Admin kullanıcısı ekle
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $this->conn->exec("INSERT IGNORE INTO users (username, email, password, role) 
                               VALUES ('admin', 'admin@example.com', '$admin_password', 'admin')");

        } catch(PDOException $e) {
            echo "Database creation error: " . $e->getMessage();
        }
    }
}
?>
