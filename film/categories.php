
<?php
ob_start();
session_start();
require_once 'config/database.php';
require_once 'classes/Movie.php';
require_once 'classes/Category.php';

$database = new Database();
$db = $database->getConnection();

$movie = new Movie($db);
$category = new Category($db);

$categories = $category->getAllCategories();

// Eğer kategori seçilmişse, o kategorinin filmlerini getir
$selected_category = isset($_GET['slug']) ? $_GET['slug'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 16;
$offset = ($page - 1) * $limit;

$movies = [];
$total_movies = 0;
$total_pages = 0;
$category_name = 'Tüm Kategoriler';

if ($selected_category) {
    $movies = $movie->getMovies($limit, $offset, $selected_category);
    $total_movies = $movie->getTotalMovies($selected_category);
    $total_pages = ceil($total_movies / $limit);
    
    // Kategori adını bul
    foreach ($categories as $cat) {
        if ($cat['slug'] == $selected_category) {
            $category_name = $cat['name'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategoriler - CinemaMax</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-film"></i> CinemaMax
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="categories.php">Kategoriler</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= $_SESSION['username'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($_SESSION['role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/index.php"><i class="fas fa-cog"></i> Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Giriş</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Kayıt</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Categories Section -->
    <section class="categories-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center mb-5">Film Kategorileri</h1>
                </div>
            </div>

            <?php if (!$selected_category): ?>
            <!-- Category Grid -->
            <div class="row">
                <?php 
                $category_icons = [
                    'aksiyon' => 'fa-fist-raised',
                    'macera' => 'fa-map',
                    'komedi' => 'fa-laugh',
                    'dram' => 'fa-theater-masks',
                    'korku' => 'fa-ghost',
                    'gerilim' => 'fa-exclamation-triangle',
                    'romantik' => 'fa-heart',
                    'bilim-kurgu' => 'fa-rocket',
                    'fantastik' => 'fa-magic',
                    'animasyon' => 'fa-palette',
                    'suc' => 'fa-gavel',
                    'belgesel' => 'fa-video',
                    'biyografi' => 'fa-user-circle',
                    'tarih' => 'fa-landmark',
                    'savas' => 'fa-shield-alt',
                    'western' => 'fa-horse',
                    'muzikal' => 'fa-music',
                    'spor' => 'fa-football-ball',
                    'aile' => 'fa-home',
                    'gizem' => 'fa-search'
                ];
                
                foreach($categories as $cat): 
                    $icon = $category_icons[$cat['slug']] ?? 'fa-film';
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="category-card">
                        <a href="categories.php?slug=<?= $cat['slug'] ?>" class="text-decoration-none">
                            <div class="category-icon">
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            <h5 class="category-name"><?= $cat['name'] ?></h5>
                            <p class="category-count">
                                <?= $movie->getTotalMovies($cat['slug']) ?> Film
                            </p>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Selected Category Movies -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>
                            <a href="categories.php" class="btn btn-outline-light me-3">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <?= $category_name ?> Filmleri
                        </h2>
                        <span class="badge bg-primary fs-6"><?= $total_movies ?> Film</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php if(!empty($movies)): ?>
                    <?php foreach($movies as $movie_item): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="movie-card">
                            <div class="movie-poster">
                                <img src="<?= $movie_item['poster_url'] ?>" alt="<?= $movie_item['title'] ?>">
                                <div class="movie-overlay">
                                    <div class="movie-actions">
                                        <a href="movie.php?id=<?= $movie_item['id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-play"></i>
                                        </a>
                                        <a href="movie.php?id=<?= $movie_item['id'] ?>" class="btn btn-outline-light btn-sm">
                                            <i class="fas fa-info"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="movie-info">
                                <h5 class="movie-title"><?= $movie_item['title'] ?></h5>
                                <div class="movie-meta">
                                    <span class="rating">
                                        <i class="fas fa-star text-warning"></i> <?= $movie_item['rating'] ?>
                                    </span>
                                    <span class="year"><?= $movie_item['year'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-film fa-4x text-muted mb-3"></i>
                            <h4>Bu kategoride henüz film bulunmuyor</h4>
                            <p class="text-muted">Yakında harika filmlerle karşınızda olacağız!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <nav aria-label="Movie pagination">
                <ul class="pagination justify-content-center">
                    <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?slug=<?= $selected_category ?>&page=<?= $page-1 ?>">Önceki</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?slug=<?= $selected_category ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?slug=<?= $selected_category ?>&page=<?= $page+1 ?>">Sonraki</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-film"></i> CinemaMax</h5>
                    <p>En iyi filmleri HD kalitesinde izleyin.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 CinemaMax. Tüm hakları saklıdır.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
