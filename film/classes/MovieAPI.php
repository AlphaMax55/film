
<?php
class MovieAPI {
    private $conn;
    private $tmdb_api_key = 'dca617e65fc86d0dcf60e614a8174ca0'; // TMDB API anahtarınızı buraya yazın
    private $tmdb_base_url = 'https://api.themoviedb.org/3';
    private $image_base_url = 'https://image.tmdb.org/t/p/w500';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // TMDB'den film çek
    public function fetchMoviesFromTMDB($category = '', $year = '', $country = '', $min_rating = 0, $page = 1) {
        $url = $this->tmdb_base_url . '/discover/movie';
        $params = [
            'api_key' => $this->tmdb_api_key,
            'language' => 'tr-TR',
            'page' => $page,
            'sort_by' => 'popularity.desc'
        ];
        
        // Kategori (genre) filtresi
        if ($category) {
            $genre_id = $this->getGenreId($category);
            if ($genre_id) {
                $params['with_genres'] = $genre_id;
            }
        }
        
        // Yıl filtresi
        if ($year) {
            $params['primary_release_year'] = $year;
        }
        
        // Ülke filtresi
        if ($country) {
            $params['with_origin_country'] = $country;
        }
        
        // Minimum rating filtresi
        if ($min_rating > 0) {
            $params['vote_average.gte'] = $min_rating;
        }
        
        $query_string = http_build_query($params);
        $response = file_get_contents($url . '?' . $query_string);
        
        if ($response === FALSE) {
            return [];
        }
        
        $data = json_decode($response, true);
        return $data['results'] ?? [];
    }
    
    // Genre ID'sini al
    private function getGenreId($category_slug) {
        $genre_map = [
            'aksiyon' => 28,
            'macera' => 12,
            'komedi' => 35,
            'dram' => 18,
            'romantik' => 10749,
            'korku' => 27,
            'gerilim' => 53,
            'bilim-kurgu' => 878,
            'fantastik' => 14,
            'animasyon' => 16,
            'belgesel' => 99,
            'aile' => 10751,
            'gizem' => 9648,
            'suc' => 80,
            'savas' => 10752,
            'western' => 37,
            'muzikal' => 10402,
            'tarih' => 36,
            'biyografi' => 10751
        ];
        
        return $genre_map[$category_slug] ?? null;
    }
    
    // TMDB film verisini database formatına çevir
    public function convertTMDBToMovie($tmdb_movie, $category_id = null) {
        return [
            'title' => $tmdb_movie['title'] ?? $tmdb_movie['original_title'],
            'description' => $tmdb_movie['overview'] ?? '',
            'poster_url' => $this->image_base_url . ($tmdb_movie['poster_path'] ?? ''),
            'backdrop_url' => $this->image_base_url . ($tmdb_movie['backdrop_path'] ?? ''),
            'tmdb_id' => $tmdb_movie['id'],
            'year' => date('Y', strtotime($tmdb_movie['release_date'] ?? 'now')),
            'rating' => round($tmdb_movie['vote_average'] ?? 0, 1),
            'vote_count' => $tmdb_movie['vote_count'] ?? 0,
            'category_id' => $category_id,
            'featured' => ($tmdb_movie['vote_average'] ?? 0) >= 8 ? 1 : 0,
            'status' => 'active'
        ];
    }
    
    // Veritabanına film ekle (duplicate check ile)
    public function saveMovieToDatabase($movie_data) {
        // Önce film zaten var mı kontrol et
        $check_query = "SELECT id FROM movies WHERE tmdb_id = :tmdb_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':tmdb_id', $movie_data['tmdb_id']);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            return false; // Film zaten mevcut
        }
        
        $query = "INSERT INTO movies 
                  (title, description, poster_url, backdrop_url, tmdb_id, year, rating, vote_count, category_id, featured, status) 
                  VALUES 
                  (:title, :description, :poster_url, :backdrop_url, :tmdb_id, :year, :rating, :vote_count, :category_id, :featured, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $movie_data['title']);
        $stmt->bindParam(':description', $movie_data['description']);
        $stmt->bindParam(':poster_url', $movie_data['poster_url']);
        $stmt->bindParam(':backdrop_url', $movie_data['backdrop_url']);
        $stmt->bindParam(':tmdb_id', $movie_data['tmdb_id']);
        $stmt->bindParam(':year', $movie_data['year']);
        $stmt->bindParam(':rating', $movie_data['rating']);
        $stmt->bindParam(':vote_count', $movie_data['vote_count']);
        $stmt->bindParam(':category_id', $movie_data['category_id']);
        $stmt->bindParam(':featured', $movie_data['featured']);
        $stmt->bindParam(':status', $movie_data['status']);
        
        return $stmt->execute();
    }
    
    // Toplu film çekme ve kaydetme
    public function fetchAndSaveMovies($category_slug = '', $year = '', $country = '', $min_rating = 0, $pages = 3) {
        $movie = new Movie($this->conn);
        $category = new Category($this->conn);
        
        $category_id = null;
        if ($category_slug) {
            $categories = $category->getAllCategories();
            foreach ($categories as $cat) {
                if ($cat['slug'] == $category_slug) {
                    $category_id = $cat['id'];
                    break;
                }
            }
        }
        
        $saved_count = 0;
        
        for ($page = 1; $page <= $pages; $page++) {
            $movies = $this->fetchMoviesFromTMDB($category_slug, $year, $country, $min_rating, $page);
            
            foreach ($movies as $tmdb_movie) {
                $movie_data = $this->convertTMDBToMovie($tmdb_movie, $category_id);
                
                if ($this->saveMovieToDatabase($movie_data)) {
                    $saved_count++;
                }
            }
            
            // API rate limiting için kısa bekleme
            sleep(1);
        }
        
        return $saved_count;
    }
}
?>
