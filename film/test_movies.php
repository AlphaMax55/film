
<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Movie.php';
require_once 'classes/Category.php';

$database = new Database();
$db = $database->getConnection();
$movie = new Movie($db);
$category = new Category($db);

// Test filmleri ekle
$test_movies = [
    [
        'title' => 'Big Buck Bunny',
        'description' => 'Kısa animasyon filmi - test amaçlı',
        'poster_url' => 'https://peach.blender.org/wp-content/uploads/bbb-splash.png',
        'backdrop_url' => 'https://peach.blender.org/wp-content/uploads/bbb-splash.png',
        'trailer_url' => 'https://www.youtube.com/watch?v=YE7VzlLtp-4',
        'movie_url' => 'https://www.youtube.com/embed/YE7VzlLtp-4',
        'tmdb_id' => '10378',
        'imdb_id' => 'tt1254207',
        'year' => 2008,
        'duration' => 10,
        'rating' => 7.2,
        'vote_count' => 1000,
        'category_id' => 1,
        'featured' => 1
    ],
    [
        'title' => 'Sintel',
        'description' => 'Kısa animasyon filmi - test amaçlı',
        'poster_url' => 'https://durian.blender.org/wp-content/uploads/2010/06/sintel_poster_small.jpg',
        'backdrop_url' => 'https://durian.blender.org/wp-content/uploads/2010/06/sintel_poster_small.jpg',
        'trailer_url' => 'https://www.youtube.com/watch?v=eRsGyueVLvQ',
        'movie_url' => 'https://www.youtube.com/embed/eRsGyueVLvQ',
        'tmdb_id' => '45162',
        'imdb_id' => 'tt1727587',
        'year' => 2010,
        'duration' => 14,
        'rating' => 7.4,
        'vote_count' => 800,
        'category_id' => 1,
        'featured' => 1
    ]
];

foreach ($test_movies as $movie_data) {
    try {
        $movie->addMovie($movie_data);
        echo "Test filmi eklendi: " . $movie_data['title'] . "<br>";
    } catch (Exception $e) {
        echo "Hata: " . $e->getMessage() . "<br>";
    }
}

echo "<br>Test filmleri başarıyla eklendi! <a href='index.php'>Ana sayfaya dön</a>";
?>
