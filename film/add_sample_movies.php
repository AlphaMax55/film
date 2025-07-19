<?php
require_once 'config/database.php';
require_once 'classes/Movie.php';
require_once 'classes/Category.php';

$database = new Database();
$db = $database->getConnection();

$movie = new Movie($db);
$category = new Category($db);

// Kategorileri al
$categories = $category->getAllCategories();
$category_map = [];
foreach ($categories as $cat) {
    $category_map[$cat['slug']] = $cat['id'];
}

// Örnek filmler
$sample_movies = [
    [
        'title' => 'Avatar: The Way of Water',
        'description' => 'Jake Sully ve Neytiri\'nin maceralarının devamı.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg',
        'backdrop_url' => 'https://image.tmdb.org/t/p/w500/s16H6tpK2utvwDtzZ8Qy4qm5Emw.jpg',
        'trailer_url' => 'https://www.youtube.com/watch?v=d9MyW72ELq0',
        'movie_url' => 'https://www.youtube.com/embed/d9MyW72ELq0',
        'tmdb_id' => '76600',
        'imdb_id' => 'tt1630029',
        'year' => 2022,
        'duration' => 192,
        'rating' => 7.6,
        'vote_count' => 5000,
        'category_id' => $category_map['aksiyon'] ?? 1,
        'featured' => 1
    ],
    [
        'title' => 'Top Gun: Maverick',
        'description' => 'Pete "Maverick" Mitchell\'in yeni görevi.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/62HCnUTziyWcpDaBO2i1DX17ljH.jpg',
        'backdrop_url' => 'https://image.tmdb.org/t/p/w500/odJ4hx6g6vBt4lBWKFD1tI8WS4x.jpg',
        'trailer_url' => 'https://www.youtube.com/watch?v=qSqVVswa420',
        'movie_url' => 'https://www.youtube.com/embed/d9MyW72ELq0',
        'tmdb_id' => '361743',
        'imdb_id' => 'tt1745960',
        'year' => 2022,
        'duration' => 131,
        'rating' => 8.3,
        'vote_count' => 4500,
        'category_id' => $category_map['aksiyon'] ?? 1,
        'featured' => 1
    ],
    [
        'title' => 'Spider-Man: No Way Home',
        'description' => 'Peter Parker\'ın çoklu evren macerası.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg',
        'backdrop_url' => 'https://image.tmdb.org/t/p/w500/14QbnygCuTO0vl7CAFmPf1fgZfV.jpg',
        'trailer_url' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA',
        'movie_url' => '#',
        'tmdb_id' => '634649',
        'imdb_id' => 'tt10872600',
        'year' => 2021,
        'duration' => 148,
        'rating' => 8.4,
        'vote_count' => 6000,
        'category_id' => $category_map['aksiyon'] ?? 1,
        'featured' => 1
    ],
    [
        'title' => 'The Batman',
        'description' => 'Gotham\'ın karanlık şövalyesinin yeni macerası.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg',
        'backdrop_url' => 'https://image.tmdb.org/t/p/w500/b0PlHJr0f5K0CDYW3DgQ6vEjNK1.jpg',
        'trailer_url' => 'https://www.youtube.com/watch?v=mqqft2x_Aa4',
        'movie_url' => '#',
        'tmdb_id' => '414906',
        'imdb_id' => 'tt1877830',
        'year' => 2022,
        'duration' => 176,
        'rating' => 7.8,
        'vote_count' => 5500,
        'category_id' => $category_map['aksiyon'] ?? 1,
        'featured' => 1
    ],
    [
        'title' => 'Interstellar',
        'description' => 'Dünya\'nın geleceği için uzay yolculuğu.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
        'backdrop_url' => 'https://image.tmdb.org/t/p/w500/xJHokMbljvjADYdit5fK5VQsXEG.jpg',
        'trailer_url' => 'https://www.youtube.com/watch?v=zSWdZVtXT7E',
        'movie_url' => '#',
        'tmdb_id' => '157336',
        'imdb_id' => 'tt0816692',
        'year' => 2014,
        'duration' => 169,
        'rating' => 8.6,
        'vote_count' => 7000,
        'category_id' => $category_map['bilim-kurgu'] ?? 1,
        'featured' => 1
    ],
    [
        'title' => 'Inception',
        'description' => 'Rüyalar içinde rüyaların hikayesi.',
        'poster_url' => 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
        'backdrop_url' => 'https://image.tmdb.org/t/p/w500/s3TBrRGB1iav7gFOCNx3H31MoES.jpg',
        'trailer_url' => 'https://www.youtube.com/watch?v=YoHD9XEInc0',
        'movie_url' => '#',
        'tmdb_id' => '27205',
        'imdb_id' => 'tt1375666',
        'year' => 2010,
        'duration' => 148,
        'rating' => 8.8,
        'vote_count' => 8000,
        'category_id' => $category_map['bilim-kurgu'] ?? 1,
        'featured' => 1
    ]
];

// Filmleri ekle
foreach ($sample_movies as $movie_data) {
    try {
        $movie->addMovie($movie_data);
        echo "Film eklendi: " . $movie_data['title'] . "\n";
    } catch (Exception $e) {
        echo "Hata: " . $e->getMessage() . "\n";
    }
}

echo "Örnek filmler başarıyla eklendi!";
?>