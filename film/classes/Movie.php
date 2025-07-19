
<?php
class Movie {
    private $conn;
    private $table_name = "movies";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getMovies($limit = 12, $offset = 0, $category = '') {
        if (!$this->conn) {
            return [];
        }
        
        $query = "SELECT m.*, c.name as category_name, c.slug as category_slug 
                  FROM " . $this->table_name . " m 
                  LEFT JOIN categories c ON m.category_id = c.id 
                  WHERE m.status = 'active'";
        
        if (!empty($category)) {
            $query .= " AND c.slug = :category";
        }
        
        $query .= " ORDER BY m.featured DESC, m.rating DESC, m.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($category)) {
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalMovies($category = '') {
        if (!$this->conn) {
            return 0;
        }
        
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " m";
        
        if (!empty($category)) {
            $query .= " LEFT JOIN categories c ON m.category_id = c.id WHERE m.status = 'active' AND c.slug = :category";
        } else {
            $query .= " WHERE m.status = 'active'";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($category)) {
            $stmt->bindParam(':category', $category);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getFeaturedMovies($limit = 6) {
        if (!$this->conn) {
            return [];
        }
        
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE featured = 1 AND status = 'active' 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMovieById($id) {
        $query = "SELECT m.*, c.name as category_name, c.slug as category_slug,
                  (SELECT AVG(rating) FROM ratings r WHERE r.movie_id = m.id) as avg_rating,
                  (SELECT COUNT(*) FROM ratings r WHERE r.movie_id = m.id) as rating_count
                  FROM " . $this->table_name . " m 
                  LEFT JOIN categories c ON m.category_id = c.id 
                  WHERE m.id = :id AND m.status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addMovie($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (title, description, poster_url, trailer_url, movie_url, imdb_id, year, duration, category_id, featured) 
                  VALUES (:title, :description, :poster_url, :trailer_url, :movie_url, :imdb_id, :year, :duration, :category_id, :featured)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':poster_url', $data['poster_url']);
        $stmt->bindParam(':trailer_url', $data['trailer_url']);
        $stmt->bindParam(':movie_url', $data['movie_url']);
        $stmt->bindParam(':imdb_id', $data['imdb_id']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':featured', $data['featured']);
        
        return $stmt->execute();
    }

    public function updateMovie($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, description = :description, poster_url = :poster_url, 
                      trailer_url = :trailer_url, movie_url = :movie_url, imdb_id = :imdb_id,
                      year = :year, duration = :duration, category_id = :category_id, featured = :featured
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':poster_url', $data['poster_url']);
        $stmt->bindParam(':trailer_url', $data['trailer_url']);
        $stmt->bindParam(':movie_url', $data['movie_url']);
        $stmt->bindParam(':imdb_id', $data['imdb_id']);
        $stmt->bindParam(':year', $data['year']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':featured', $data['featured']);
        
        return $stmt->execute();
    }

    public function deleteMovie($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function searchMovies($search, $limit = 12, $offset = 0) {
        $query = "SELECT m.*, c.name as category_name 
                  FROM " . $this->table_name . " m 
                  LEFT JOIN categories c ON m.category_id = c.id 
                  WHERE m.status = 'active' AND (m.title LIKE :search OR m.description LIKE :search)
                  ORDER BY m.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $search_param = "%{$search}%";
        $stmt->bindParam(':search', $search_param);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMovieActors($movie_id) {
        // Actors tablosu henüz oluşturulmamış, boş array döndür
        return [];
    }

    public function updateRating($movie_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET rating = (SELECT AVG(rating) FROM ratings WHERE movie_id = :movie_id)
                  WHERE id = :movie_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':movie_id', $movie_id);
        return $stmt->execute();
    }

    
}
?>
