
<?php
class OMDBMovieAPI {
    private $conn;
    private $omdb_api_key = '5b86fb8b'; // OMDB API Key
    private $omdb_base_url = 'http://www.omdbapi.com/';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // OMDB'den film ara ve çek
    public function searchMovies($search_term, $type = 'movie', $year = '', $page = 1) {
        $url = $this->omdb_base_url;
        $params = [
            'apikey' => $this->omdb_api_key,
            's' => $search_term,
            'type' => $type,
            'page' => $page
        ];
        
        if ($year) {
            $params['y'] = $year;
        }
        
        $query_string = http_build_query($params);
        $response = file_get_contents($url . '?' . $query_string);
        
        if ($response === FALSE) {
            return [];
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['Response']) && $data['Response'] === 'True') {
            return $data['Search'] ?? [];
        }
        
        return [];
    }
    
    // OMDB'den film detayları al
    public function getMovieDetails($imdb_id) {
        $url = $this->omdb_base_url;
        $params = [
            'apikey' => $this->omdb_api_key,
            'i' => $imdb_id,
            'plot' => 'full'
        ];
        
        $query_string = http_build_query($params);
        $response = file_get_contents($url . '?' . $query_string);
        
        if ($response === FALSE) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['Response']) && $data['Response'] === 'True') {
            return $data;
        }
        
        return null;
    }
    
    // OMDB film verisini database formatına çevir
    public function convertOMDBToMovie($omdb_movie, $category_id = null) {
        return [
            'title' => $omdb_movie['Title'] ?? '',
            'description' => $omdb_movie['Plot'] ?? '',
            'poster_url' => ($omdb_movie['Poster'] && $omdb_movie['Poster'] !== 'N/A') ? $omdb_movie['Poster'] : 'https://via.placeholder.com/300x450?text=No+Image',
            'backdrop_url' => ($omdb_movie['Poster'] && $omdb_movie['Poster'] !== 'N/A') ? $omdb_movie['Poster'] : 'https://via.placeholder.com/1920x1080?text=No+Image',
            'trailer_url' => '',
            'movie_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Placeholder video
            'imdb_id' => $omdb_movie['imdbID'] ?? '',
            'year' => is_numeric($omdb_movie['Year'] ?? '') ? (int)$omdb_movie['Year'] : date('Y'),
            'duration' => $this->parseDuration($omdb_movie['Runtime'] ?? ''),
            'rating' => $this->parseRating($omdb_movie['imdbRating'] ?? ''),
            'vote_count' => 0,
            'category_id' => $category_id,
            'featured' => ($this->parseRating($omdb_movie['imdbRating'] ?? '') >= 8) ? 1 : 0,
            'status' => 'active'
        ];
    }
    
    // Runtime string'ini dakikaya çevir
    private function parseDuration($runtime) {
        if (preg_match('/(\d+)/', $runtime, $matches)) {
            return (int)$matches[1];
        }
        return 120; // Default 120 minutes
    }
    
    // IMDB rating'i float'a çevir
    private function parseRating($rating) {
        if ($rating && $rating !== 'N/A') {
            return (float)$rating;
        }
        return 0;
    }
    
    // Genre'ları kategori ID'sine çevir
    private function getGenreToCategory($genre_string, $categories) {
        $genres = explode(', ', strtolower($genre_string));
        
        $genre_map = [
            'action' => 'aksiyon',
            'adventure' => 'macera',
            'comedy' => 'komedi',
            'drama' => 'dram',
            'romance' => 'romantik',
            'horror' => 'korku',
            'thriller' => 'gerilim',
            'sci-fi' => 'bilim-kurgu',
            'fantasy' => 'fantastik',
            'animation' => 'animasyon',
            'documentary' => 'belgesel',
            'family' => 'aile',
            'mystery' => 'gizem',
            'crime' => 'suc',
            'war' => 'savas',
            'western' => 'western',
            'musical' => 'muzikal',
            'history' => 'tarih',
            'biography' => 'biyografi'
        ];
        
        foreach ($genres as $genre) {
            $genre = trim($genre);
            if (isset($genre_map[$genre])) {
                $slug = $genre_map[$genre];
                foreach ($categories as $cat) {
                    if ($cat['slug'] == $slug) {
                        return $cat['id'];
                    }
                }
            }
        }
        
        return $categories[0]['id'] ?? 1; // Default category
    }
    
    // Veritabanına film ekle (duplicate check ile)
    public function saveMovieToDatabase($movie_data) {
        // Önce film zaten var mı kontrol et
        $check_query = "SELECT id FROM movies WHERE imdb_id = :imdb_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':imdb_id', $movie_data['imdb_id']);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            return false; // Film zaten mevcut
        }
        
        $query = "INSERT INTO movies 
                  (title, description, poster_url, backdrop_url, trailer_url, movie_url, imdb_id, year, duration, rating, vote_count, category_id, featured, status) 
                  VALUES 
                  (:title, :description, :poster_url, :backdrop_url, :trailer_url, :movie_url, :imdb_id, :year, :duration, :rating, :vote_count, :category_id, :featured, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $movie_data['title']);
        $stmt->bindParam(':description', $movie_data['description']);
        $stmt->bindParam(':poster_url', $movie_data['poster_url']);
        $stmt->bindParam(':backdrop_url', $movie_data['backdrop_url']);
        $stmt->bindParam(':trailer_url', $movie_data['trailer_url']);
        $stmt->bindParam(':movie_url', $movie_data['movie_url']);
        $stmt->bindParam(':imdb_id', $movie_data['imdb_id']);
        $stmt->bindParam(':year', $movie_data['year']);
        $stmt->bindParam(':duration', $movie_data['duration']);
        $stmt->bindParam(':rating', $movie_data['rating']);
        $stmt->bindParam(':vote_count', $movie_data['vote_count']);
        $stmt->bindParam(':category_id', $movie_data['category_id']);
        $stmt->bindParam(':featured', $movie_data['featured']);
        $stmt->bindParam(':status', $movie_data['status']);
        
        return $stmt->execute();
    }
    
    // Popüler film listesi için arama terimlerini kullan
    public function fetchPopularMovies($categories, $pages = 3) {
        $search_terms = [
            'batman', 'superman', 'spider', 'avengers', 'star wars', 'harry potter',
            'lord rings', 'matrix', 'inception', 'titanic', 'avatar', 'frozen',
            'toy story', 'jurassic', 'fast furious', 'mission impossible',
            'james bond', 'indiana jones', 'terminator', 'alien', 'predator',
            'pirates caribbean', 'transformers', 'xmen', 'iron man', 'thor'
        ];
        
        $saved_count = 0;
        
        foreach ($search_terms as $term) {
            if ($saved_count >= 100) break; // Limit to prevent too many requests
            
            for ($page = 1; $page <= $pages; $page++) {
                $movies = $this->searchMovies($term, 'movie', '', $page);
                
                foreach ($movies as $movie) {
                    if ($saved_count >= 100) break;
                    
                    // Film detaylarını al
                    $details = $this->getMovieDetails($movie['imdbID']);
                    
                    if ($details) {
                        $category_id = $this->getGenreToCategory($details['Genre'] ?? '', $categories);
                        $movie_data = $this->convertOMDBToMovie($details, $category_id);
                        
                        if ($this->saveMovieToDatabase($movie_data)) {
                            $saved_count++;
                        }
                    }
                    
                    // API rate limiting için kısa bekleme
                    usleep(200000); // 0.2 second delay
                }
                
                if ($saved_count >= 100) break;
                sleep(1); // 1 second delay between pages
            }
        }
        
        return $saved_count;
    }
    
    // Kategori bazında film çek
    public function fetchMoviesByCategory($category_slug, $categories, $count = 20) {
        $category_searches = [
            'aksiyon' => ['action', 'batman', 'superman', 'avengers', 'fast', 'mission'],
            'komedi' => ['comedy', 'funny', 'laugh', 'humor', 'comic'],
            'dram' => ['drama', 'life', 'family', 'love', 'story'],
            'korku' => ['horror', 'scary', 'ghost', 'zombie', 'evil'],
            'romantik' => ['romance', 'love', 'romantic', 'heart', 'couple'],
            'bilim-kurgu' => ['sci-fi', 'space', 'future', 'robot', 'alien'],
            'fantastik' => ['fantasy', 'magic', 'wizard', 'dragon', 'fairy'],
            'gerilim' => ['thriller', 'suspense', 'mystery', 'crime', 'detective'],
            'macera' => ['adventure', 'journey', 'quest', 'treasure', 'explorer'],
            'animasyon' => ['animation', 'cartoon', 'pixar', 'disney', 'anime']
        ];
        
        $search_terms = $category_searches[$category_slug] ?? ['movie'];
        $category_id = null;
        
        foreach ($categories as $cat) {
            if ($cat['slug'] == $category_slug) {
                $category_id = $cat['id'];
                break;
            }
        }
        
        if (!$category_id) return 0;
        
        $saved_count = 0;
        
        foreach ($search_terms as $term) {
            if ($saved_count >= $count) break;
            
            $movies = $this->searchMovies($term, 'movie');
            
            foreach ($movies as $movie) {
                if ($saved_count >= $count) break;
                
                $details = $this->getMovieDetails($movie['imdbID']);
                
                if ($details) {
                    $movie_data = $this->convertOMDBToMovie($details, $category_id);
                    
                    if ($this->saveMovieToDatabase($movie_data)) {
                        $saved_count++;
                    }
                }
                
                usleep(200000); // 0.2 second delay
            }
        }
        
        return $saved_count;
    }
}
?>
