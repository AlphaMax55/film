
<?php
class Comment {
    private $conn;
    private $table_name = "comments";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Film yorumlarını getir
    public function getMovieComments($movie_id, $status = 'approved') {
        $query = "SELECT c.*, u.username 
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.user_id = u.id
                  WHERE c.movie_id = :movie_id AND c.status = :status
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Yorum ekle
    public function addComment($user_id, $movie_id, $comment) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, movie_id, comment, status) 
                  VALUES (:user_id, :movie_id, :comment, 'approved')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->bindParam(':comment', $comment);
        
        return $stmt->execute();
    }

    // Bekleyen yorum sayısı
    public function getPendingCommentsCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Tüm yorumları getir (admin için)
    public function getAllComments() {
        $query = "SELECT c.*, u.username, m.title as movie_title 
                  FROM " . $this->table_name . " c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN movies m ON c.movie_id = m.id
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Yorum durumunu güncelle
    public function updateCommentStatus($comment_id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $comment_id);
        
        return $stmt->execute();
    }

    // Yorum sil
    public function deleteComment($comment_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $comment_id);
        
        return $stmt->execute();
    }
}
?>
