
<?php
class Rating {
    private $conn;
    private $table_name = "ratings";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Kullanıcının bir filme verdiği puanı getir
    public function getUserRating($user_id, $movie_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND movie_id = :movie_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Puan ekle veya güncelle
    public function addOrUpdateRating($user_id, $movie_id, $rating) {
        // Önce var mı kontrol et
        $existing = $this->getUserRating($user_id, $movie_id);
        
        if ($existing) {
            // Güncelle
            $query = "UPDATE " . $this->table_name . " 
                      SET rating = :rating 
                      WHERE user_id = :user_id AND movie_id = :movie_id";
        } else {
            // Yeni ekle
            $query = "INSERT INTO " . $this->table_name . " 
                      (user_id, movie_id, rating) 
                      VALUES (:user_id, :movie_id, :rating)";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->bindParam(':rating', $rating);
        
        return $stmt->execute();
    }

    // Film ortalamasını getir
    public function getMovieAverageRating($movie_id) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count 
                  FROM " . $this->table_name . " 
                  WHERE movie_id = :movie_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // En yüksek puanlı filmleri getir
    public function getTopRatedMovies($limit = 10) {
        $query = "SELECT m.*, AVG(r.rating) as avg_rating, COUNT(r.rating) as rating_count
                  FROM movies m
                  LEFT JOIN " . $this->table_name . " r ON m.id = r.movie_id
                  GROUP BY m.id
                  HAVING rating_count >= 5
                  ORDER BY avg_rating DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kullanıcının verdiği tüm puanları getir
    public function getUserRatings($user_id) {
        $query = "SELECT r.*, m.title, m.poster_url 
                  FROM " . $this->table_name . " r
                  LEFT JOIN movies m ON r.movie_id = m.id
                  WHERE r.user_id = :user_id
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
